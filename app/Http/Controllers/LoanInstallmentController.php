<?php

namespace App\Http\Controllers;

use App\EduLoan;
use App\EmployeeLoan;
use App\BusinessLoan;
use App\SavingAcount;

use App\LoanInstallment;

use Carbon\Carbon;

use App\User;

use DB;

use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class LoanInstallmentController extends Controller
{
    public function show() {
        $edu_loans = DB::table('users')
            ->join('edu_loans', 'users.id', '=', 'edu_loans.user_id')
            ->where('edu_loans.approved', '=', 1)
            ->where('edu_loans.completed', '=', 0)
            ->get()
            ->toArray();

        $employee_loans = DB::table('users')
            ->join('employee_loans', 'users.id', '=', 'employee_loans.user_id')
            ->where('employee_loans.approved', '=', 1)
            ->where('employee_loans.completed', '=', 0)
            ->get()
            ->toArray();

        $business_loans = DB::table('users')
            ->join('business_loans', 'users.id', '=', 'business_loans.user_id')
            ->where('business_loans.approved', '=', 1)
            ->where('business_loans.completed', '=', 0)
            ->get()
            ->toArray();

        $users = array_merge($edu_loans, $employee_loans, $business_loans);

        return view('admin.loanInstallments', [
            'users' => $users
        ]);
    }

    public function showSingleLoanDetails(Request $request) {
        $edu_loans = DB::table('edu_loans')
            ->join('loan_installments', 'edu_loans.id', '=', 'loan_installments.loan_id')
            ->select('edu_loans.amount as total', 'edu_loans.*', 'loan_installments.*')
            ->where('loan_installments.token', $request->token)
            ->where('loan_installments.approved', '=', 0)
            ->where('edu_loans.approved', '=', 1)
            ->get()
            ->toArray();

        $employee_loans = DB::table('employee_loans')
            ->join('loan_installments', 'employee_loans.id', '=', 'loan_installments.loan_id')
            ->select('employee_loans.amount as total', 'employee_loans.*', 'loan_installments.*')
            ->where('loan_installments.token', $request->token)
            ->where('loan_installments.approved', '=', 0)
            ->where('employee_loans.approved', '=', 1)
            ->get()
            ->toArray();

        $business_loans = DB::table('business_loans')
            ->join('loan_installments', 'business_loans.id', '=', 'loan_installments.loan_id')
            ->select('business_loans.amount as total', 'business_loans.*', 'loan_installments.*')
            ->where('loan_installments.approved', '=', 0)
            ->where('loan_installments.token', $request->token)
            ->get()
            ->toArray();

        $users = array_merge($edu_loans, $employee_loans, $business_loans);

        if($users == []) {
            return view('admin.showSingleloanInstallments', [
                'users' => "",
            ]);
        }   else {
            $date = Carbon::create($users[0]->created_at);

            $ending = date('d-m-Y', strtotime($date->addMonth($users[0]->installments)));

            if($users == []) {
                dd('ami ekhane');
            }

            return view('admin.showSingleloanInstallments', [
                'users' => $users,
                'ending' => $ending
            ]);
        }
    }

    public function acceptSingleLoanInstallment(Request $request) {
        $edu_loans = DB::table('edu_loans')
            ->join('users', 'edu_loans.user_id', '=', 'users.id')
            ->join('loan_installments', 'edu_loans.id', '=', 'loan_installments.loan_id')
            ->select('users.id as userId', 'edu_loans.amount as total', 'edu_loans.*', 'loan_installments.*')
            ->where('loan_installments.tracking_number', $request->tracking_number)
            ->get()
            ->toArray();

        $employee_loans = DB::table('employee_loans')
            ->join('users', 'employee_loans.user_id', '=', 'users.id')
            ->join('loan_installments', 'employee_loans.id', '=', 'loan_installments.loan_id')
            ->select('users.id as userId', 'employee_loans.amount as total', 'employee_loans.*', 'loan_installments.*')
            ->where('loan_installments.tracking_number', $request->tracking_number)
            ->get()
            ->toArray();

        $business_loans = DB::table('business_loans')
            ->join('users', 'business_loans.user_id', '=', 'users.id')
            ->join('loan_installments', 'business_loans.id', '=', 'loan_installments.loan_id')
            ->select('users.id as userId', 'business_loans.amount as total', 'business_loans.*', 'loan_installments.*')
            ->where('loan_installments.tracking_number', $request->tracking_number)
            ->get()
            ->toArray();

        $user = array_merge($edu_loans, $employee_loans, $business_loans);

        if(isset($user[0]->business_name)) {
            $loan = LoanInstallment::where('tracking_number', $request->tracking_number)->first();
            $loan->approved = 1;
            $loan->save();
            if($loan->net_amount == 0) {
                $business_loan = Businessloan::where('token', $request->token)->first();

                $business_loan->completed = 1;
                $business_loan->save();

                return redirect('/admin')->with('status', 'Full Loan Installment is approved and Completed');

            }   else if($loan->net_amount <= 0) {
                $saving = SavingAcount::where('user_id', $user[0]->userId)->latest()->first();
                $saving->total += (-$loan->net_amount);
                $saving->save();

                $business_loan = Businessloan::where('token', $request->token)->first();

                $loan->net_amount = 0;
                $loan->save();

                $business_loan->completed = 1;
                $business_loan->save();

                return redirect('/admin')->with('status', 'Full Loan Installment is approved and Completed');
            }   else {
                return redirect('/admin')->with('status', 'Loan Installment is approved');
            }
        }   else if(isset($user[0]->org_name)) {
            $loan = LoanInstallment::where('tracking_number', $request->tracking_number)->first();
            $loan->approved = 1;
            $loan->save();
            if($loan->net_amount == 0) {
                $meployee_loan = EmployeeLoan::where('token', $request->token)->first();
                $meployee_loan->completed = 1;
                $meployee_loan->save();

                return redirect('/admin')->with('status', 'Full Loan Installment is approved and Completed');

            }   else if($loan->net_amount <= 0) {
                $saving = SavingAcount::where('user_id', $user[0]->userId)->latest()->first();
                $saving->total -= $loan->net_amount;

                $saving->save();

                $employee_loan = EmployeeLoan::where('token', $request->token)->first();

                $loan->net_amount = 0;
                $loan->save();

                $employee_loan->completed = 1;
                $employee_loan->save();

                return redirect('/admin')->with('status', 'Full Loan Installment is approved and Completed');
            }   else {
                return redirect('/admin')->with('status', 'Loan Installment is approved');
            }
        }   else {
            $loan = LoanInstallment::where('tracking_number', $request->tracking_number)->first();
            $loan->approved = 1;
            $loan->save();
            if($loan->net_amount == 0) {
                $edu_loan = EduLoan::where('token', $request->token)->first();

                $edu_loan->completed = 1;
                $edu_loan->save();

                return redirect('/admin')->with('status', 'Full Loan Installment is approved and Completed');

            }   else if($loan->net_amount <= 0) {
                $saving = SavingAcount::where('user_id', $user[0]->userId)->latest()->first();
                $saving->total -= $loan->net_amount;

                $saving->save();

                $edu_loan = EduLoan::where('token', $request->token)->first();

                $loan->net_amount = 0;
                $loan->save();

                $edu_loan->completed = 1;
                $edu_loan->save();

                return redirect('/admin')->with('status', 'Full Loan Installment is approved and Completed');
            }   else {
                return redirect('/admin')->with('status', 'Loan Installment is approved');
            }
        }
    }
    public function eduloanInstallmentIndex(Request $request) {
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

        $date = Carbon::create($user[0]['approved_at']);

        $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

        foreach($loans as $loan) {
            $total_amount += $loan->amount;
        }

        $installment = $user[0]['installments'];


        //For two installments

        if($installment == 2) {
            $date = Carbon::create($user[0]['created_at']);
            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));
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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']) || ($total_amount <= $user[0]['perInstallmentAmount'])) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
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
                        }   else {
                            dd('logic');
                        }
                    }
                }
            }
        }   else if($installment == 3) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 4) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 5) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 6) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
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
                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 7) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(6)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*6 && $total_amount < $user[0]['perInstallmentAmount']*7)) {
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

                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 8) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(6)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*6 && $total_amount < $user[0]['perInstallmentAmount']*7)) {
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

                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(7)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*7 && $total_amount < $user[0]['perInstallmentAmount']*8)) {
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
                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 9) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(6)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*6 && $total_amount < $user[0]['perInstallmentAmount']*7)) {
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

                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(7)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*7 && $total_amount < $user[0]['perInstallmentAmount']*8)) {
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
                }    else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(8)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*8 && $total_amount < $user[0]['perInstallmentAmount']*9)) {
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
                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 10) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount = $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }    else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(8)->lte(Carbon::now()->startOfMonth()) || ($total_amount <= $user[0]['perInstallmentAmount']*8 && $total_amount < $user[0]['perInstallmentAmount']*9)) {
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
                }  else {
                    dd('bad logic');
                }
            }
        }
        dd("mane ki");
    }

    public function employeeloanInstallmentIndex(Request $request) {
        $edu_loans = EduLoan::where("user_id", $request->id)
            ->where('approved', 1)
            ->get()
            ->toArray();

        $employee_loans = EmployeeLoan::where('user_id', $request->id)
            ->where('approved', 1)
            ->get()
            ->toArray();

        $business_loans = BusinessLoan::where('user_id', $request->id)
            ->where('approved', 1)
            ->get()
            ->toArray();

        $user = array_merge($edu_loans, $employee_loans, $business_loans);

        $loan = LoanInstallment::where('loan_id' ,'=', $user[0]['id'])->first();

        $loans = LoanInstallment::where('loan_id' ,'=', $user[0]['id'])->get();

        $latest = LoanInstallment::where('loan_id' ,'=', $user[0]['id'])->latest()->first();

        $total_amount = 0;

        $date = Carbon::create($user[0]['created_at']);

        $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

        foreach($loans as $loan) {
            $total_amount += $loan->amount;
        }

        $installment = $user[0]['installments'];

        //For two installments

        if($installment == 2) {
            $date = Carbon::create($user[0]['created_at']);
            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));
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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']) || ($total_amount <= $user[0]['perInstallmentAmount'])) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
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
                        }   else {
                            dd('logic');
                        }
                    }
                }
            }
        }   else if($installment == 3) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 4) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 5) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 6) {
            $date = Carbon::create($user[0]['created_at']);
            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
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
                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 7) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(6)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*6 && $total_amount < $user[0]['perInstallmentAmount']*7)) {
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

                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 8) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(6)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*6 && $total_amount < $user[0]['perInstallmentAmount']*7)) {
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

                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(7)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*7 && $total_amount < $user[0]['perInstallmentAmount']*8)) {
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
                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 9) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+2 month')->format('F');
                            dd($next_month);
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(6)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*6 && $total_amount < $user[0]['perInstallmentAmount']*7)) {
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

                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(7)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*7 && $total_amount < $user[0]['perInstallmentAmount']*8)) {
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
                }    else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(8)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*8 && $total_amount < $user[0]['perInstallmentAmount']*9)) {
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
                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 10) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount = $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }    else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(8)->lte(Carbon::now()->startOfMonth()) || ($total_amount <= $user[0]['perInstallmentAmount']*8 && $total_amount < $user[0]['perInstallmentAmount']*9)) {
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
                }  else {
                    dd('bad logic');
                }
            }
        }
        dd("mane ki");
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

        $date = Carbon::create($user[0]['created_at']);

        $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

        foreach($loans as $loan) {
            $total_amount += $loan->amount;
        }

        $installment = $user[0]['installments'];

        $temp = Carbon::now()->startOfMonth();

        //For two installments

        if($installment == 2) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

            if(($total_amount < $user[0]['perInstallmentAmount']) || $temp->startOfMonth()->lte(Carbon::now()->startOfMonth())) {
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
                if($temp->lte(Carbon::now()->startOfMonth()->addMonth(1)) || ($total_amount >= $user[0]['perInstallmentAmount'])) {
                    //For Second Month
                    if($loan == '') {
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
                    }   else {
                        if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                        }   else {
                            if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending) || ($total_amount >= $user[0]['perInstallmentAmount'])) {
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
                            }   else {
                                dd('logic');
                        }
                    }
                }
                }
            }
        }   else if($installment == 3) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

            if(($total_amount < $user[0]['perInstallmentAmount']) || $temp->startOfMonth()->lte(Carbon::now()->startOfMonth())) {
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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending) || ($total_amount >= $user[0]['perInstallmentAmount'])) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 4) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

            if(($total_amount < $user[0]['perInstallmentAmount']) || $temp->startOfMonth()->lte(Carbon::now()->startOfMonth())) {
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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 5) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

            if(($total_amount < $user[0]['perInstallmentAmount']) || $temp->startOfMonth()->lte(Carbon::now()->startOfMonth())) {
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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 6) {

            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

            if(($total_amount < $user[0]['perInstallmentAmount']) || $temp->startOfMonth()->lte(Carbon::now()->startOfMonth())) {
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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
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
                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 7) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

            if(($total_amount < $user[0]['perInstallmentAmount']) || $temp->startOfMonth()->lte(Carbon::now()->startOfMonth())) {
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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                            dd('logic');
                        }   else {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
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
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(6)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*6 && $total_amount < $user[0]['perInstallmentAmount']*7)) {
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

                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 8) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

            if(($total_amount < $user[0]['perInstallmentAmount']) || $temp->startOfMonth()->lte(Carbon::now()->startOfMonth())) {
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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(6)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*6 && $total_amount < $user[0]['perInstallmentAmount']*7)) {
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

                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(7)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*7 && $total_amount < $user[0]['perInstallmentAmount']*8)) {
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
                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 9) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

            if(($total_amount < $user[0]['perInstallmentAmount']) || $temp->startOfMonth()->lte(Carbon::now()->startOfMonth())) {
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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(4)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*4 && $total_amount < $user[0]['perInstallmentAmount']*5)){
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(5)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*5 && $total_amount < $user[0]['perInstallmentAmount']*6)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(6)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*6 && $total_amount < $user[0]['perInstallmentAmount']*7)) {
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

                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(7)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*7 && $total_amount < $user[0]['perInstallmentAmount']*8)) {
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
                }    else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(8)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*8 && $total_amount < $user[0]['perInstallmentAmount']*9)) {
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
                }  else {
                    dd('bad logic');
                }
            }
        }   else if($installment == 10) {
            $date = Carbon::create($user[0]['created_at']);

            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

            if(($total_amount < $user[0]['perInstallmentAmount']) || $temp->startOfMonth()->lte(Carbon::now()->startOfMonth())) {
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
                if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                    //For Second Month
                    if($latest->net_amount == 0) {
                        return redirect()->with('status','Your loan is completed');
                    }   else {
                        if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(1)->lte($ending) || ($total_amount >= $user[0]['perInstallmentAmount'] && $total_amount < $user[0]['perInstallmentAmount']*2)) {
                            $staring_date = date('d-m-Y', strtotime($date));
                            $ending = date('d-m-Y', strtotime($date->addMonth($user[0]['installments'])));

                            $next_month_date = Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2);
                            $next_month = Carbon::now()->startOfMonth()->modify('+3 month')->format('F');
                            $created_date = Carbon::create($user[0]['approved_date']);

                            return view('user.dashboard.loanInstallment', [
                                'user' => $user,
                                'starting_date' => $staring_date,
                                'ending' => $ending,
                                'next_month' => $next_month,
                                'next_month_date' => $next_month_date
                            ]);
                        }   else {
                            dd('logic');
                        }
                    }
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(2)->lte(Carbon::now()->startOfMonth()) || ($total_amount >= $user[0]['perInstallmentAmount']*2 && $total_amount < $user[0]['perInstallmentAmount']*3)) {
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
                }   else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(3)->lte(Carbon::now()->startOfMonth()) || ($total_amount = $user[0]['perInstallmentAmount']*3 && $total_amount < $user[0]['perInstallmentAmount']*4)) {
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
                }    else if(Carbon::create($user[0]['approved_date'])->startOfMonth()->addMonth(8)->lte(Carbon::now()->startOfMonth()) || ($total_amount <= $user[0]['perInstallmentAmount']*8 && $total_amount < $user[0]['perInstallmentAmount']*9)) {
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
                }  else {
                    dd('bad logic');
                }
            }
        }
        dd("mane ki");
    }

    public function firstStore(Request $request) {
        $this->validate($request, [
            'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
            'tracking_number' => 'required',
            'amount' => 'required',
            'password' => 'required',
         ]);

        $edu_loans = EduLoan::where("token", $request->token)
            ->get()
            ->toArray();

        $employee_loans = EmployeeLoan::where('token', $request->token)
            ->get()
            ->toArray();

        $business_loans = BusinessLoan::where('token', $request->token)
            ->get()
            ->toArray();

        $users = array_merge($edu_loans, $employee_loans, $business_loans);

        $this_month = Carbon::create($users[0]['approved_date'])->startOfMonth()->modify('+1 month');
        $next_month = $this_month->startOfMonth()->modify('+1 month');

        $user = User::where('mobile_number',$request->phone_number)->first();
        if($user == "") {
            return redirect("/")->with('status', 'Your phone number is incorrect');
        }   else {
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

                if($loan->net_amount <= 0) {
                    if(isset($users[0]['business_name'])) {
                        $business_loan = BusinessLoan::where('token', $request->token)->first();

                        $business_loan->paid = 1;
                        $business_loan->save();

                        return redirect('/')->with('status', 'Congrates! Your loan installment is completed. After Admin Aproval you will get a confirmation message');
                    }   else if(isset($users[0]['office_no'])) {
                        $employee_loan = EmployeeLoan::where('token', $request->token)->first();

                        $employee_loan->paid = 1;
                        $employee_loan->save();

                        return redirect('/')->with('status', 'Congrates! Your loan installment is completed. After Admin Aproval you will get a confirmation message');
                    }   else {
                        $edu_loan = EduLoan::where('token', $request->token)->first();

                        $edu_loan->paid = 1;
                        $edu_loan->save();

                        return redirect('/')->with('status', 'Congrates! Your loan installment is completed. After Admin Aproval you will get a confirmation message');
                    }
                }   else {
                    return redirect('/')->with('status', 'Loan Installment for '.$request->month. ' is successful');
                }
            }   else {
                return redirect('/')->with('status', 'Your Password is Incorrect');
            }
        }
    }

    public function showPrevLoanInstallments(Request $request) {
        $loans = LoanInstallment::where('loan_id', $request->id)->orderBy('created_at', 'desc')->get();

        return view('user.dashboard.prevLoanInstallment', [
            'loans' => $loans
        ]);
    }
}
