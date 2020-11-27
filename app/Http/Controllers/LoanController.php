<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use App\BusinessLoan;

use App\Garantor;

use App\Accounts;

use App\EmployeeLoan;

use App\EduLoan;

use App\User;

use Auth;

class LoanController extends Controller
{
    // Business Loan
    public function businessLoanIndex() {
        return view('user.businessLoan');
    }

    public function businessLoanCreate(Request $request) {
        $validatedData = $request->validate([
            'amount' => 'required',
            'name' => 'required',
            'address' => 'required',
            'category' => 'required',
            'contact_no' => 'required',
            'installments' => ['required', 'max:10'],
            'exp' => 'required',
            'capital' => 'required',
            'fee' => 'nullable',
            'g_name' => 'nullable',
            'g_account_no' => 'nullable',
            'g_approved' => 'nullable'
        ]);


        $garantor_numbers = $request->g_account_no;

        if($request->g_account_no[0] == null) {
            $loan = new BusinessLoan();

            $user = Auth::user();
            $fee = $request->amount * 2/100;
            $users = User::get();

            if($user->savingAmount() > $fee && $user->savingAmount() * 20 >= $request->amount) {
                foreach ($users as $user) {
                    if($request->installments < 11) {
                        $loan->user_id = $request->id;
                        $loan->business_name = $request->name;
                        $loan->business_Address = $request->address;
                        $loan->business_type = $request->category;
                        $loan->contact_business = $request->contact_no;
                        $loan->exp_business = $request->exp;
                        $loan->amount = $request->amount;
                        $loan->installments = $request->installments;
                        $loan->capital = $request->capital;
                        $loan->fee = $fee;
                        $loan->token = Str::random(5);
                        $loan->perInstallmentAmount = (int)$request->amount / (int)$request->installments;

                        $loan->save();

                        $garantor = new Garantor();
                        $garantor->loan_id = $loan->id;
                        $garantor->Loan_method = 'business_loan';

                        $garantor->save();

                        return redirect('/')->with('status', 'Wait for Garantor and Admin to approve. Your Loan Processing fee '. $fee .'TK is succesfully deducted from your savings');
                    }
                }
                return redirect('/')->with('status', 'Wrong Gaurantor Info');
            }   else {
                return redirect('/')->with('status', 'Wrong garantor information');
            }
        }   else {
            $loan = new BusinessLoan();

            $user = Auth::user();
            $fee = $request->amount * 2/100;
            $users = User::get();

            if($user->savingAmount() > $fee && $user->savingAmount() * 20 >= $request->amount) {
                foreach ($users as $user) {
                    foreach($garantor_numbers as $garantor_number) {
                        if($user->mobile_number == $garantor_number && Auth::user()->mobile_number != $garantor_number && $request->installments < 11) {
                            $g_account = $user->mobile_number;
                            $g_name = $user->name;
                            $loan->user_id = $request->id;
                            $loan->business_name = $request->name;
                            $loan->business_Address = $request->address;
                            $loan->business_type = $request->category;
                            $loan->contact_business = $request->contact_no;
                            $loan->exp_business = $request->exp;
                            $loan->amount = $request->amount;
                            $loan->installments = $request->installments;
                            $loan->capital = $request->capital;
                            $loan->fee = $fee;
                            $loan->token = Str::random(5);
                            $loan->perInstallmentAmount = (int)$request->amount / (int)$request->installments;

                            $loan->save();

                            $g_infos = array_combine($request->g_name, $request->g_account_no);

                            foreach($g_infos as $g_name => $g_number) {
                                $garantor = new Garantor();
                                $garantor->loan_id = $loan->id;
                                $garantor->Loan_method = 'business_loan';

                                $garantor->g_name = $g_name;
                                $garantor->g_mobile_number = $g_number;
                                $garantor->save();
                            }
                            return redirect('/')->with('status', 'Wait for Garantor and Admin to approve. Your Loan Processing fee '. $fee .'TK is succesfully deducted from your savings');
                        }
                    }
                }
                return redirect('/')->with('status', 'Wrong Gaurantor Info');
            }   else {
                return redirect('/')->with('status', 'Wrong garantor information');
            }
        }
    }

