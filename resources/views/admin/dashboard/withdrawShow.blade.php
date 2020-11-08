@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}

            <h2>Withdraw Records</h2><br><br>
            <div class="table-responsive">
              <table class="table table-striped table-sm">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Mobile Number</th>
                    <th>Address</th>
                    <th>Thana</th>
                    <th>Amount</th>
                    <th>Total</th>
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
                    <td>{{ $user->amount }}</td>
                    <td>{{ $user->total }}</td>
                    <td><a class="btn btn-primary btn-sm" href="/admin/withdraws/approve/{{ $user->user_id }}/{{ $user->serial }}">Approve</a></td>
                    <td><a class="btn btn-danger btn-sm" href="/admin/withdraws/reject/{{ $user->user_id }}/{{ $user->serial }}">Reject</a></td>
                </tr>
                 @endforeach
                </tbody>
              </table>
            </div>
          </main>
        </div>
      </div>
@endsection
