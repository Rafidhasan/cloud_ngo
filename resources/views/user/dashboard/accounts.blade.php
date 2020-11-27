@extends('user.dashboard.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}

            <h2>{{  Auth::user()->name }}'s Accounts History</h2><br><br>
            <div class="table-responsive">
              <table class="table table-striped table-sm">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Loan Processing Fee</th>
                    <th>Service Charge</th>
                    <th>Default Charge</th>
                    <th>Total Saving</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach ($users as $key => $user)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $user->fee }}</td>
                    <td>{{ $user->service_charge }}</td>
                    <td>{{ $user->default_charge }}</td>
                    <td>{{ $user->total }}</td>
                    <td>{{ $user->created_at }}</td>
                </tr>
                 @endforeach
                </tbody>
              </table>
            </div>
          </main>
        </div>
      </div>
@endsection
