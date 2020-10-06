<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use Auth;

use App\User;

class UserRecordController extends Controller
{
    public function index() {
        return view('index');
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


         if($request->refer_account_number == null) {
            $user = new User();
            $date = $request->date_of_birth;
            $token = Str::random(5);

            $user->name = $request->input('name');
            $user->mobile_number = $request->input('mobile_number');
            $user->fathers_name = $request->input('fathers_name');
            $user->mothers_name = $request->input('mothers_name');
            $user->date_of_birth =  $request->date_of_birth;
            $user->address = $request->input('address');
            $user->thana = $request->input('thana');
            $user->NID_or_birth_certificate_number = $request->input('NID_or_birth_certificate_number');
            $user->password = \Hash::make($token);
            $user->refer_account_number = null;


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
            return redirect('/')->with('status', 'You are added. Your password is '.$token.' You can see your password in profile');
         }  else {

         }
    }

    public function login(Request $request) {
        $request->validate([
            'mobile_number' => 'required',
            'password' => 'required'
        ]);
        $password = \Hash::make($request->password);

        $credentials = $request->only('mobile_number', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect('/');
        }else{
            return redirect()->back();
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
}
