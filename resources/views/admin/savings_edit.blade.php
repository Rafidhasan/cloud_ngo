@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <h2>Savings Information of {{ $user->name }}</h2>
                            <br><br>
                            <form method="post" action="/admin/savings/update/{{ $user->user_id }}/{{ $user->total }}/{{ $user->id }}/{{ $user->tracking_number }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <h5>Mobile Number</h5>
                                    <input type="text" class="form-control" name="mobile_number" value="{{ $user->mobile_number }}">
                                </div>
                                <div class="form-group">
                                    <h5>Method</h5>
                                    <input type="text" class="form-control" name="method" value="{{ $user->method }}">
                                </div>
                                <div class="form-group">
                                    <h5>Tracking Number</h5>
                                    <input type="text" class="form-control" name="tracking_number" value="{{ $user->tracking_number }}">
                                </div>
                                <div class="form-group">
                                    <h5>Amount</h5>
                                    <input type="text" class="form-control" name="amount" value="{{ $user->amount }}">
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </table>
                    </div>
                </div>
            </div>
          </main>
        </div>
      </div>
@endsection
