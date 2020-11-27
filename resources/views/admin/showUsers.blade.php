@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}

            <h2>Approved User List</h2><br><br>
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
                    <th>Nominee Address</th>
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
                    <td>{{ $user->nominee_address }}</td>
                    <td><a href="/admin/showSingleUserEditForm/{{ $user->id }}" class="btn btn-primary btn-sm">Edit</a></td>
                    <td><a type="button"  data-toggle="modal" data-target="#exampleModal" class="btn btn-danger btn-sm text-white">Delete</a></td>
                </tr>
                 @endforeach
                </tbody>
              </table>
            </div>
          </main>
        </div>
      </div>
      <div class="modal" id="exampleModal"  tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Delete {{ $user->name }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>Are you sure?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <a href="/admin/deleteUser/{{ $user->id }}" type="button" class="btn btn-danger">Delete</a>
            </div>
          </div>
        </div>
      </div>
@endsection
