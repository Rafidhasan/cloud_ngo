<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use Auth;

use App\ServiceCharge;

use App\ForgetUser;
use App\SavingAcount;
use App\UserNotification;

use DB;

use App\User;

class UserRecordController extends Controller
{
    public function index() {
        if(Auth::user() == '') {
            return view('index');
        }   else {
            $user = UserNotification::where('user_id', Auth::user()->id)->first();
            return view('index', [
                'user' => $user
            ]);
        }
    }

    public function create() {
        return view('registration.index');
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'mobile_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
            'fathers_name' => 'required',
            'mothers_name' => 'required',
            'date_of_birth' => 'required',
            'address' => 'required',
            'thana' => 'required',
            'NID_or_birth_certificate_number' => 'required',
         ]);

         $user = "";

         if($user == '') {
            if($request->refer_account_number == null) {
                $user = User::select('mobile_number')->get();
                $user = new User();
                $digits = 5;
                $date = $request->date_of_birth;
                $token = rand(pow(10, $digits-1), pow(10, $digits)-1);

                $user->name = $request->input('name');
                $user->mobile_number = $request->input('mobile_number');
                $user->fathers_name = $request->input('fathers_name');
                $user->mothers_name = $request->input('mothers_name');
                $user->date_of_birth =  $request->date_of_birth;
                $user->address = $request->input('address');
                $user->thana = $request->input('thana');
                $user->nominee_name = $request->nominee_name;
                $user->nominee_address = $request->nominee_address;
                $user->NID_or_birth_certificate_number = $request->input('NID_or_birth_certificate_number');
                $user->password = \Hash::make($token);
                $user->refer_account_number = $request->refer_account_number;
                $user->nominee_nid = null;

                if($request->hasFile('image')) {
                    $user_file = $request->file('image');
                    $extension = $user_file->getClientOriginalExtension();
                    $fileName = time() . '.' .$extension;
                    $user_file->move('storage/profile-image', $fileName);
                    $user->image = $fileName;
                }   else {
                    $user->image = ' ';
                    return redirect('/')->with('status', 'Must have Image');
                }

                if($request->hasFile('nid_image')) {
                    $nid_file = $request->file('nid_image');
                    $extension = $nid_file->getClientOriginalExtension();
                    $fileName = time() . '.' .$extension;
                    $nid_file->move('storage/nid_or_birth_certificate_image', $fileName);
                    $user->nid_image = $fileName;
                }   else {
                    $user->nid_image = ' ';
                    return redirect('/')->with('status', 'Must have Image');
                }

                $user->save();

                $notification = new UserNotification();
                $notification->user_id = $user->id;
                $notification->status = 'Thank you for Registration. Your PIN is '.$token;

                $notification->save();

                return redirect('/')->with('status', 'Wait for Authity to validate. Your password is '.$token);
             }  else {
                $user = new User();
                $digits = 5;
                $date = $request->date_of_birth;
                $token = rand(pow(10, $digits-1), pow(10, $digits)-1);

                $user->name = $request->input('name');
                $user->mobile_number = $request->input('mobile_number');
                $user->fathers_name = $request->input('fathers_name');
                $user->mothers_name = $request->input('mothers_name');
                $user->date_of_birth =  $request->date_of_birth;
                $user->address = $request->input('address');
                $user->thana = $request->input('thana');
                $user->NID_or_birth_certificate_number = $request->input('NID_or_birth_certificate_number');
                $user->password = \Hash::make($token);
                $user->refer_account_number = $request->refer_account_number;

                if($request->hasFile('image')) {
                    $user_file = $request->file('image');
                    $extension = $user_file->getClientOriginalExtension();
                    $fileName = time() . '.' .$extension;
                    $user_file->move('storage/profile-image', $fileName);
                    $user->image = $fileName;
                }   else {
                    return $request;
                    $user->image = ' ';
                }

                if($request->hasFile('nid_image')) {
                    $nid_file = $request->file('nid_image');
                    $extension = $nid_file->getClientOriginalExtension();
                    $fileName = time() . '.' .$extension;
                    $nid_file->move('storage/nid_or_birth_certificate_image', $fileName);
                    $user->nid_image = $fileName;
                }   else {
                    return $request;
                    $user->nid_image = ' ';
                }
                $user->save();

                $notification = new UserNotification();
                $notification->user_id = $user->id;
                $notification->status = 'Wait for Authity to validate. Your password is '.$token;

                $notification->save();

                return redirect('/')->with('status', 'Wait for Authity to validate. Your password is '.$token);
             }
         }  else if($request->refer_account_number != null) {
            $user = User::where('mobile_number',$request->refer_account_number)->first();

            if($user != '') {
                $user = User::select('mobile_number')->get();
                $user = new User();
                $digits = 5;
                $date = $request->date_of_birth;
                $token = rand(pow(10, $digits-1), pow(10, $digits)-1);

                $user->name = $request->input('name');
                $user->mobile_number = $request->input('mobile_number');
                $user->fathers_name = $request->input('fathers_name');
                $user->mothers_name = $request->input('mothers_name');
                $user->date_of_birth =  $request->date_of_birth;
                $user->address = $request->input('address');
                $user->thana = $request->input('thana');
                $user->nominee_name = $request->input('nominee_name');
                $user->nominee_address = $request->input('nominee_address');
                $user->NID_or_birth_certificate_number = $request->input('NID_or_birth_certificate_number');
                $user->password = \Hash::make($token);
                $user->refer_account_number = $request->refer_account_number;


                if($request->hasFile('image')) {
                    $user_file = $request->file('image');
                    $extension = $user_file->getClientOriginalExtension();
                    $fileName = time() . '.' .$extension;
                    $user_file->move('storage/profile-image', $fileName);
                    $user->image = $fileName;
                }   else {
                    $user->image = ' ';
                    return redirect('/')->with('status', 'Must have Image');
                }

                if($request->hasFile('nid_image')) {
                    $nid_file = $request->file('nid_image');
                    $extension = $nid_file->getClientOriginalExtension();
                    $fileName = time() . '.' .$extension;
                    $nid_file->move('storage/nid_or_birth_certificate_image', $fileName);
                    $user->nid_image = $fileName;
                }   else {
                    $user->nid_image = ' ';
                    return redirect('/')->with('status', 'Must have Image');
                }

                $user->save();

                $notification = new UserNotification();
                $notification->user_id = $user->id;
                $notification->status = 'Thank you for Registration. Your PIN is '.$token;

                $notification->save();

                return redirect('/')->with('status', 'Wait for Authity to validate. Your password is '.$token);
            }   else {
                return redirect('/')->with('status', 'Referesnce Account Number is not Correct');
            }
         }  else {
            return redirect('/')->with('status', 'Incorrect Informtation');
         }
    }

    public function login(Request $request) {
        $request->validate([
            'mobile_number' => 'required',
            'password' => 'required'
        ]);
        $password = \Hash::make($request->password);

        $user = User::where("mobile_number", $request->mobile_number)->first();

        $credentials = $request->only('mobile_number', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            if($user->approved == 0) {
                return redirect('/');
            }   else {
                return redirect('/login')->with('status', 'wait for authority to approve');
            }
        }else{
            return redirect('/login')->with('status', 'Incorrect Password');
        }
    }


    public function logout() {
         auth()->logout();

         return redirect('/');
    }

    public function show(Request $request) {
        $user = User::where('id', $request->id)->first();
        return view('userProfile.index', [
            'user'=>$user
        ]);
    }

    public function update(Request $request) {
        $id = $request->id;
        $user = User::find($id);

        $user->name = $request->name;
        $user->mobile_number = $request->mobile_number;
        $user->fathers_name = $request->fathers_name;
        $user->mothers_name = $request->mothers_name;
        $user->date_of_birth =  $request->date_of_birth;
        $user->address = $request->address;
        $user->thana = $request->thana;
        $user->nominee_name = $request->nominee_name;
        $user->nominee_nid = $request->nominee_nid;
        $user->NID_or_birth_certificate_number = $request->NID_or_birth_certificate_number;
        $user->refer_account_number = $request->refer_account_number;
        $user->password = User::select('password')->where('id', $request->id)->first();

        if($request->image == '') {
            $image = User::select('image')->where('id', $request->id)->first();
            $user->image = $image->image;
        }   else {
            $user->image = $request->image;
        }

        if($request->nid_image == '') {
            $nid_image = User::select('nid_image')->where('id', $request->id)->first();
            $user->nid_image = $nid_image->nid_image;
        }   else {
            $user->nid_image = $request->nid_image;
        }

        $user->save();
        return redirect('/')->with('success', 'Your Information has updated');
    }

    public function forgetPassIndex() {
        return view('user.forgetPass');
    }

    public function forgetPassStore(Request $request) {
        $user = User::where('mobile_number',$request->mobile_number)->first();
        if($user == "") {
            dd('incorrect mobile number');
        }   else {
            $digits = 5;
            $token = rand(pow(10, $digits-1), pow(10, $digits)-1);

            $forget_user = new ForgetUser();
            $forget_user->user_id = $user->id;
            $forget_user->token = $token;
            $forget_user->save();

            $user->password = \Hash::make($token);
            $user->save();

            return redirect('/')->with('status', 'Manually verify by calling 01111111 number');
        }
    }

    public function removeNotification(Request $request) {
        DB::table('user_notifications')->where('user_id', '=', $request->id)->delete();

        return redirect('/');
    }

    public function accounts() {
        $users = DB::table('users')
            ->join('accounts', 'users.id', '=', 'accounts.user_id')
            ->join('saving_acounts', 'users.id', '=', 'saving_acounts.user_id')
            ->get();

        return view('user.dashboard.accounts', [
            'users' => $users
        ]);
    }

    public function garantorList() {
        $business_loans = DB::table('users')
            ->join('business_loans', 'users.id', '=', 'business_loans.user_id')
            ->join('garantors', 'business_loans.id', '=', 'garantors.loan_id')
            ->get()
            ->toArray();

        $employee_loans = DB::table('users')
            ->join('employee_loans', 'users.id', '=', 'employee_loans.user_id')
            ->join('garantors', 'employee_loans.id', '=', 'garantors.loan_id')
            ->get()
            ->toArray();

        $edu_loans = DB::table('users')
            ->join('edu_loans', 'users.id', '=', 'edu_loans.user_id')
            ->join('garantors', 'edu_loans.id', '=', 'garantors.loan_id')
            ->get()
            ->toArray();

        $users = array_merge($business_loans, $employee_loans, $edu_loans);

        return view('user.dashboard.garantorList', [
            'users' => $users
        ]);
    }

    public function showSavings() {
        $users = SavingAcount::where('user_id', Auth::user()->id)
            ->where('approved', 1)
            ->get();

        return view('user.dashboard.savings', [
            'users' => $users
        ]);
    }

    public function showSavingsForm() {
        return view('user.savings');
    }

    public function loan() {
        return view('user.laon');
    }
}
