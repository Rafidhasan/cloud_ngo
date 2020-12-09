@extends('user.dashboard.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}

            <h2>Loans for Approval</h2><br><br>
            <div class="table-responsive">
              <table class="table table-striped table-sm">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Method</th>
                    <th>Name</th>
                    <th>Mobile Number</th>
                    <th>Garantor Name</th>
                    <th>Garantor Account No</th>
                    <th>Accept</th>
                    <th>Reject</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach ($users as $key => $user)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>
                        {{ $user->loan_method }}
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->mobile_number }}</td>
                    <td>
                        @if (isset($user->g_name))
                            <a href="/admin/showGProfile/{{ $user->g_mobile_number }}">{{ $user->g_name }}</a>
                        @else
                            None
                        @endif
                    </td>
                    <td>
                        @if (isset($user->g_mobile_number))
                            {{ $user->g_mobile_number }}
                        @else
                            None
                        @endif
                    </td>
                    <td>
                        @if ($user->loan_method == 'business_loan')
                        <a href="/g_loan/business/accept/{{ $user->user_id }}/{{ $user->loan_id }}">Accept</a>
                        @elseif ($user->loan_method == 'employee_loan')
                            <a href="/g_loan/emp/accept/{{ $user->user_id }}">Accept</a>
                        @else
                            <a href="/g_loan/edu/accept/{{ $user->user_id }}">Accept</a>
                        @endif
                    </td>
                    <td>
                        @if (isset($user->business_name))
                            <a href="/g_loan/business/reject/{{ $user->user_id }}">Reject</a>
                        @elseif (isset($user->office_no))
                            <a href="/g_loan/emp/reject/{{ $user->user_id }}">Reject</a>
                        @else
                            <a href="/g_loan/edu/reject/{{ $user->user_id }}">Reject</a>
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
