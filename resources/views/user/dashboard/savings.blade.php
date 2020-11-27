@extends('user.dashboard.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}

            <h2>{{  Auth::user()->name }}'s Savings</h2><br><br>
            <div class="table-responsive">
              <table class="table table-striped table-sm">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Mobile Number</th>
                    <th>Method</th>
                    <th>Tracking Number</th>
                    <th>Amount</th>
                    <th>Total</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach ($users as $key => $user)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $user->mobile_number }}</td>
                    <td>{{ $user->method }}</td>
                    <td>{{ $user->tracking_number }}</td>
                    <td>{{ $user->amount }}</td>
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
