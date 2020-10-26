@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}
            <h2>{{ Auth::user()->name }}'s Dashboard</h2><br><br>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Company Funds</h5>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Mobile Number</h5>
                            <h1 class="card-text">{{ $user->mobile_number }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Defaul Charge</h5>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Service Charge</h5>
                            <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Loan Processing Fee</h5>
                            <h1 class="card-text">
                                @if (isset($loan))
                                    {{ $loan->net_amount }}
                                @else
                                    No Due
                                @endif
                            </h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Members</h5>
                            <h1 class="card-text">
                                {{ $total_user }}
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Loan Applications</h5>
                            <h1 class="card-text">
                                @if ($total_loans == 0)
                                    No Loan Applications
                                @else
                                    {{ $total_loans }}
                                @endif
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
          </main>
        </div>
      </div>
@endsection
