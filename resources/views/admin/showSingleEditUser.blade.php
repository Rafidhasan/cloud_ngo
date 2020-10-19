@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="mb-4 text-white text-center">Update your informtation</h2>
            </div>
        </div>
        <div class="row">
            @include('admin.userProfile.updateForm')

        </div>
    </div>
	<!-- Hero Section end -->
@endsection


