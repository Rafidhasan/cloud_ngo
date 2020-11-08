<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Admin;

use App\user;

use App\SavingAcount;

use App\ServiceCharge;

use App\EmployeeLoan;
use App\BusinessLoan;
use App\EduLoan;

use App\UserNotification;

use App\Accounts;

use App\Withdraw;

use App\ForgetPass;
use App\Forgetuser;

use Auth;

use DB;

class AdminController extends Controller
{
    public function index() {
        $user = User::where('id', Auth::user()->id)->first();
        $users = User::get();
        $total_user = 0;

        foreach($users as $user) {
            if($user->checkAdmin() != "admin") {
                $person[] = $user;
                $total_user = count($person);
            }
        }

        $saving = SavingAcount::where('user_id', $user->id)->first();

        $edu_loans = DB::table('edu_loans')
            ->get()
            ->toArray();

        $employee_loans = DB::table('employee_loans')
            ->get()
            ->toArray();

        $business_loans = DB::table('business_loans')
            ->get()
            ->toArray();

        $loans = array_merge($edu_loans, $employee_loans, $business_loans);

        $total_loans = count($loans);

        return view('admin.dashboard.index', [
            'user' => $user,
            'saving' => $saving,
            'total_user' => $total_user,
            'total_loans' => $total_loans
        ]);
    }

    public function show(Request $request) {
        $users = User::select('id', 'name', 'mobile_number')
        ->where('users.approved', 0)
        ->get();

        return view('admin.index', [
            'users' => $users,
        ]);
    }

    public function showUsers() {
        $users = User::select('id', 'name', 'mobile_number', 'address', 'thana', 'NID_or_birth_certificate_number', 'nominee_name', 'nominee_nid')
            ->where('users.approved', 1)
            ->get();

        return view('admin.showUsers', [
            'users' => $users
        ]);
    }

    public function savings() {
        $users = User::join('saving_acounts', 'users.id', '=', 'saving_acounts.user_id')
            ->select('name', 'user_id', 'users.mobile_number', 'address', 'thana', 'tracking_number', 'amount', 'total')
            ->where('saving_acounts.approved', 1)
            ->orderByDesc('saving_acounts.created_at')
            ->get();

            return view('admin.savings', [
            'users' => $users
        ]);
    }

    public function editSavingsIndex(Request $request) {
        $user = User::join('saving_acounts', 'users.id', '=', 'saving_acounts.user_id')
            ->select('users.name', 'saving_acounts.*')
            ->where('tracking_number', $request->tracking_number)
            ->first();

        return view('admin.savings_edit', [
            'user' => $user
        ]);
    }

    public function updateSavings(Request $request) {
        $id = $request->id;
        $savings = SavingAcount::find($id);

        $savings->mobile_number = $request->mobile_number;
        $savings->method = $request->method;
        $savings->tracking_number = $request->tracking_number;
        $savings->amount = $request->amount;
        $savings->user_id = $request->user_id;
        $savings->approved = 1;

        // $row = count(SavingAcount::select('amount')->where('user_id', $user->id)->get());

        if($request->total == $request->amount) {
            $savings->total = $request->amount;
        }   else {
            $user = SavingAcount::select('amount')->where('tracking_number', $request->track)->first();

            $updated_amount = (int)$request->amount - $user->amount;

            $users = SavingAcount::where('user_id', $request->user_id)->orderBy('created_at', 'asc')->get();

            foreach($users as $key => $user) {
                if($user->total == $request->total) {
                    $index = $key;
                }
            }

            $savings->total = $request->total + $updated_amount;

            foreach($users as $key => $user) {
                if ($key < $index) continue;
                $user->total += $updated_amount;

                $user->save();
            }
        }
        $savings->save();

        return redirect('/admin/savings')->with('status', 'savings Updated');
    }

    public function deleteSavings(Request $request) {
        $user = SavingAcount::where('tracking_number', $request->track)
        ->first();

        $user->delete();

        return redirect('/admin')->with('status', 'Saving Deleted');
    }

    public function edituser(Request $request) {
        $user = User::where('id', $request->id)->first();

        return view('admin.showSingleEditUser', [
            'user' => $user
        ]);
    }

    public function updateUser(Request $request) {
        $id = $request->id;
        $user = User::find($id);

        $user->name = $request->name;
        $user->mobile_number = $request->mobile_number;
        $user->fathers_name = $request->fathers_name;
        $user->mothers_name = $request->mothers_name;
        $user->date_of_birth = $request->date_of_birth;
        $user->address = $request->address;
        $user->thana = $request->thana;
        $user->nominee_name = $request->nominee_name;

        $user->save();

        return redirect('/admin')->with('status', 'User updating Successfull');
    }

