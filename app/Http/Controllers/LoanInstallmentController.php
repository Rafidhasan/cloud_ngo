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

        $loan = LoanInstallment::where('loan_id' ,'=', $user[0]['id'])->first();

        $loans = LoanInstallment::where('loan_id' ,'=', $user[0]['id'])->get();

        $latest = LoanInstallment::where('loan_id' ,'=', $user[0]['id'])->latest()->first();

        $total_amount = 0;

        foreach($loans as $loan) {
            $total_amount += $loan->amount;
        }

        $date = Carbon::create($user[0]['created_at']);

        if($loan == '') {
            //For First month
            $staring_date = date('d-m-Y', strtotime($date));
            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1);
            $next_month = Carbon::now()->startOfMonth()->modify('+1 month')->format('F');
            $created_date = Carbon::create($user[0]['approved_date']);

            return view('user.dashboard.loanInstallment', [
                'user' => $user,
                'starting_date' => $staring_date,
                'ending' => $ending,
                'next_month' => $next_month,
                'next_month_date' => $next_month_date
            ]);
        }   else {
            if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount <= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                //For Second Month
                if($latest->net_amount == 0) {
                    return redirect()->with('status','Your loan is completed');
                }   else {
                    $staring_date = date('d-m-Y', strtotime($date));
                    $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                    $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                    $next_month = Carbon::now()->startOfMonth()->modify('+2 month')->format('F');
                    $created_date = Carbon::create($user[0]['approved_date']);

                    return view('user.dashboard.loanInstallment', [
                        'user' => $user,
                        'starting_date' => $staring_date,
                        'ending' => $ending,
                        'next_month' => $next_month,
                        'next_month_date' => $next_month_date
                    ]);
                }
            }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount <= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
                //For third month
                if($latest->net_amount == 0) {
                    return redirect()->with('status','Your loan is completed');
                }   else {
                    $staring_date = date('d-m-Y', strtotime($date));
                    $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                    $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3);
                    $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                    $created_date = Carbon::create($user[0]['approved_date']);

                    return view('user.dashboard.loanInstallment', [
                        'user' => $user,
                        'starting_date' => $staring_date,
                        'ending' => $ending,
                        'next_month' => $next_month,
                        'next_month_date' => $next_month_date
                    ]);
                }
            }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount <= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
                // forth month

                if($latest->net_amount == 0) {
                    return redirect()->with('status','Your loan is completed');
                }   else {
                    $staring_date = date('d-m-Y', strtotime($date));
                    $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                    $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4);
                    $next_month = Carbon::now()->startOfMonth()->modify('+4 month')->format('F');
                    $created_date = Carbon::create($user[0]['approved_date']);

                    return view('user.dashboard.loanInstallment', [
                        'user' => $user,
                        'starting_date' => $staring_date,
                        'ending' => $ending,
                        'next_month' => $next_month,
                        'next_month_date' => $next_month_date
                    ]);
                }
            }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount <= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
                //for fifth month
                if($latest->net_amount == 0) {
                    return redirect()->with('status','Your loan is completed');
                }   else {
                    $staring_date = date('d-m-Y', strtotime($date));
                    $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                    $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5);
                    $next_month = Carbon::now()->startOfMonth()->modify('+5month')->format('F');
                    $created_date = Carbon::create($user[0]['approved_date']);

                    return view('user.dashboard.loanInstallment', [
                        'user' => $user,
                        'starting_date' => $staring_date,
                        'ending' => $ending,
                        'next_month' => $next_month,
                        'next_month_date' => $next_month_date
                    ]);
                }
            }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount <= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
                //for sixth month
                if($latest->net_amount == 0) {
                    return redirect()->with('status','Your loan is completed');
                }   else {
                    $staring_date = date('d-m-Y', strtotime($date));
                    $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                    $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(6);
                    $next_month = Carbon::now()->startOfMonth()->modify('+6 month')->format('F');
                    $created_date = Carbon::create($user[0]['approved_date']);

                    return view('user.dashboard.loanInstallment', [
                        'user' => $user,
                        'starting_date' => $staring_date,
                        'ending' => $ending,
                        'next_month' => $next_month,
                        'next_month_date' => $next_month_date
                    ]);
                }
            }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(6)->lte(Carbon::now()->startOfMonth()) || ($total_amount <= $user[0]['perInstallmentAmount']*6 && $total_amount < $user[0]['perInstallmentAmount']*7)) {
                //for seventh month
                if($latest->net_amount == 0) {
                    return redirect()->with('status','Your loan is completed');
                }   else {
                    $staring_date = date('d-m-Y', strtotime($date));
                    $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                    $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(7);
                    $next_month = Carbon::now()->startOfMonth()->modify('+7 month')->format('F');
                    $created_date = Carbon::create($user[0]['approved_date']);

                    return view('user.dashboard.loanInstallment', [
                        'user' => $user,
                        'starting_date' => $staring_date,
                        'ending' => $ending,
                        'next_month' => $next_month,
                        'next_month_date' => $next_month_date
                    ]);
                }

            }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(7)->lte(Carbon::now()->startOfMonth()) || ($total_amount <= $user[0]['perInstallmentAmount']*7 && $total_amount < $user[0]['perInstallmentAmount']*8)) {
                //for eigth month
                if($latest->net_amount == 0) {
                    return redirect()->with('status','Your loan is completed');
                }   else {
                    $staring_date = date('d-m-Y', strtotime($date));
                    $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                    $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(8);
                    $next_month = Carbon::now()->startOfMonth()->modify('+8 month')->format('F');
                    $created_date = Carbon::create($user[0]['approved_date']);

                    return view('user.dashboard.loanInstallment', [
                        'user' => $user,
                        'starting_date' => $staring_date,
                        'ending' => $ending,
                        'next_month' => $next_month,
                        'next_month_date' => $next_month_date
                    ]);
                }
            }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(8)->lte(Carbon::now()->startOfMonth()) || ($total_amount <= $user[0]['perInstallmentAmount']*8 && $total_amount < $user[0]['perInstallmentAmount']*9)) {
                //for nine month
                if($latest->net_amount == 0) {
                    return redirect()->with('status','Your loan is completed');
                }   else {

                    $staring_date = date('d-m-Y', strtotime($date));
                    $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                    $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(9);
                    $next_month = Carbon::now()->startOfMonth()->modify('+9 month')->format('F');
                    $created_date = Carbon::create($user[0]['approved_date']);

                    return view('user.dashboard.loanInstallment', [
                        'user' => $user,
                        'starting_date' => $staring_date,
                        'ending' => $ending,
                        'next_month' => $next_month,
                        'next_month_date' => $next_month_date
                    ]);
                }
            }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(9)->lte(Carbon::now()->startOfMonth()) || ($total_amount <= $user[0]['perInstallmentAmount']*9 && $total_amount < $user[0]['perInstallmentAmount']*10)) {
                //for tenth month
                if($latest->net_amount == 0) {
                    return redirect()->with('status','Your loan is completed');
                }   else {
                    $staring_date = date('d-m-Y', strtotime($date));
                    $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                    $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(10);
                    $next_month = Carbon::now()->startOfMonth()->modify('+10 month')->format('F');
                    $created_date = Carbon::create($user[0]['approved_date']);

                    return view('user.dashboard.loanInstallment', [
                        'user' => $user,
                        'starting_date' => $staring_date,
                        'ending' => $ending,
                        'next_month' => $next_month,
                        'next_month_date' => $next_month_date
                    ]);
                }
        }
    }
}

    public function firstStore(Request $request) {
        $this->validate($request, [
            'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
            'tracking_number' => 'required',
            'amount' => 'required',
            'password' => 'required',
         ]);
        $next_month = $this_month->startOfMonth()->modify('+1 month');
        $this_month = Carbon::create($user[0]['approved_date'])->startOfMonth()->modify('+1 month');

        $user = User::where('mobile_number',$request->phone_number)->first();
        if (Hash::check($request->password, $user->password)) {
            $loan = new LoanInstallment();

            $prev_amount = LoanInstallment::select('net_amount')->where('loan_id', $request->id)->latest()->first();

            if(isset($prev_amount)) {
                $loan->net_amount = $prev_amount->net_amount - $request->amount;
            }   else {
                $loan->net_amount = $request->total - $request->amount;
            }

            $loan->loan_id = $request->id;
            $loan->token = $request->token;
            $loan->tracking_number = $request->tracking_number;
            $loan->mobile_number = $request->phone_number;
            $loan->amount = $request->amount;

            $loan->this_month = $request->month;
            $loan->next_month = $next_month;

            $loan->save();

            return redirect('/')->with('status', 'Payment Completed. Wait for Admin to Approve');
        }
    }

    public function showPrevLoanInstallments(Request $request) {
        $loans = LoanInstallment::where('loan_id', $request->id)->orderBy('created_at', 'desc')->get();

        return view('user.dashboard.prevLoanInstallment', [
            'loans' => $loans
        ]);
    }
}
