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

                        <button type="submit" class="site-btn">Login</button><br><br>
                        <a href="/forget" class="lead" style="color: floralwhite">Forget Password</a>
					</form>
				</div>
			</div>
		</div>
	</section>
	<!-- Hero Section end -->
@endsection


