@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}

            <h2>Loan Installments Details</h2><br><br>
            <div class="table-responsive">
              <table class="table table-striped table-sm">
                <thead>
                  <tr>
                    @if ($users == "")
                        <th>Status</th>
                    @else
                        <th>#</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Net Amount</th>
                        <th>Installments</th>
                        <th>Per Installment Amount</th>
                        <th>Total</th>
                        <th>Last Paid</th>
                        <th>Next Payment</th>
                        <th>End Date</th>
                        <th>Approve</th>
                        <th>Reject</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                    @if ($users == "")
                        Still Due for this users
                    @else
                    @foreach ($users as $key => $user)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>
                            @if (isset($user->business_name))
                                Business Loan
                            @elseif (isset($user->office_no))
                                Employee Loan
                            @else
                                Education Loan
                            @endif
                        </td>
                        <td>{{ $user->amount }}</td>
                        <td>{{ $user->net_amount }}</td>
                        <td>{{ $user->installments }}</td>
                        <td>{{ $user->perInstallmentAmount }}</td>
                        <td>{{ $user->total }}</td>
                        <td>{{ date('d-m-Y', strtotime($user->this_month)) }}</td>
                        <td>{{ date('d-m-Y', strtotime($user->next_month)) }}</td>
                        <td>{{ $ending }}</td>
                        <td><a class="btn btn-success btn-sm" href="/admin/loanInstallment/apporve/{{ $user->tracking_number }}/{{ $user->token }}">Approve</a></td>
                        <td><a class="btn btn-danger btn-sm" href="/admin/loanInstallment/apporve/{{ $user->tracking_number }}/{{ $user->token }}">Reject</a></td>
                    </tr>
                     @endforeach
                    @endif
                </tbody>
              </table>
            </div>
          </main>
        </div>
      </div>
@endsection
