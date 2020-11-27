@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <h2>Personal Information of {{ $user->name }}</h2>
                            <br><br>
                            <form method="post" action="/admin/user/update/{{ $user->id }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <h5>Name</h5>
                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}">
                                </div>
                                <div class="form-group">
                                    <h5>Mobile Number</h5>
                                    <input type="text" class="form-control" name="method" value="{{ $user->mobile_number }}">
                                </div>
                                <div class="form-group">
                                    <h5>Father's Name</h5>
                                    <input type="text" class="form-control" name="fathers_name" value="{{ $user->fathers_name }}">
                                </div>
                                <div class="form-group">
                                    <h5>Mother's Name</h5>
                                    <input type="text" class="form-control" name="mothers_name" value="{{ $user->mothers_name }}">
                                </div>
                                <div class="form-group">
                                    <h5>Date of Birth</h5>
                                    <input type="text" class="form-control" name="mothers_name" value="{{ $user->date_of_birth }}">
                                </div>
                                <div class="form-group">
                                    <h5>Address</h5>
                                    <input type="text" class="form-control" name="address" value="{{ $user->address }}">
                                </div>
                                <div class="form-group">
                                    <h5>NID / Birth Certificate</h5>
                                    <input type="text" class="form-control" name="NID_or_birth_certificate_number" value="{{ $user->NID_or_birth_certificate_number }}">
                                </div>
                                <div class="form-group">
                                    <h5>Nominee Name</h5>
                                    <input type="text" class="form-control" name="nominee_name" value="{{ $user->nominee_name }}">
                                </div>
                                <div class="form-group">
                                    <h5>Nominee Address</h5>
                                    <input type="text" class="form-control" name="nominee_address" value="{{ $user->nominee_address }}">
                                </div>
                                <div class="form-group">
                                    <h5>Refer Account Number</h5>
                                    <input type="text" class="form-control" name="mothers_name" value="{{ $user->refer_account_number }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Personal Image</h5>
                                            <input type="file" name="image" class="form-control-file">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Personal NID Image</h5>
                                            <input type="file" class="form-control-file" name="nid_image">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </table>
                    </div>
                </div>
            </div>
          </main>
        </div>
      </div>
@endsection
