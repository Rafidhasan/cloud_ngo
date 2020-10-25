<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use Illuminate\Notifications\Notifiable;

use App\Notifications\ServiceCharge;

use App\Garantor;

use App\EduLoan;
use App\BusinessLoan;
use App\EmployeeLoan;

use DB;

use Auth;

class userDashboard extends Controller
{
    public function index() {
        $user = User::where('id', Auth::user()->id)->first();

        return view('user.dashboard.index');
    }

    public function approvedLoans() {
        $edu_loans = DB::table('users')
            ->join('edu_loans', 'users.id', '=', 'edu_loans.user_id')
            ->join('garantors', 'edu_loans.id', '=', 'garantors.loan_id')
            ->where('edu_loans.approved', '=', 0)
            ->where('garantors.g_mobile_number', '=', Auth::user()->mobile_number)
            ->where('garantors.loan_method', 'edu_loan')
            ->get()
            ->toArray();

        $employee_loans = DB::table('users')
            ->join('employee_loans', 'users.id', '=', 'employee_loans.user_id')
            ->join('garantors', 'employee_loans.id', '=', 'garantors.loan_id')
            ->where('employee_loans.approved', '=', 0)
            ->where('garantors.g_mobile_number', '=', Auth::user()->mobile_number)
            ->where('garantors.loan_method', 'employee_loan')
            ->get()
            ->toArray();

        $business_loans = DB::table('users')
            ->join('business_loans', 'users.id', '=', 'business_loans.user_id')
            ->join('garantors', 'business_loans.id', '=', 'garantors.loan_id')
            ->where('business_loans.approved', '=', 0)
            ->where('garantors.g_mobile_number', '=', Auth::user()->mobile_number)
            ->where('garantors.loan_method', 'business_loan')
            ->get()
            ->toArray();

        $users = array_merge($edu_loans, $employee_loans, $business_loans);

        return view('user.dashboard.showGApproveLoan', [
            'users' => $users
        ]);
    }

    public function g_acceptB(Request $request) {
        $garantors = Garantor::where('loan_method', 'business_loan')->where('g_mobile_number', Auth::user()->mobile_number)->get();

        foreach ($garantors as $key => $garantor) {
            if($garantor->g_approved == 0) {
                $garantor->g_approved = 1;
                $garantor->save();

                return redirect('/dashboard')->with('status', 'loan accepted');
            }   else {
                return redirect('/admin')->with('status', 'Already Approved');
            }
        }
    }

    public function g_acceptEm(Request $request) {
        $loan = EmployeeLoan::where('token', $request->token)
            ->first();

        if(isset($loan->g_account_no)) {
            if($loan->g_approved == 0) {
                $loan->g_approved = 1;
                $loan->save();

                return redirect('/dashboard')->with('status', 'loan accepted');
            }   else {
                return redirect('/admin')->with('status', 'Already Approved');
            }
        }   else {
            return redirect('/dashboard')->with('status', 'loan meaningless');
        }
    }

    public function g_acceptEd(Request $request) {
        $loan = EduLoan::where('token', $request->token)
            ->first();

        if(isset($loan->g_account_no)) {
            if($loan->g_approved == 0) {
                $loan->g_approved = 1;
                $loan->save();

                return redirect('/dashboard')->with('status', 'loan accepted');
            }   else {
                return redirect('/admin')->with('status', 'Already Approved');
            }
        }   else {
            return redirect('/dashboard')->with('status', 'loan meaningless');
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
