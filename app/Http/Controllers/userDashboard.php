<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use Illuminate\Notifications\Notifiable;

use App\Notifications\ServiceCharge;

use App\Garantor;
use App\SavingAcount;

use App\EduLoan;
use App\BusinessLoan;
use App\EmployeeLoan;

use DB;

use Auth;

class userDashboard extends Controller
{
    public function index() {
        $user = User::where('id', Auth::user()->id)->first();
        $saving = SavingAcount::where('user_id', $user->id)->where('approved', 1)->latest()->first();

        $edu_loans = DB::table('edu_loans')
            ->join('loan_installments', 'edu_loans.id', '=', 'loan_installments.loan_id')
            ->where('edu_loans.user_id', $user->id)
            ->where('loan_installments.approved', 1)
            ->select('edu_loans.amount as loan_amount', 'loan_installments.net_amount')
            ->orderBy('loan_installments.created_at', 'desc')
            ->get()
            ->toArray();

        $employee_loans = DB::table('employee_loans')
            ->join('loan_installments', 'employee_loans.id', '=', 'loan_installments.loan_id')
            ->where('employee_loans.user_id', $user->id)
            ->where('loan_installments.approved', 1)
            ->select('employee_loans.amount as loan_amount', 'loan_installments.net_amount')
            ->orderBy('loan_installments.created_at', 'desc')
            ->get()
            ->toArray();

        $business_loans = DB::table('business_loans')
            ->join('loan_installments', 'business_loans.id', '=', 'loan_installments.loan_id')
            ->where('business_loans.user_id', $user->id)
            ->select('business_loans.amount as loan_amount','loan_installments.net_amount')
            ->orderBy('loan_installments.created_at', 'desc')
            ->get()
            ->toArray();

        $loan = array_merge($edu_loans, $employee_loans, $business_loans);

        if($loan == null) {
            return view('user.dashboard.index', [
                'user' => $user,
                'saving' => $saving
            ]);
        }   else {
            return view('user.dashboard.index', [
                'user' => $user,
                'saving' => $saving,
                'loan' => $loan[0]
            ]);
        }
    }

    public function approvedLoans() {
        $edu_loans = DB::table('users')
            ->join('garantors', 'users.id', '=', 'garantors.user_id')
            ->where('garantors.g_approved', 0)
            ->where('garantors.loan_method', 'edu_loan')
            ->where('garantors.user_id', Auth::user()->id)
            ->get()
            ->toArray();

        $employee_loans = DB::table('users')
            ->join('garantors', 'users.id', '=', 'garantors.user_id')
            ->where('garantors.g_approved', 0)
            ->where('garantors.loan_method', 'employee_loan')
            ->where('garantors.user_id', Auth::user()->id)
            ->get()
            ->toArray();

        $business_loans = DB::table('users')
            ->join('garantors', 'users.id', '=', 'garantors.user_id')
            ->where('garantors.g_approved', 0)
            ->where('garantors.loan_method', 'business_loan')
            ->where('garantors.user_id', Auth::user()->id)
            ->get()
            ->toArray();

        $users = array_merge($edu_loans, $employee_loans, $business_loans);

        return view('user.dashboard.showGApproveLoan', [
            'users' => $users
        ]);
    }

    public function g_acceptB(Request $request) {
        $garantors = Garantor::where('loan_method', 'business_loan')->where('g_mobile_number', Auth::user()->mobile_number)->where('loan_id', $request->loan_id)->get();
        $loan = BusinessLoan::where('id', $garantors[0]->loan_id)->first();
        $garantor = Garantor::where('loan_method', 'business_loan')->where('g_mobile_number', Auth::user()->mobile_number)->where('loan_id', $request->loan_id)->first();
        $garantor->g_approved = 1;
        $garantor->save();

        $loan->g_approved = 1;
        $loan->save();
        return redirect('/dashboard')->with('status', 'loan accepted');
    }

    public function g_acceptEm(Request $request) {
        $loan = EmployeeLoan::where('token', $request->token)
            ->first();
        $garantors = Garantor::where('loan_method', 'employee_loan')->where('g_mobile_number', Auth::user()->mobile_number)->get();
        $row = count($garantors);

        foreach ($garantors as $key => $garantor) {
            if($loan->g_approved == 0) {
                if($row == 1) {

                    $garantor = Garantor::where('loan_method', 'employee_loan')->where('g_mobile_number', Auth::user()->mobile_number)->first();
                    dd($garantor);
                    $garantor->g_approved = 1;
                    $garantor->save();

                    $loan->g_approved = 1;
                    $loan->save();
                }   else {
                    dd();
                }
                return redirect('/dashboard')->with('status', 'loan accepted');
            }   else {
                return redirect('/admin')->with('status', 'Already Approved');
            }
        }
    }

    public function g_acceptEd(Request $request) {
        $loan = EduLoan::where('token', $request->token)
            ->first();
        $garantors = Garantor::where('loan_method', 'edu_loan')->where('g_mobile_number', Auth::user()->mobile_number)->get();
        $row = count($garantors);

        foreach ($garantors as $key => $garantor) {
            if($loan->g_approved == 0) {
                if($row == 1) {
                    $garantor = Garantor::where('loan_method', 'edu_loan')->where('g_mobile_number', Auth::user()->mobile_number)->first();
                    $garantor->g_approved = 1;
                    $garantor->save();

                    $loan->g_approved = 1;
                    $loan->save();
                }   else {
                    dd();
                }
                return redirect('/dashboard')->with('status', 'loan accepted');
            }   else {
                return redirect('/admin')->with('status', 'Already Approved');
            }
        }
    }

    public function showLoans(Request $request) {
        $edu_loans = EduLoan::where("user_id", $request->id)
            ->where('edu_loans.completed', 0)
            ->get()
            ->toArray();

        $employee_loans = EmployeeLoan::where('user_id', $request->id)
            ->where('employee_loans.completed', 0)
            ->get()
            ->toArray();

        $business_loans = BusinessLoan::where('user_id', $request->id)
            ->where('business_loans.completed', 0)
            ->get()
            ->toArray();

        $users = array_merge($edu_loans, $employee_loans, $business_loans);

        return view('user.dashboard.showLoans', [
            'users' => $users
        ]);
    }
}
