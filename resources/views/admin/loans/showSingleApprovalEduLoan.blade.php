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
                        <div class="col-md-3">
                            <h4>Security Information</h4>
                            <img src="{{ asset('/storage/nid_or_birth_certificate_image/'.$user->nid_image)}}" height="250" width="300" alt="">

                            <ul class="mt-4" style="font-size: 20px">
                                <li><strong>NID/Birth Certificate Number: </strong>{{ $user->NID_or_birth_certificate_number }}</li>
                                <li><strong>Reference Account Number: </strong>{{ $user->refer_account_number }}</li>
                                <li><strong>Nominee Name: </strong>{{ $user->nominee_name }}</li>
                                <li><strong>Nominee NID: </strong>{{ $user->nominee_nid }}</li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <h4>Loan Information</h4>
                            <ul class="mt-4" style="font-size: 20px">
                                <li><strong>Loan Method: </strong>Business Loan</li>
                                <li><strong>Loan Amount: </strong>{{ $user->amount }}</li>
                                <li><strong>Installments: </strong>{{ $user->installments }}</li>
                                <li><strong>Organization Name: </strong>{{ $user->org_name }}</li>
                                <li><strong>Organization Address: </strong>{{ $user->org_address }}</li>
                                <li><strong>Level: </strong>{{ $user->level }}</li>
                                <li><strong>Phone Number of Education: </strong>{{ $user->edu_no }}</li>
                                <li class="mt-5">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="/admin/approveEduLoan/loan/{{ $user->id }}/{{ $user->token }}" class="btn btn-sm btn-success">Approve</a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="/admin/rejectEduloan/loan/{{ $user->id }}/{{ $user->token }}" class="btn btn-sm btn-danger">Reject</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <h4 class="mb-4">Garantor Information</h4>
                            <div style="font-size: 20px">
                                @foreach ($garantors as $key => $garantor)
                                <ul>
                                    <strong style="color: dodgerblue">{{ $key + 1 }}</strong>
                                    <li><strong>Garantor Name: </strong><a href="/admin/showGProfile/{{ $garantor->g_mobile_number}}">{{ $garantor->g_name }}</a></li>
                                    <li><strong>GarantorNumber: </strong><a href="/admin/showGProfile/{{ $garantor->g_mobile_number}}">{{ $garantor->g_mobile_number }}</a></li>
                                    <li>
                                        @if ($garantor->g_approved == 0)
                                            <strong>Status: </strong><span style="color: red">Not Approved</span>
                                        @else
                                            <strong>Status: </strong><span style="color: green">Approved</span>
                                        @endif
                                    </li>
                                </ul>
                            @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </main>
        </div>
      </div>
@endsection
