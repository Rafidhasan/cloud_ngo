<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

use Auth;

use App\savingAcount;

use App\Accounts;
use App\Adminwithdraw;

class AdminwithdrawController extends Controller
{
    public function index() {
        $users = DB::table('accounts')->get();

        $total = 0;

        $total = DB::table('accounts')->latest()->first();

        return view('admin.withdraw.index' ,[
            'total' => $total
        ]);
    }

    public function store(Request $request) {
        $users = Accounts::get();
        $total = 0;

        foreach($users as $user) {
            $total += $user->total_fee + $user->total_service_charge + $user->total_default_charge;
        }

        if($request->amount <= $total) {
            $withdraw = new AdminWithdraw();
            $withdraw->user_id = Auth::user()->id;
            $withdraw->amount = $request->amount;
            $withdraw->details = $request->details;
            $withdraw->save();

            $saving = SavingAcount::where('user_id', Auth::user()->id)->latest()->first();
            $saving->total = $saving->total - $request->amount;
            $saving->save();

            $accounts = new Accounts();
            $accounts->user_id = Auth::user()->id;
            $prev_fees = Accounts::latest()->first();
            $accounts->total = $prev_fees->total - $request->amount;

            $accounts->save();

            return redirect('admin')->with('status', 'Withdraw is completed');
        }   else {
            return redirect('admin')->with('status', 'Amount is more than withdraw amount');
        }
    }
}
