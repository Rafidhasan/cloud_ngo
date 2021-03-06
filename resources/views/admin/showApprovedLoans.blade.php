@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}

            <h2>Approved Loans</h2><br><br>
            <div class="table-responsive">
              <table class="table table-striped table-sm">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Method</th>
                    <th>Name</th>
                    <th>Mobile Number</th>
                    <th>Amount</th>
                    <th>Installments</th>
                    <th>Per Installment Amount</th>
                    <th>Fee</th>
                    <th>Edit</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
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
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->mobile_number }}</td>
                    <td>{{ $user->amount }}</td>
                    <td>{{ $user->installments }}</td>
                    <td>{{ $user->perInstallmentAmount }}</td>
                    <td>{{ $user->fee }}</td>
                    <td>
                        @if (isset($user->business_name))
                            <a class="btn btn-primary btn-sm" href="/admin/singleShowBusinessLoanEdit/{{ $user->user_id }}/{{ $user->token }}">Edit</a>
                        @elseif (isset($user->office_no))
                            <a class="btn btn-primary btn-sm" href="/admin/singleShowEmployeeLoanEdit/{{ $user->user_id }}/{{ $user->token }}">Edit</a>
                        @else
                            <a class="btn btn-primary btn-sm" href="/admin/singleShowEducationLoanEdit/{{ $user->user_id }}/{{ $user->token }}">Edit</a>
                        @endif
                    </td>
                    <td>
                        @if (isset($user->business_name))
                            <a class="btn btn-danger btn-sm" href="/admin/singleShowBusinessLoanDelete/{{ $user->user_id }}/{{ $user->token }}">Delete</a>
                        @elseif (isset($user->office_no))
                            <a class="btn btn-danger btn-sm" href="/admin/singleShowEmployeeLoanDelete/{{ $user->user_id }}/{{ $user->token }}">Delete</a>
                        @else
                            <a class="btn btn-danger btn-sm" href="/admin/singleShowEducationLoanDelete/{{ $user->user_id }}/{{ $user->token }}">Delete</a>
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