    public function showSingleUser(Request $request) {
        $user = User::where('id', $request->id)->first();

        return view('admin.showMembers', [
            'user' => $user
        ]);
    }

    public function approveSingleUser(Request $request) {
        $user = User::where('id', $request->id)
            ->first();

        $notification = UserNotification::where('user_id', $request->id)->first();

        $user->approved = 1;
        $user->update();

        $username = "Alauddin101";
        $hash = "4f9ec55ab0531a44a466910119d97847";
        $numbers = $user->mobile_number;
        $message = $notification->status;


        $params = array('app'=>'ws', 'u'=>$username, 'h'=>$hash, 'op'=>'pv', 'unicode'=>'1','to'=>$numbers, 'msg'=>$message);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://alphasms.biz/index.php?".http_build_query($params, "", "&"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Accept:application/json"));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($ch);
        curl_close ($ch);

        return redirect('/admin')->with('member approved');
    }

    public function rejectSingleUser(Request $request) {
        $user = User::where('id', $request->id)
            ->first();

        $user->delete();
        return redirect('/admin')->with('Member deleted');
    }

    public function approveSavings() {
        $users = User::join('saving_acounts', 'users.id', '=', 'saving_acounts.user_id')
            ->select('users.id', 'name', 'users.mobile_number', 'address', 'thana', 'tracking_number', 'amount', 'total')
            ->where('saving_acounts.approved', 0)
            ->orderByDesc('saving_acounts.created_at')
            ->get();

            return view('admin.approveSavings', [
            'users' => $users
        ]);
    }

    public function accptSavings(Request $request) {
        $user_info = DB::table('users')
            ->join('saving_acounts', 'users.id', '=', 'saving_acounts.user_id')
            ->first();

        $saving = SavingAcount::where('tracking_number', $request->id)->first();
        $saving->approved = 1;
        $saving->save();

        $username = "Alauddin101";
        $hash = "4f9ec55ab0531a44a466910119d97847";
        $numbers = $user_info->mobile_number; //Recipient Phone Number multiple number must be separated by comma
        $message = 'Thanks! Your Saving amount is '.$saving->amount.' and your current saving is '.$saving->total;


        $params = array('app'=>'ws', 'u'=>$username, 'h'=>$hash, 'op'=>'pv', 'unicode'=>'1','to'=>$numbers, 'msg'=>$message);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://alphasms.biz/index.php?".http_build_query($params, "", "&"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Accept:application/json"));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($ch);
        curl_close ($ch);

        return redirect('/admin/approveSavings')->with('status', 'Saving Approved');
    }

    public function rejectSavings(Request $request) {
        $saving = SavingAcount::where('tracking_number', $request->id)->first();

        $user_info = DB::table('users')
            ->join('saving_acounts', 'users.id', '=', 'saving_acounts.user_id')
            ->first();


        $saving->delete();

        $username = "Alauddin101";
        $hash = "4f9ec55ab0531a44a466910119d97847";
        $numbers = $user_info->mobile_number; //Recipient Phone Number multiple number must be separated by comma
        $message = 'Your amount '.$saving->amount. ' is not received';


        $params = array('app'=>'ws', 'u'=>$username, 'h'=>$hash, 'op'=>'pv', 'unicode'=>'1','to'=>$numbers, 'msg'=>$message);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://alphasms.biz/index.php?".http_build_query($params, "", "&"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Accept:application/json"));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($ch);
        curl_close ($ch);

        return redirect('/admin/approveSavings')->with('status', 'Saving Deleted');
    }

    // Service Charge
    public function showServiceCharge() {
        $users = DB::table('users')
            ->select('name', 'mobile_number', 'address', 'thana', 'clearence_date')
            ->join('service_charges', 'users.id', '=', 'service_charges.user_id')
            ->get();

        return view('admin.serviceCharge', [
            'users' => $users
        ]);
    }

    //accounts
    public function accounts() {
        $users = DB::table('users')
            ->join('accounts', 'users.id', '=', 'accounts.user_id')
            ->get();
        dd($users);
    }

    // Loans
    public function showLoans() {
        $edu_loans = DB::table('users')
            ->join('edu_loans', 'users.id', '=', 'edu_loans.user_id')
            ->where('edu_loans.approved', '=', 0)
            ->get()
            ->toArray();

        $employee_loans = DB::table('users')
            ->join('employee_loans', 'users.id', '=', 'employee_loans.user_id')
            ->where('employee_loans.approved', '=', 0)
            ->get()
            ->toArray();

        $business_loans = DB::table('users')
            ->join('business_loans', 'users.id', '=', 'business_loans.user_id')
            ->where('business_loans.approved', '=', 0)
            ->get()
            ->toArray();

        $users = array_merge($edu_loans, $employee_loans, $business_loans);

        return view('admin.showLoans', [
            'users' => $users
        ]);
    }