    // Emplyee Loan
    public function employeeLoanIndex() {
        return view('user.employeeLoan');
    }

    public function employeeLoanCreate(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required',
            'contact_no' => 'required',
            'exp' => 'required',
            'position' => 'required',
            'salary' => 'required',
            'amount' => 'required',
            'fee' => 'nullable',
            'g_name' => 'nullable',
            'g_account_no' => 'nullable',
        ]);
        $garantor_numbers = $request->g_account_no;

        $loan = new EmployeeLoan();

        $user = Auth::user();
        $fee = $request->amount * 2/100;
        $users = User::get();

        if($user->savingAmount() > $fee && $user->savingAmount() * 20 <= $request->amount) {
            foreach ($users as $user) {
                foreach($garantor_numbers as $garantor_number) {
                    if($user->mobile_number == $garantor_number && Auth::user()->mobile_number != $garantor_number && $user->installments < 11) {
                        $loan->user_id = $request->id;
                        $loan->org_name = $request->name;
                        $loan->exp = $request->exp;
                        $loan->office_no = $request->contact_no;
                        $loan->position = $request->position;
                        $loan->salary = $request->salary;
                        $loan->amount = $request->amount;
                        $loan->installments = $request->installments;
                        $loan->fee = $fee;
                        $loan->token = Str::random(5);
                        $loan->perInstallmentAmount = (int)$request->amount / (int)$request->installments;
                        $loan->save();

                        $g_infos = array_combine($request->g_name, $request->g_account_no);

                        foreach($g_infos as $g_name => $g_number) {
                            $garantor = new Garantor();
                            $garantor->loan_id = $loan->id;
                            $garantor->Loan_method = 'employee_loan';

                            $garantor->g_name = $g_name;
                            $garantor->g_mobile_number = $g_number;
                            $garantor->save();
                        }
                        return redirect('/')->with('status', 'Wait for Garantor and Admin to approve');
                    }
                }
            }
            return redirect('/')->with('status', 'Wrong Gaurantor Info');
        }   else {
            return redirect('/')->with('status', 'You must have savings for loan');
        }
    }

    public function educationLoanIndex() {
        return view('user.educationLoan');
    }

    public function educationLoanCreate(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required',
            'contact_no' => 'required',
            'address' => 'required',
            'level' => 'required',
            'amount' => 'required',
            'fee' => 'nullable',
            'g_name' => 'nullable',
            'g_account_no' => 'nullable',
        ]);

        $garantor_numbers = $request->g_account_no;

        $loan = new EduLoan();

        $user = Auth::user();
        $fee = $request->amount * 2/100;
        $users = User::get();

        if($user->savingAmount() > $fee && $user->savingAmount() * 20 <= $request->amount) {
            foreach ($users as $user) {
                foreach($garantor_numbers as $garantor_number) {
                    if($user->mobile_number == $garantor_number && Auth::user()->mobile_number != $garantor_number && $user->installments < 11) {
                        $loan->user_id = $request->id;
                        $loan->org_name = $request->name;
                        $loan->org_address = $request->address;
                        $loan->edu_no = $request->contact_no;
                        $loan->level = $request->level;
                        $loan->amount = $request->amount;
                        $loan->installments = $request->installments;
                        $loan->fee = $fee;
                        $loan->token = Str::random(5);
                        $loan->perInstallmentAmount = (int)$request->amount / (int)$request->installments;
                        $loan->save();
                        $g_infos = array_combine($request->g_name, $request->g_account_no);

                        foreach($g_infos as $g_name => $g_number) {
                            $garantor = new Garantor();
                            $garantor->loan_id = $loan->id;
                            $garantor->Loan_method = 'edu_loan';

                            $garantor->g_name = $g_name;
                            $garantor->g_mobile_number = $g_number;
                            $garantor->save();
                        }
                        return redirect('/')->with('status', 'Wait for Garantor and Admin to approve');
                    }
                }
            }
            return redirect('/')->with('status', 'Wrong Gaurantor Info');
        }   else {
            return redirect('/')->with('status', 'You must have savings for loan');
        }
    }
}
