<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            'post_office' => 'required',
            'NID_or_birth_certificate_number' => 'required',
         ]);


         if($request->refer_account_number == null) {
            $user = new User();

            $user->name = $request->input('name');
            $user->mobile_number = $request->input('mobile_number');
            $user->fathers_name = $request->input('fathers_name');
            $user->mothers_name = $request->input('mothers_name');
            $user->date_of_birth = $request->input('date_of_birth');
            $user->address = $request->input('address');
            $user->post_office = $request->input('post-office');
            $user->NID_or_birth_certificate_number = $request->input('NID_or_birth_certificate_number');
            if($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '.' .$extension;
                $file->move('uploads/images/', $fileName);
                $user->image = $fileName;
            }   else {
                return $request;
                $user->image = ' ';
            }

            $user->save();
            dd('added');
         }  else {

         }
    }
}
