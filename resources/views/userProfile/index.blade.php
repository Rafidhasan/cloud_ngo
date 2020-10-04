@extends('layouts.app')

@section('content')
    <!-- Hero Section end -->
	<section class="hero-section">
		<div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="mb-4 text-white text-center">{{ $user->name }}'s Profile</h2>
                </div>
            </div>
			<div class="row">
                <div class="col-lg-5">
                    <div class="hero-form">
                        <div class="container">
                            <div class="row">
                                <img src="{{ asset('storage/profile-image/'.$user->image) }}" class="avatar rounded-circle mx-auto d-block" alt="">
                            </div>
                            <div class="row">
                                <div class="text-white">
                                    <h5 class="mt-4">Name: <span class="text-secondary">{{ $user->name }}</span></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-white">
                                    <h5 class="mt-4">Phone Number: <span class="text-secondary">{{ $user->mobile_number }}</span></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-white">
                                    <h5 class="mt-4">Father's Name <span class="text-secondary">{{ $user->fathers_name }}</span></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-white">
                                    <h5 class="mt-4">Mother's Name: <span class="text-secondary">{{ $user->mothers_name }}</span></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-white">
                                    <h5 class="mt-4">Date_of_birth: <span class="text-secondary">{{ $user->date_of_birth }}</span></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-white">
                                    <h5 class="mt-4">Address: <span class="text-secondary">{{ $user->address }}</span></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-white">
                                    <h5 class="mt-4">Thana: <span class="text-secondary">{{ $user->thana }}</span></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-white">
                                    <h5 class="mt-4">NID / Birth Certificate Number: <span class="text-secondary">{{ $user->NID_or_birth_certificate_number }}</span></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-white">
                                    <h5 class="mt-4">Nominee Name: <span class="text-secondary">{{ $user->nominee_name }}</span></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-white">
                                    <h5 class="mt-4">Nominee NID: <span class="text-secondary">{{ $user->nominee_nid }}</span></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-white">
                                    <h5 class="mt-4">Refer Account Number: <span class="text-secondary">{{ $user->refer_account_number }}</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="col-lg-7">
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

                        <input type="text" name="thana" placeholder="Your related Thana" required autofocus>
                        @error('thana')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <label for="nid_image" class="text-white">Your NID Card / Birth Certificate Image</label>
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
			<div class="hs-item set-bg" data-setbg="{{ asset('img/hero-slider/1.jpg') }}"></div>
			<div class="hs-item set-bg" data-setbg="{{ asset('img/hero-slider/2.jpg') }}"></div>
			<div class="hs-item set-bg" data-setbg="{{ asset('img/hero-slider/3.jpg') }}"></div>
		</div>
	</section>
	<!-- Hero Section end -->
@endsection


