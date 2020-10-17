<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use App\BusinessLoan;

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
            'exp' => 'required',
            'capital' => 'required',
            'fee' => 'nullable',
            'g_name' => 'nullable',
            'g_account_no' => 'nullable',
            'g_approved' => 'nullable'
        ]);

        $loan = new BusinessLoan();
        $user = Auth::user();
        $fee = $request->amount * 2/100;

        if($user->savingAmount() > $fee) {
            if($request->g_account_no == '') {
                $loan->user_id = $request->id;
                $loan->business_name = $request->name;
                $loan->business_Address = $request->address;
                $loan->business_type = $request->category;
                $loan->contact_business = $request->contact_no;
                $loan->exp_business = $request->exp;
                $loan->amount = $request->amount;
                $loan->installments = 10;
                $loan->capital = $request->capital;
                $loan->fee = $fee;
                $loan->token = Str::random(5);
                $loan->perInstallmentAmount = (int)$request->amount / 10;
                $loan->save();

                return redirect('/')->with('status', 'Wait for Admin to approve');
            }   else {
                $users = User::get();

                foreach ($users as $user) {
                    if($user->mobile_number == $request->g_account_no && Auth::user()->mobile_number != $request->g_account_no) {
                        $g_account = $user->mobile_number;
                        $g_name = $user->name;
                        $loan->user_id = $request->id;
                        $loan->business_name = $request->name;
                        $loan->business_Address = $request->address;
                        $loan->business_type = $request->category;
                        $loan->contact_business = $request->contact_no;
                        $loan->exp_business = $request->exp;
                        $loan->amount = $request->amount;
                        $loan->installments = 10;
                        $loan->capital = $request->capital;
                        $loan->g_name = $g_name;
                        $loan->g_account_no = $g_account;
                        $loan->fee = $fee;
                        $loan->token = Str::random(5);
                        $loan->perInstallmentAmount = (int)$request->amount / 10;
                        $loan->save();
                        return redirect('/')->with('status', 'Wait for Garantor and Admin to approve');
                    }
                }
                return redirect('/')->with('status', 'Wrong Gaurantor Info');
            }
        }   else {
            return redirect('/')->with('status', 'You must have savings for loan');
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

        $loan = new EmployeeLoan();
        $user = Auth::user();
        $fee = $request->amount * 2/100;

        if($user->savingAmount() > $fee) {
            if($request->g_account_no == '') {
                $loan->user_id = $request->id;
                $loan->org_name = $request->name;
                $loan->exp = $request->exp;
                $loan->office_no = $request->contact_no;
                $loan->position = $request->position;
                $loan->salary = $request->salary;
                $loan->amount = $request->amount;
                $loan->installments = 10;
                $loan->fee = $fee;
                $loan->g_name = $g_name;
                $loan->g_account_no = $g_account;
                $loan->token = Str::random(5);
                $loan->perInstallmentAmount = (int)$request->amount / 10;
                $loan->save();

                return redirect('/')->with('status', 'Wait for Admin to approve');
            }   else {
                $users = User::get();

                foreach ($users as $user) {
                    if($user->mobile_number == $request->g_account_no && Auth::user()->mobile_number != $request->g_account_no) {
                        $g_account = $user->mobile_number;
                        $g_name = $user->name;
                        $loan->user_id = $request->id;
                        $loan->org_name = $request->name;
                        $loan->exp = $request->exp;
                        $loan->office_no = $request->contact_no;
                        $loan->position = $request->position;
                        $loan->salary = $request->salary;
                        $loan->amount = $request->amount;
                        $loan->installments = 10;
                        $loan->fee = $fee;
                        $loan->g_name = $g_name;
                        $loan->g_account_no = $g_account;
                        $loan->token = Str::random(5);
                        $loan->perInstallmentAmount = (int)$request->amount / 10;
                        $loan->save();

                        return redirect('/')->with('status', 'Wait for Garantor and Admin to approve');
                    }
                }
                return redirect('/')->with('status', 'Wrong Gaurantor Info');
            }
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

        $loan = new EduLoan();
        $user = Auth::user();
        $fee = $request->amount * 2/100;

        if($user->savingAmount() > $fee) {
            if($request->g_account_no == '') {
                $loan->user_id = $request->id;
                $loan->org_name = $request->name;
                $loan->org_address = $request->address;
                $loan->edu_no = $request->contact_no;
                $loan->level = $request->level;
                $loan->amount = $request->amount;
                $loan->installments = 10;
                $loan->fee = $fee;
                $loan->token = Str::random(5);
                $loan->perInstallmentAmount = (int)$request->amount / 10;
                $loan->save();

                return redirect('/')->with('status', 'Wait for Admin to approve');
            }   else {
                $users = User::get();

                foreach ($users as $user) {
                    if($user->mobile_number == $request->g_account_no && Auth::user()->mobile_number != $request->g_account_no) {
                        $g_account = $user->mobile_number;
                        $g_name = $user->name;
                        $loan->user_id = $request->id;
                        $loan->org_name = $request->name;
                        $loan->org_address = $request->address;
                        $loan->edu_no = $request->contact_no;
                        $loan->level = $request->level;
                        $loan->amount = $request->amount;
                        $loan->installments = 10;
                        $loan->fee = $fee;
                        $loan->g_name = $g_name;
                        $loan->g_account_no = $g_account;
                        $loan->token = Str::random(5);
                        $loan->perInstallmentAmount = (int)$request->amount / 10;
                        $loan->save();

                        return redirect('/')->with('status', 'Wait for Garantor and Admin to approve');
                    }
                }
                return redirect('/')->with('status', 'Wrong Gaurantor Info');
            }
        }   else {
            return redirect('/')->with('status', 'You must have savings for loan');
        }
    }
}
