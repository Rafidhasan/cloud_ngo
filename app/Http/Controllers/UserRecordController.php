<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use App\Models\User;

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

            $user->name = $request->input('name');
            $user->mobile_number = $request->input('mobile_number');
            $user->fathers_name = $request->input('fathers_name');
            $user->mothers_name = $request->input('mothers_name');
            $user->date_of_birth =  $request->date_of_birth;
            $user->address = $request->input('address');
            $user->thana = $request->input('thana');
            $user->NID_or_birth_certificate_number = $request->input('NID_or_birth_certificate_number');
            $user->token = Str::random(5);
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
            dd('added');
         }  else {

         }
    }
}
