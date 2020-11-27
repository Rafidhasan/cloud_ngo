<?php

namespace App\Http\Controllers;

use App\User;

use App\Withdraw;

use App\Accounts;

use Auth;

use DB;

use App\UserNotification;

use App\SavingAcount;

use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use PDO;

class WithdrawController extends Controller
{
    public function index() {
        return view('user.withdraw');
    }

    public function create(Request $request) {
        $user = User::where('id', $request->id)->first();
        $saving = SavingAcount::where('user_id', Auth::user()->id)->latest()->first();

        if (Hash::check($request->password, $user->password)) {
            if($saving->total > $request->amount) {
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
                return redirect('/dashboard')->with('status', 'Your saving is Less than your demand');
            }
        }   else {
            return redirect('/')->with('status', 'Your Password is Incorrect');
        }
    }

    public function showUserForm() {
        $user = SavingAcount::where('user_id', Auth::user()->id)->where('approved', 1)->latest()->first();

        return view('user.withdraw', [
            'user' => $user
        ]);
    }

    public function adminApproved() {
        $users = DB::table('users')
            ->join('withdraws', 'users.id', 'withdraws.user_id')
            ->where('withdraws.approved', '=', 0)
            ->get();

        return view('admin.dashboard.approveUserWithdraw', [
            'users' => $users
        ]);
    }

    public function accpt(Request $request) {
        $user = Withdraw::where('user_id', $request->id)->first();
        $user->approved = 1;
        $user->save();

        $savings = SavingAcount::where('user_id', $request->id)->where('approved', 1)->latest()->first();
        $savings->total -= $user->amount;
        $savings->save();

        $notification = new UserNotification();
        $notification->user_id = $request->id;
        $notification->status = "Your Withdraw is approved. Your Total saving now is ".$savings->total;
        $notification->save();

        $account = new Accounts();
        $account->withdraw_amount = $user->amount;
        $account->save();

        return redirect('/admin')->with('status', 'Withdraw Accepted');
    }

    public function rjct(Request $request) {
        $user = Withdraw::where('user_id', $request->id)->first();
        $user->delete();

        return redirect('/admin')->with('status', 'Withdraw Rejected');
    }
}
