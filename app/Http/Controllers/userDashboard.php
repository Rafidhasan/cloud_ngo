<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use Illuminate\Notifications\Notifiable;

use App\Notifications\ServiceCharge;

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
            ->where('edu_loans.approved', '=', 0)
            ->where('edu_loans.g_approved', '=', 0)
            ->where('edu_loans.g_account_no', '=', Auth::user()->mobile_number)
            ->get()
            ->toArray();

        $employee_loans = DB::table('users')
            ->join('employee_loans', 'users.id', '=', 'employee_loans.user_id')
            ->where('employee_loans.approved', '=', 0)
            ->where('employee_loans.g_approved', '=', 0)
            ->where('employee_loans.g_account_no', '=', Auth::user()->mobile_number)
            ->get()
            ->toArray();

        $business_loans = DB::table('users')
            ->join('business_loans', 'users.id', '=', 'business_loans.user_id')
            ->where('business_loans.approved', '=', 0)
            ->where('business_loans.g_approved', '=', 0)
            ->where('business_loans.g_account_no', '=', Auth::user()->mobile_number)
            ->get()
            ->toArray();

        $users = array_merge($edu_loans, $employee_loans, $business_loans);

        return view('user.dashboard.showGApproveLoan', [
            'users' => $users
        ]);
    }

    public function g_acceptB(Request $request) {
        $loan = BusinessLoan::where('token', $request->token)
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
}
