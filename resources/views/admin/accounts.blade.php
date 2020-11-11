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
                    <th>Name</th>
                    <th>Mobile Number</th>
                    <th>Service Charge</th>
                    <th>Default Charge</th>
                    <th>Loan Fee</th>
                    <th>Total Service Charge</th>
                    <th>Total Default Charge</th>
                    <th>Total Loan Processing Fee</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach ($users as $key => $user)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->mobile_number }}</td>
                    <td>{{ $user->service_charge }}</td>
                    <td>{{ $user->default_charge }}</td>
                    <td>{{ $user->fee }}</td>
                    <td>{{ $user->total_service_charge }}</td>
                    <td>{{ $user->total_default_charge }}</td>
                    <td>{{ $user->total_fee }}</td>
                </tr>
                 @endforeach
                </tbody>
              </table>
            </div>
          </main>
        </div>
      </div>
@endsection
