@extends('layouts.app')

@section('content')
    <!-- Hero Section end -->
	<section class="hero-section">
		<div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="mb-4 text-white text-center">Update your informtation</h2>
                </div>
            </div>
			<div class="row">
                @include('userProfile.userinfo')
                @include('userProfile.updateForm')
			</div>
		<div class="hero-slider owl-carousel">
			<div class="hs-item set-bg" data-setbg="{{ asset('img/hero-slider/1.jpg') }}"></div>
			<div class="hs-item set-bg" data-setbg="{{ asset('img/hero-slider/2.jpg') }}"></div>
			<div class="hs-item set-bg" data-setbg="{{ asset('img/hero-slider/3.jpg') }}"></div>
		</div>
	</section>
	<!-- Hero Section end -->
@endsection


