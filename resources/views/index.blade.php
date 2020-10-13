@extends('layouts.app')

@section('content')

    <!-- Hero Section end -->
	<section class="hero-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-6">
					<div class="hs-text">
						<h2>Looking for a same day loan?</h2>
						<p>Donec eget efficitur ex. Donec eget dolor vitae eros feugiat tristique id vitae massa. Proin vulputate congue rutrum. Fusce lobortis a enim eget tempus. Class aptent taciti sociosqu ad litora torquent per conubia.</p>
						<a href="#" class="site-btn sb-dark">Find out more</a>
					</div>
				</div>
				<div class="col-lg-6">
                        @if (Auth::check())
                            @include('savingsForm.authForm')
                        @else
                        <form class="hero-form" method="post" action="/saving">
                            @csrf
                            <div class="form-group">
                                <label class="text-white">Payment Method</label>
                                <select name="method" class="form-control">
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
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. </p>
                            <button class="site-btn">Apply for Loan</button>
                        </form>
                        @endif
                    </form>
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

	<!-- Why Section end -->
	<section class="why-section spad">
		<div class="container">
			<div class="text-center mb-5 pb-4">
				<h2>Why Choose us?</h2>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="icon-box-item">
						<div class="ib-icon">
							<i class="flaticon-012-24-hours"></i>
						</div>
						<div class="ib-text">
							<h5>Money in 1 Hour!</h5>
							<p>Lorem ipsum dolor sit amet, consecte-tur adipiscing elit, sed do eiusmod tem por incididunt ut labore et dolore mag na aliqua. </p>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="icon-box-item">
						<div class="ib-icon">
							<i class="flaticon-036-customer-service"></i>
						</div>
						<div class="ib-text">
							<h5>Helpfull Staff</h5>
							<p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per incep-tos himenaeos. Suspendisse potenti. Ut gravida mattis.</p>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="icon-box-item">
						<div class="ib-icon">
							<i class="flaticon-039-info"></i>
						</div>
						<div class="ib-text">
							<h5>Credit History Considered</h5>
							<p>Conubia nostra, per inceptos himenae os. Suspendisse potenti. Ut gravida mattis magna, non varius lorem sodales nec. In libero orci.</p>
						</div>
					</div>
				</div>
			</div>
			<div class="text-center pt-3">
				<a href="#" class="site-btn sb-big">Apply Now!</a>
			</div>
		</div>
	</section>
	<!-- Why Section end -->


	<!-- CTA Section end -->
	<section class="cta-section set-bg" data-setbg="img/cta-bg.jpg">
		<div class="container">
			<h2>Already have a <strong>L2Go</strong> Bank Loan?</h2>
			<h5>If you're thinking about borrowing more, we're here to help.</h5>
			<a href="#" class="site-btn sb-dark sb-big">Find out More</a>
		</div>
	</section>
	<!-- CTA Section end -->


	<!-- Feature Section -->
	<section class="feature-section spad">
		<div class="container">
			<div class="feature-item">
				<div class="row">
					<div class="col-lg-6">
						<img src="img/feature-1.jpg" alt="">
					</div>
					<div class="col-lg-6">
						<div class="feature-text">
							<h2>Get a personal loan from just 8.5% APR</h2>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tem por incididunt ut labore et dolore mag na aliqua.  Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse potenti. Ut gravida mattis magna, non varius lorem sodales nec. In libero orci, ornare non nisl.</p>
							<a href="#" class="readmore">Apply for a loan now <img src="img/arrow.png" alt=""></a>
						</div>
					</div>
				</div>
			</div>
			<div class="feature-item">
				<div class="row">
					<div class="col-lg-6 order-lg-2">
						<img src="img/feature-2.jpg" alt="">
					</div>
					<div class="col-lg-6 order-lg-1">
						<div class="feature-text">
							<h2>Get aproved in minutes after you apply online</h2>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tem por incididunt ut labore et dolore mag na aliqua.  Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse potenti. Ut gravida mattis magna, non varius lorem sodales nec. In libero orci, ornare non nisl.</p>
							<a href="#" class="readmore">Apply for a loan now <img src="img/arrow.png" alt=""></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Feature Section end -->


	<!-- Help Section -->
	<section class="help-section spad">
		<div class="container">
			<div class="text-center text-white mb-5 pb-4">
				<h2>How a personal loan can help</h2>
			</div>
			<div class="row">
				<div class="col-md-6">
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tem por incididunt ut labore et dolore mag na aliqua.  Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse potenti. Ut gravida mattis magna, non varius lorem sodales nec.</p>
				</div>
				<div class="col-md-6">
					<p>Sit amet, consectetur adipiscing elit, sed do eiusmod tem por incididunt ut labore et dolore mag na aliqua.  Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse potenti. Ut gravida mattis magna, non varius lorem sodales nec. In libero orci, ornare non nisl.</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<ul class="help-list">
						<li>Buying a car</li>
						<li>Take control of your finances</li>
						<li>Pay school tuitions</li>
						<li>Adding value to your home</li>
					</ul>
				</div>
				<div class="col-md-4">
					<ul class="help-list">
						<li>Increese your budget</li>
						<li>Have a day to remember</li>
						<li>Get a new card</li>
						<li>Go on a holliday</li>
					</ul>
				</div>
				<div class="col-md-4">
					<ul class="help-list">
						<li>Get an Insurance</li>
						<li>Take a trip</li>
						<li>Help your kids</li>
						<li>Renovate your home</li>
					</ul>
				</div>
			</div>
		</div>
	</section>
	<!-- Help Section end -->


	<!-- Info Section -->
	<section class="info-section spad">
		<div class="container">
			<div class="row">
				<div class="col-lg-5">
					<img src="img/info-img.jpg" alt="">
				</div>
				<div class="col-lg-7">
					<div class="info-text">
						<h2>We’re here to help</h2>
						<h5>Monday to Thursday (8am to 8pm), and Friday (8am to 5pm).</h5>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tem por incididunt ut labore et dolore mag na aliqua.  Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse potenti. Ut gravida mattis magna, non varius lorem sodales nec. In libero orci, ornare non nisl.</p>
						<ul>
							<li>+34 56873 2246</li>
							<li>contact@loans2go.com</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Info Section end -->

	<!-- Score Section end -->
	<section class="score-section text-white set-bg" data-setbg="img/score-bg.jpg">
		<div class="container">
			<div class="row">
				<div class="col-xl-6 col-lg-8">
					<h2>Calculate my Score</h2>
					<h4>Check your credit reports as often as you want, it won't affect your scores.</h4>
					<a href="#" class="site-btn sb-big">show my score</a>
				</div>
			</div>
			<img src="img/hand.png" alt="" class="hand-img">
		</div>
	</section>
    <!-- Score Section end -->
@endsection