    public function showSingleMemberLoansBusiness(Request $request) {
        $user = DB::table('users')
            ->join('business_loans', 'users.id', '=', 'business_loans.user_id')
            ->join('garantors', 'business_loans.id', '=', 'garantors.loan_id')
            ->where('business_loans.token', '=', $request->token)
            ->where('business_loans.approved', '=', 0)
            ->where('business_loans.user_id', '=', $request->id)
            ->first();

        $garantors = DB::table('garantors')->where('loan_id', $user->loan_id)->where('garantors.loan_method', '=', "business_loan")->get();

        return view('admin.loans.showSingleApprovalBusinessLoan', [
            'user' => $user,
            'garantors' => $garantors
        ]);
    }

    public function showSingleMemberLoansEmployee(Request $request) {
        $user = DB::table('users')
            ->join('employee_loans', 'users.id', '=', 'employee_loans.user_id')
            ->join('garantors', 'employee_loans.id', '=', 'garantors.loan_id')
            ->where('employee_loans.token', '=', $request->token)
            ->where('employee_loans.user_id', '=', $request->id)
            ->first();

        $garantors = DB::table('garantors')->where('loan_id', $user->loan_id)->where('garantors.loan_method', '=', "employee_loan")->get();

        return view('admin.loans.showSingleApprovalEmployeeLoan', [
            'user' => $user,
            'garantors' => $garantors
        ]);
    }

    public function showSingleMemberLoansEdu(Request $request) {
        $user = DB::table('users')
            ->join('edu_loans', 'users.id', '=', 'edu_loans.user_id')
            ->join('garantors', 'edu_loans.id', '=', 'garantors.loan_id')
            ->where('edu_loans.token', '=', $request->token)
            ->where('edu_loans.user_id', '=', $request->id)
            ->first();

        $garantors = DB::table('garantors')->where('loan_id', $user->loan_id)->where('garantors.loan_method', '=', "edu_loan")->get();

        return view('admin.loans.showSingleApprovalEduLoan', [
            'user' => $user,
            'garantors' => $garantors
        ]);
    }

    public function approveBusinessLoan(Request $request) {
        $loan = BusinessLoan::where('token', $request->token)
            ->first();

        if(isset($loan->g_account_no)) {
            if($loan->g_approved == 1) {
                $loan->approved = 1;
                $loan->approved_date = now();
                $loan->save();



                return redirect('/admin/loans')->with('status', 'loan accepted');
            }   else {
                return redirect('/admin')->with('status', 'Wait for Garantor to accept first');
            }
        }   else {
            $loan->approved = 1;
            $loan->approved_date = now();
            $savings = SavingAcount::where('user_id', $loan->user_id)->latest()->first();

            $savings->total = (int)$savings->total - (int)$loan->fee;

            $loan->save();

            $savings->save();

            return redirect('/admin/loans')->with('status', 'loan accepted');
        }
    }

    public function rejectBusinessLoan(Request $request) {
        $loan = BusinessLoan::where('user_id', $request->id)
            ->where('token', $request->token)
            ->first();

        $loan->delete();

        return redirect('/admin/loans')->with('status', 'loan rejected');
    }

    public function approveEmployeeLoan(Request $request) {
        $loan = EmployeeLoan::where('token', $request->token)
            ->first();

        if(isset($loan->g_account_no)) {
            if($loan->g_approved == 1) {
                $loan->approved = 1;
                $loan->approved_date = now();
                $loan->save();

                return redirect('/admin/loans')->with('status', 'loan accepted');
            }   else {
                return redirect('/admin')->with('status', 'Wait for Garantor to accept first');
            }
        }   else {
            $loan->approved = 1;
            $loan->approved_date = now();
            $savings = SavingAcount::where('user_id', $loan->user_id)->latest()->first();

            $savings->total = (int)$savings->total - (int)$loan->fee;

            $loan->save();

            $savings->save();

            return redirect('/admin/loans')->with('status', 'loan accepted');
        }
    }

    public function rejectEmployeeLoan(Request $request) {
        $loan = EmployeeLoan::where('user_id', $request->id)
            ->where('token', $request->token)
            ->first();

        $loan->delete();

        return redirect('/admin/loans')->with('status', 'loan rejected');
    }

