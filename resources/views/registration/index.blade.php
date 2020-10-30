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
					<form class="hero-form" method="post" action="/register" enctype="multipart/form-data">
                        @csrf
                        <input type="text" name="name" placeholder="Your Name"  required autofocus>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="mobile_number" placeholder="Your mobile number" required autofocus>
                        @error('mobile_number')
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

                        <label for="date_of_birth" class="text-white">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" placeholder="Enter you date of birth" required autofocus>
                        @error('date_of_birth')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="address" placeholder="Your Address" required autofocus>
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="thana" placeholder="Your related Thana" required autofocus>
                        @error('thana')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="NID_or_birth_certificate_number" placeholder="Your NID or Birth Certificate number" required autofocus>
                        @error('NID_or_birth_certificate_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="nominee_name" placeholder="Enter Nominee name" required autofocus>
                        @error('nominee_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="nominee_address" placeholder="Enter Nominee Address" required autofocus>
                        @error('nominee_address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="refer_account_number" placeholder="Reference Account number" autofocus>
                        @error('refer_account_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <p class="text-white">Note About Refer Account number</p>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="user_image" class="text-white">Your Image</label>
                                <input type="file" name="image" class="form-control-file" required>
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-4">
                                <label for="nid_image" class="text-white">Your NID Card / Birth Certificate Image</label>
                                <input type="file" name="nid_image" class="form-control-file" required>
                                @error('nid_image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-4">
                                <button type="submit" class="site-btn">Register</button>
                            </div>
                        </div>
					</form>
				</div>
			</div>
		</div>
	</section>
	<!-- Hero Section end -->
@endsection


