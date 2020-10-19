<?php

namespace App\Http\Controllers;

use App\EduLoan;
use App\EmployeeLoan;
use App\BusinessLoan;

use App\LoanInstallment;

use Carbon\Carbon;

use App\User;

use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class LoanInstallmentController extends Controller
{
    public function eduLoanInstallmentIndex(Request $request) {
        $edu_loans = EduLoan::where("token", $request->token)
            ->where('approved', 1)
            ->get()
            ->toArray();

        $employee_loans = EmployeeLoan::where('token', $request->token)
            ->where('approved', 1)
            ->get()
            ->toArray();

        $business_loans = BusinessLoan::where('token', $request->token)
            ->where('approved', 1)
            ->get()
            ->toArray();

        $user = array_merge($edu_loans, $employee_loans, $business_loans);

        $date = Carbon::create($user[0]['created_at']);
        $staring_date = date('d-m-Y', strtotime($date));
        $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

        return view('user.dashboard.loanInstallment', [
            'user' => $user,
            'starting_date' => $staring_date,
            'ending' => $ending
        ]);
    }

    public function employeeloanInstallmentIndex(Request $request) {
        $edu_loans = EduLoan::where("token", $request->token)
            ->get()
            ->toArray();

        $employee_loans = EmployeeLoan::where('token', $request->token)
            ->get()
            ->toArray();

        $business_loans = BusinessLoan::where('token', $request->token)
            ->get()
            ->toArray();

        $user = array_merge($edu_loans, $employee_loans, $business_loans);



        return view('user.dashboard.loanInstallment', [
            'user' => $user,
            'starting_date' => $staring_date,
            'ending_date' => $ending_date
        ]);
    }

    public function businessloanInstallmentIndex(Request $request) {
        $edu_loans = EduLoan::where("token", $request->token)
            ->where('approved', 1)
            ->get()
            ->toArray();

        $employee_loans = EmployeeLoan::where('token', $request->token)
            ->where('approved', 1)
            ->get()
            ->toArray();

        $business_loans = BusinessLoan::where('token', $request->token)
            ->where('approved', 1)
            ->get()
            ->toArray();

        $user = array_merge($edu_loans, $employee_loans, $business_loans);

        $date = Carbon::create($user[0]['created_at']);

        if(Carbon::create($user[0]['created_at'])->startOfMonth()->addMonth(9)->lte(Carbon::now()->startOfMonth())) {
            dd('9 month');
        }   elseif(Carbon::create($user[0]['created_at'])->startOfMonth()->addMonth(8)->lte(Carbon::now()->startOfMonth())) {
            dd('8 month');
        }   elseif(Carbon::create($user[0]['created_at'])->startOfMonth()->addMonth(7)->lte(Carbon::now()->startOfMonth())) {
            dd('7 month');
        }   elseif(Carbon::create($user[0]['created_at'])->startOfMonth()->addMonth(6)->lte(Carbon::now()->startOfMonth())) {
            dd('6 month');
        }   elseif(Carbon::create($user[0]['created_at'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth())) {
            dd('5 month');
        }   elseif(Carbon::create($user[0]['created_at'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth())) {
            dd('4 month');
        }   elseif(Carbon::create($user[0]['created_at'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth())) {
            dd('3 month');
        }   elseif(Carbon::create($user[0]['created_at'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth())) {
            //For three month


        }   else if(Carbon::create($user[0]['created_at'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth())) {
            //For Second Month

            $staring_date = date('d-m-Y', strtotime($date));
            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

            $next_month_date = Carbon::create($user[0]['created_at'])->startOfMonth()->addMonth(2);
            $next_month = Carbon::now()->startOfMonth()->modify('+2 month')->format('F');
            $created_date = Carbon::create($user[0]['created_at']);

            return view('user.dashboard.loanInstallment', [
                'user' => $user,
                'starting_date' => $staring_date,
                'ending' => $ending,
                'next_month' => $next_month,
                'next_month_date' => $next_month_date
            ]);
        }   else {
            //For First month

            $staring_date = date('d-m-Y', strtotime($date));
            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

            $next_month_date = Carbon::create($user[0]['created_at'])->startOfMonth()->addMonth(1);
            $next_month = Carbon::now()->startOfMonth()->modify('+1 month')->format('F');
            $created_date = Carbon::create($user[0]['created_at']);

            return view('user.dashboard.loanInstallment', [
                'user' => $user,
                'starting_date' => $staring_date,
                'ending' => $ending,
                'next_month' => $next_month,
                'next_month_date' => $next_month_date
            ]);
        }
    }

    public function store(Request $request) {
        $this->validate($request, [
            'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
            'tracking_number' => 'required',
            'amount' => 'required',
            'password' => 'required',
         ]);
        $next_month = Carbon::now()->startOfMonth()->modify('+2 month');
        $this_month = Carbon::now()->startOfMonth()->modify('+1 month');

        $user = User::where('mobile_number',$request->phone_number)->first();
        if (Hash::check($request->password, $user->password)) {
            $loan = new LoanInstallment();

            $loan->user_id = $request->id;
            $loan->token = $request->token;
            $loan->tracking_number = $request->tracking_number;
            $loan->mobile_number = $request->phone_number;
            $loan->amount = $request->amount;
            $loan->net_amount = $request->net_amount - $request->amount;
            $loan->this_month = $request->month;
            $loan->next_month = $next_month;

            $loan->save();

            return redirect('/');
        }
    }
}
