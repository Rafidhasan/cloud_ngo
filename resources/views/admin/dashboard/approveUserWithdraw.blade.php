@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}

            <h2>Withdraw for Approval</h2><br><br>
            <div class="table-responsive">
              <table class="table table-striped table-sm">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Mobile Number</th>
                    <th>Method</th>
                    <th>Withdraw Amount</th>
                    <th>withdraw Number</th>
                    <th>Total Withdraw</th>
                    <th>Accept</th>
                    <th>Reject</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach ($users as $key => $user)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->mobile_number }}</td>
                    <td>{{ $user->method }}</td>
                    <td>{{ $user->amount }}</td>
                    <td>{{ $user->number }}</td>
                    <td>{{ $user->total }}</td>
                    <td><a class="btn btn-primary btn-sm" href="admin/user/withdraw/approve/{{ $user->id }}">Approve</a></td>
                    <td><a type="button"  data-toggle="modal" data-target="#exampleModal" class="btn btn-danger btn-sm text-white">Reject</a></td>
                </tr>
                <div class="modal" id="exampleModal"  tabindex="-1">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Delete {{ $user->name }}'s Savings</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <p>Are you sure?</p>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <a href="admin/user/withdraw/reject/{{ $user->id }}" type="button" class="btn btn-danger">Reject</a>
                        </div>
                      </div>
                    </div>
                  </div>
                 @endforeach
                </tbody>
              </table>
            </div>
          </main>
        </div>
      </div>
@endsection
