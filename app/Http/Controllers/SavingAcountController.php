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
                $savings->amount = $request->amount;
                $row = count(SavingAcount::select('amount')->where('user_id', $user->id)->get());

                if($row == 0) {
                    $savings->total = $request->amount;
                }   else {
                    $prev_amounts = SavingAcount::select('amount')->where('user_id', $user->id)->get();

                    foreach($prev_amounts as $amount) {
                        $total = $request->amount + $amount->amount;
                    }

                    $savings->total = $total;
                }

                $savings->save();

                return redirect('/')->with('status', 'You Saving is added');
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
                $row = count(SavingAcount::select('amount')->where('user_id', $user->id)->get());

                if($row == 0) {
                    $savings->total = $request->amount;
                }   else {
                    $prev_amounts = SavingAcount::select('amount')->where('user_id', $user->id)->get();

                    foreach($prev_amounts as $amount) {
                        $total = $request->amount + $amount->amount;
                    }

                    $savings->total = $total;
                }

                $savings->save();

                return redirect('/')->with('status', 'You Saving is added');
            }   else {
                return redirect('/')->with('status', 'Your Password is Incorrect');
            }
        }
    }
}