    public function approveEduLoan(Request $request) {
        $loan = EduLoan::where('token', $request->token)
            ->first();

        if(isset($loan->g_account_no)) {
            if($loan->g_approved == 1) {
                $loan->approved = 1;
                $loan->approved_date = now();
                $loan->save();

                return redirect('/admin/loans')->with('status', 'loan accepted');
            }   else {
                return redirect('/admin')->with('status', 'Wait for Garantor to accept first');
            }
        }   else {
            $loan->approved = 1;
            $loan->approved_date = now();
            $savings = SavingAcount::where('user_id', $loan->user_id)->latest()->first();

            $savings->total = (int)$savings->total - (int)$loan->fee;

            $loan->save();

            $savings->save();

            return redirect('/admin/loans')->with('status', 'loan accepted');
        }
    }

    public function rejectEduLoan(Request $request) {
        $loan = EduLoan::where('user_id', $request->id)
            ->where('token', $request->token)
            ->first();

        $loan->delete();

        return redirect('/admin/loans')->with('status', 'loan rejected');
    }

    // Approved Loans
    public function approvedLoans() {
        $edu_loans = DB::table('users')
            ->join('edu_loans', 'users.id', '=', 'edu_loans.user_id')
            ->select('users.name', 'token', 'users.mobile_number', 'user_id', 'edu_no', 'amount', 'installments', 'perInstallmentAmount', 'fee')
            ->where('edu_loans.approved', '=', 1)
            ->get()
            ->toArray();

        $employee_loans = DB::table('users')
            ->join('employee_loans', 'users.id', '=', 'employee_loans.user_id')
            ->select('users.name', 'org_name', 'users.mobile_number','user_id','token',  'office_no', 'amount', 'installments', 'perInstallmentAmount', 'fee')
            ->where('employee_loans.approved', '=', 1)
            ->get()
            ->toArray();

        $business_loans = DB::table('users')
            ->join('business_loans', 'users.id', '=', 'business_loans.user_id')
            ->select('users.name', 'users.mobile_number', 'user_id','token', 'business_name', 'amount', 'installments', 'perInstallmentAmount', 'fee')
            ->where('business_loans.approved', '=', 1)
            ->get()
            ->toArray();

        $users = array_merge($edu_loans, $employee_loans, $business_loans);

        return view('admin.showApprovedLoans', [
            'users' => $users
        ]);
    }

    public function showGProfile(Request $request) {
        $user = User::where('mobile_number', $request->number)->first();

        return view('admin.showGProfile', [
            'user' => $user
        ]);
    }

    // admin forget pass routes
    public function forgetPass() {
        $users = DB::table('users')
            ->join('forget_users', 'users.id', '=', 'forget_users.user_id')
            ->get();

        return view('admin.forgetPass', [
            'users' => $users
        ]);
    }

    public function approvePass(Request $request) {
        $user = ForgetUser::where('user_id', $request->id)->first();

        $user_info = User::select('mobile_number')->where('id', $request->id)->first();

        $notification = new UserNotification();
        $notification->user_id = $request->id;
        $notification->status = "Your Password is ". $user->token;
        $notification->save();

        $username = "Alauddin101";
        $hash = "4f9ec55ab0531a44a466910119d97847";
        $numbers = $user_info->mobile_number; //Recipient Phone Number multiple number must be separated by comma
        $message = $notification->status;


        $params = array('app'=>'ws', 'u'=>$username, 'h'=>$hash, 'op'=>'pv', 'unicode'=>'1','to'=>$numbers, 'msg'=>$message);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://alphasms.biz/index.php?".http_build_query($params, "", "&"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Accept:application/json"));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($ch);
        curl_close ($ch);

        DB::table('forget_users')->where('user_id', '=', $request->id)->delete();

        return redirect('/admin')->with('status', "Notification send to this number ".$user_info->mobile_number);
    }

    public function rejectPass(Request $request) {
        $user = ForgetUser::where('id', $request->id)->first();

        DB::table('forget_users')->where('id', '=', $request->id)->delete();

        return redirect('/admin')->with('status', "Not Approved. Notification send");
    }

    //withdraw form
    public function showWithdraw() {
        $users = DB::table('users')
            ->join('withdraws', 'users.id', '=', 'withdraws.user_id')
            ->where('withdraws.approved', 0)
            ->get();

        return view('admin.dashboard.withdrawShow', [
            'users' => $users
        ]);
    }

    public function approveWithdraws(Request $request) {
        $user = Withdraw::where('user_id', $request->id)->where('serial', $request->serial)->first();
        $user->approved = 1;
        $user->save();

        $savings = SavingAcount::where('user_id', $request->id)->latest()->first();
        $savings->total = $savings->total - $user->amount;
        $savings->save();

        $notification = new UserNotification();
        $notification->user_id = $request->id;
        $notification->status = "Your Withdraw is approved and ".$user->amount. " is deducted from your savings";
        $notification->save();

        return redirect('/admin')->with('status', "Approved and Notification send");
    }
}
