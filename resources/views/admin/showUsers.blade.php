@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}

            <h2>Approve Savings Records</h2><br><br>
            <div class="table-responsive">
              <table class="table table-striped table-sm">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Mobile Number</th>
                    <th>Address</th>
                    <th>Thana</th>
                    <th>NID Number</th>
                    <th>Nominee Name</th>
                    <th>Nominee NID</th>
                    <th>Edit</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach ($users as $key => $user)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->mobile_number }}</td>
                    <td>{{ $user->address }}</td>
                    <td>{{ $user->thana }}</td>
                    <td>{{ $user->NID_or_birth_certificate_number }}</td>
                    <td>{{ $user->nominee_name }}</td>
                    <td>{{ $user->nominee_nid }}</td>
                    <td><a href="/admin/showUser/{{ $user->id }}" class="btn btn-primary btn-sm">Edit</a></td>
                    <td><a href="/admin/deleteUser/{{ $user->id }}" class="btn btn-danger btn-sm">Delete</a></td>
                </tr>
                 @endforeach
                </tbody>
              </table>
            </div>
          </main>
        </div>
      </div>
@endsection
