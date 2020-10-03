@extends('layouts.app')

@section('content')
    <!-- Hero Section end -->
	<section class="hero-section">
		<div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="mb-4 text-white text-center">Register and Become our Member</h2>
                </div>
            </div>
			<div class="row">
				<div class="col-lg-12">
					<form class="hero-form" method="post" action="/registration" enctype="multipart/form-data">
                        @csrf
                        <input type="text" name="name" placeholder="Your Name"  required autofocus>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="mobile_number" placeholder="Your mobile number" required autofocus>
                        @error('mobile_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <label for="user_image" class="text-white">Your Image</label>
                        <input type="file" name="image" class="form-control-file" required>
                        @error('image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="fathers_name" placeholder="Your Father's/Husbend's Name" required autofocus>
                        @error('fathers_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="mothers_name" placeholder="Your Mother's Name" required autofocus>
                        @error('mothers_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="date" class="form-control" name="date_of_birth" placeholder="Enter you date of birth" required autofocus>
                        @error('date_of_birth')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="address" placeholder="Your Address" required autofocus>
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="post_office" placeholder="Post office" required autofocus>
                        @error('post_office')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <label for="nid_image" class="text-white">Your NID Card / Birth Certificate image Image</label>
                        <input type="file" name="nid_image" class="form-control-file" required>
                        @error('nid_image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="NID_or_birth_certificate_number" placeholder="Your NID or Birth Certificate number" required autofocus>
                        @error('NID_or_birth_certificate_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="refer_account_number" placeholder="Reference Account number" autofocus>
                        @error('refer_account_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <p class="text-white">Note About Refer Account number</p>

						<button type="submit" class="site-btn">Register</button>
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


