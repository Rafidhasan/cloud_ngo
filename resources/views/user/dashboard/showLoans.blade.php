@extends('user.dashboard.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}

            <h2>Loans</h2><br><br>
            <div class="table-responsive">
              <table class="table table-striped table-sm">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Installments</th>
                    <th>Per Installment Amount</th>
                    <th>Fee</th>
                    <th>Details</th>
                    {{-- <th>Edit</th> --}}
                  </tr>
                </thead>
                <tbody>
                 @foreach ($users as $key => $user)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>
                        @if (isset($user['business_name']))
                            Business Loan
                        @elseif (isset($user['office_no']))
                            Employee Loan
                        @else
                            Education Loan
                        @endif
                    </td>
                    <td>{{ $user['amount'] }}</td>
                    <td>{{ $user['installments'] }}</td>
                    <td>{{ $user['perInstallmentAmount'] }}</td>
                    <td>{{ $user['fee'] }}</td>
                    <td>
                        @if (isset($user['business_name']))
                            @if ($user['paid'] == 1)
                                Wait for Admin to Approve. Your loan is  completed
                            @else
                                <a href="singleShowLoanBusiness/{{ $user['user_id'] }}/{{ $user['token'] }}">Pay</a>
                            @endif
                        @elseif (isset($user['office_no']))
                        @if ($user['paid'] == 1)
                                Wait for Admin to Approve. Your loan is  completed
                            @else
                                <a href="singleShowLoanEmployee/{{ $user['user_id'] }}/{{ $user['token'] }}">Pay</a>
                            @endif
                        @else
                            @if ($user['paid'] == 1)
                                Wait for Admin to Approve. Your loan is  completed
                            @else
                                <a href="singleShowLoanEdu/{{ $user['user_id'] }}/{{ $user['token'] }}">Pay</a>
                            @endif
                        @endif
                    </td>
                    {{-- <td><a href="singleShowLoanEdit/{{ $user['user_id'] }}/{{ $user['token'] }}">Edit</a></td> --}}
                </tr>
                 @endforeach
                </tbody>
              </table>
            </div>
          </main>
        </div>
      </div>
@endsection
