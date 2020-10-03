@extends('layouts.app')

@section('content')
    <!-- Hero Section end -->
	<section class="hero-section">
		<div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="mb-4 text-white text-center">Login</h2>
                </div>
            </div>
			<div class="row">
				<div class="col-lg-12">
					<form class="hero-form" method="post" action="/login">
                        @csrf
                        <input type="text" name="mobile_number" placeholder="Your mobile number" required autofocus>
                        @error('mobile_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="password" name="password" placeholder="Enter Passweord" required autofocus>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

						<button type="submit" class="site-btn">Login</button>
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
@endsection


