@extends('admin.layouts.app')

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
                    <th>Mobile Number</th>
                    <th>Tracking Number</th>
                    <th>Amount</th>
                    <th>Net Amount</th>
                    <th>Paying for</th>
                    <th>Next Session</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach ($loans as $key => $loan)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $loan->mobile_number }}</td>
                    <td>{{ $loan->tracking_number }}</td>
                    <td>{{ $loan->amount }}</td>
                    <td>{{ $loan->net_amount }}</td>
                    <td>{{ date('F', strtotime($loan->this_month)) }}</td>
                    <td>{{ date('F', strtotime($loan->next_month)) }}</td>
                </tr>
                 @endforeach
                </tbody>
              </table>
            </div>
          </main>
        </div>
      </div>
@endsection
