@extends('user.dashboard.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}

            <h2>{{  Auth::user()->name }}'s Garantors History</h2><br><br>
            <div class="table-responsive">
              <table class="table table-striped table-sm">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Garantor Name</th>
                    <th>Garantor Mobile Number</th>
                    <th>Loan Method</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach ($users as $key => $user)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $user->g_name }}</td>
                    <td>{{ $user->g_mobile_number }}</td>
                    <td>{{ $user->loan_method }}</td>
                    @if ($user->g_approved == 0)
                        <td style="color: red">Not Approved</td>
                    @else
                        <td style="color: green">Approved</td>
                    @endif
                    </td>
                </tr>
                 @endforeach
                </tbody>
              </table>
            </div>
          </main>
        </div>
      </div>
@endsection
