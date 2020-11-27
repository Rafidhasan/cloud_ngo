@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <h2>loan Information</h2>
                            <br><br>
                            <form method="post" action="/admin/BusinessLoan/update/{{ $user->user_id }}/{{ $user->token }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <h5>Amount</h5>
                                    <input type="text" class="form-control" name="amount" value="{{ $user->amount }}">
                                </div>
                                <div class="form-group">
                                    <h5>Installments</h5>
                                    <input type="text" class="form-control" name="installments" value="{{ $user->installments }}">
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
