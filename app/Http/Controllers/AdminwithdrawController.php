<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

use App\Accounts;
use App\Adminwithdraw;

class AdminwithdrawController extends Controller
{
    public function index() {
        $users = DB::table('accounts')->get();

        $total = 0;

        foreach($users as $user) {
            $total += $user->total_fee + $user->total_service_charge + $user->total_default_charge;
        }

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

        if($request->amount >= $total) {
            $withdraw = new AdminWithdraw();
            $withdraw->user_id = Auth::user()->id;
            $withdraw->amount = $request->amount;
            $withdraw->details = $request->details;
            $withdraw->save();

            return redirect('status', '');
        }   else {

        }
    }
}
