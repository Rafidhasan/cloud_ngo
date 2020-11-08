<?php

namespace App\Http\Controllers;

use App\User;

use App\Withdraw;

use App\SavingAcount;

use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index() {
        return view('user.withdraw');
    }

    public function create(Request $request) {
        $user = User::where('id', $request->id)->first();

        if (Hash::check($request->password, $user->password)) {
            $withdraw = new Withdraw();

            $digits = 5;

            $withdraw->user_id = $user->id;
            $withdraw->number = $request->number;
            $withdraw->method = $request->method;
            $withdraw->amount = $request->amount;
            $withdraw->serial = rand(pow(10, $digits-1), pow(10, $digits)-1);

            $row = count(Withdraw::select('amount')->where('user_id', $user->id)->get());

            if($row == 0) {
                $withdraw->total = $request->amount;
            }   else {
                $prev_amount = Withdraw::select('total')->where('user_id', $user->id)->latest()->first();
                $withdraw->total = $request->amount + $prev_amount->total;
            }

            $withdraw->save();

            return redirect('/')->with('status', 'Wait for authority to approve');
        }   else {
            return redirect('/')->with('status', 'Your Password is Incorrect');
        }
    }
}
