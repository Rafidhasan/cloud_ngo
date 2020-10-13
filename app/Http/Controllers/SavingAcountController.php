<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

use App\User;

use Auth;

use App\SavingAcount;

class SavingAcountController extends Controller
{
    public function store(Request $request) {
        if($request->phone_number == '') {
            $user = User::find($request->id);
            if (Hash::check($request->password, $user->password)) {
                $savings = new SavingAcount();

                $savings->user_id = $user->id;
                $savings->mobile_number = $user->mobile_number;
                $savings->tracking_number = $request->tracking_number;
                $savings->method = $request->method;
                $savings->amount = $request->amount;
                $row = count(SavingAcount::select('amount')->where('user_id', $user->id)->get());

                if($row == 0) {
                    $savings->total = $request->amount;
                }   else {
                    $prev_amount = SavingAcount::select('total')->where('user_id', $user->id)->latest()->first();
                    $savings->total = $request->amount + $prev_amount->total;
                }

                $savings->save();

                return redirect('/')->with('status', 'You Saving is added. Wait for authority to approve');
            }   else {
                return redirect('/')->with('status', 'Your Password is Incorrect');
            }
        }   else {
            $user = User::where('mobile_number',$request->phone_number)->first();

            if (Hash::check($request->password, $user->password)) {
                $savings = new SavingAcount();

                $total = 0;

                $savings->user_id = $user->id;
                $savings->mobile_number = $user->mobile_number;
                $savings->tracking_number = $request->tracking_number;
                $savings->amount = $request->amount;
                $savings->method = $request->method;
                $row = count(SavingAcount::select('amount')->where('user_id', $user->id)->get());

                if($row == 0) {
                    $savings->total = $request->amount;
                }   else {
                    $prev_amount = SavingAcount::select('total')->where('user_id', $user->id)->latest()->first();
                    $savings->total = $request->amount + $prev_amount->total;
                }

                $savings->save();

                return redirect('/')->with('status', 'You Saving is added. Wait for Authority to approve');
            }   else {
                return redirect('/')->with('status', 'Your Password is Incorrect');
            }
        }
    }
}
