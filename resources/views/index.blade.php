@extends('layouts.app')

@section('content')

    <!-- Hero Section end -->
	<section class="hero-section">
		<div class="container">
			<div class="row">
				{{-- <div class="col-lg-6">
					<div class="hs-text">
						<h2>Looking for a same day loan?</h2>
						<p>Donec eget efficitur ex. Donec eget dolor vitae eros feugiat tristique id vitae massa. Proin vulputate congue rutrum. Fusce lobortis a enim eget tempus. Class aptent taciti sociosqu ad litora torquent per conubia.</p>
						<a href="#" class="site-btn sb-dark">Find out more</a>
					</div>
				</div> --}}
				<div class="col-lg-12" class="center">
                    @auth
                    @if ($user != '')
                    <div class="alert alert-primary" role="alert" style="z-index: 9999">
                        <div class="row">
                            <div class="col-md-9">
                                <h3>{{ $user->status }}</h3>
                            </div>
                            <div class="col-md-3">
                                <a href="/accptApprove/{{Auth::user()->id}}" class="btn btn-primary">OK</a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endauth
                        @if (Auth::check())
                            @include('savingsForm.authForm')
                        @else
                        <form class="hero-form" method="post" action="/saving">
                            @csrf
                            <div class="form-group">
                                <label class="text-white">Payment Method</label>
                                <select id="method" name="method" class="form-control">
                                  <option value="bikash">Bikash</option>
                                  <option value="nogod">Nogod</option>
                                  <option value="rocket">Rocket</option>
                                  <option value="bank">Bank</option>
                                </select>
                            </div>
                            <input type="text" name="phone_number" placeholder="Enter Mobile Number"  required autofocus>
                            @error('phone_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <input type="text" name="tracking_number" placeholder="Enter Tracking Number"  required autofocus>
                            @error('tracking_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <input type="text" name="amount" placeholder="Enter Amount"  required autofocus>
                            @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <input type="password" name="password" placeholder="Enter Password" required autofocus>
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <p class="text-white" style="font-size: 24px" id="show"></p>
                            <button class="site-btn">Apply for Loan</button>
                        </form>
                        @endif
                    </form>
                    <script>
                        const selectElement = document.getElementById("method");

                        selectElement.addEventListener('change', function (event)  {
                            if(event.target.value == 'bikash') {
                                document.getElementById("show").innerHTML = "Bkash account Number is 01772974123";
                            }   else if(event.target.value == 'nogod') {
                                document.getElementById("show").innerHTML = "Nogod account Number is 01772974123";
                            }   else if(event.target.value == 'rocket') {
                                document.getElementById("show").innerHTML = "Rocket Account Number is 017729741230";
                            }   else {
                                document.getElementById("show").innerHTML = "Select A Method";
                            }
                        });
                    </script>
				</div>
			</div>
		</div>
		<div class="hero-slider owl-carousel">
			<div class="hs-item set-bg" data-setbg="img/hero-slider/1.jpg"></div>
			<div class="hs-item set-bg" data-setbg="img/hero-slider/2.jpg"></div>
			<div class="hs-item set-bg" data-setbg="img/hero-slider/3.jpg"></div>
		</div>
	</section>
	<!-- Hero Section end -->
@endsection


