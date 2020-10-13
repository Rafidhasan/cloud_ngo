@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}
            <h2>{{ $user->name }}'s Info</h2><br><br>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h4>Personal Information</h4>
                            <img src="{{ asset('/storage/profile-image/'.$user->image)}}" height="250" width="250" alt="">

                            <ul class="mt-4" style="font-size: 20px">
                                <li><strong>Name: </strong>{{ $user->name }}</li>
                                <li><strong>Phone number: </strong>{{ $user->mobile_number }}</li>
                                <li><strong>Father's Name: </strong>{{ $user->fathers_name }}</li>
                                <li><strong>Mother's Name: </strong>{{ $user->mothers_name }}</li>
                                <li><strong>Address: </strong>{{ $user->address }}</li>
                                <li><strong>Thana: </strong>{{ $user->thana }}</li>
                            </ul>
                        </div>
                        <div class="col-md-5">
                            <h4>Security Information</h4>
                            <img src="{{ asset('/storage/nid_or_birth_certificate_image/'.$user->nid_image)}}" height="250" width="500" alt="">

                            <ul class="mt-4" style="font-size: 20px">
                                <li><strong>NID/Birth Certificate Number: </strong>{{ $user->NID_or_birth_certificate_number }}</li>
                                <li><strong>Reference Account Number: </strong>{{ $user->refer_account_number }}</li>
                                <li><strong>Nominee Name: </strong>{{ $user->nominee_name }}</li>
                                <li><strong>Nominee NID: </strong>{{ $user->nominee_nid }}</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h4>Loan Information</h4>
                            <ul class="mt-4" style="font-size: 20px">
                                <li><strong>Loan Method: </strong>Business Loan</li>
                                <li><strong>Loan Amount: </strong>{{ $user->amount }}</li>
                                <li><strong>Installments: </strong>{{ $user->installments }}</li>
                                <li><strong>Organization Name: </strong>{{ $user->org_name }}</li>
                                <li><strong>Office Number: </strong>{{ $user->office_no }}</li>
                                <li><strong>Position: </strong>{{ $user->position }}</li>
                                <li><strong>Salary: </strong>{{ $user->salary }}</li>
                                <li><strong>Experience: </strong>{{ $user->exp }}</li>
                                <li class="mt-5">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="/admin/approveEmployeeLoan/loan/{{ $user->id }}/{{ $user->token }}" class="btn btn-sm btn-success">Approve</a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="/admin/rejectEmployeeloan/loan/{{ $user->id }}/{{ $user->token }}" class="btn btn-sm btn-danger">Reject</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
          </main>
        </div>
      </div>
@endsection
