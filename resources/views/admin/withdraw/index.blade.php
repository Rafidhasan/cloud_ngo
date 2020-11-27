@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <h2>Admin Withdraw Form -  <spanc>Total Accounts - {{ $total }} TK</span></h2><br>
                            <a class="btn btn-primary" href="/admin/prev-withdraws">Previous Withdraws</a>
                            <br><br>
                            <form method="post" action="/admin/withdraw/create/{{ Auth::user()->id }}">
                                @csrf
                                <div class="form-group">
                                    <h5>Amount</h5>
                                    <input type="text" class="form-control" name="amount">
                                </div>
                                <div class="form-group">
                                    <h5>Details</h5>
                                    <textarea type="text" class="form-control" name="details"></textarea>
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
