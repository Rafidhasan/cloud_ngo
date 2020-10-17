@extends('layouts.app')

@section('content')
    <!-- Hero Section end -->
	<section class="hero-section">
		<div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="mb-4 text-white text-center">Fill all the form for Loan as Employee</h2>
                </div>
            </div>
			<div class="row">
				<div class="col-lg-12">
					<form class="hero-form" method="post" action="/emp_loan/{{Auth::user()->id}}" enctype="multipart/form-data">
                        @csrf
                        <input type="text" name="name" placeholder="Name of your offcie" required autofocus>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="contact_no" placeholder="Contact Number of your Office" required autofocus>
                        @error('contact_no')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="exp" placeholder="Experience As a Employee" required autofocus>
                        @error('exp')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="position" placeholder="Position in your office" required autofocus>
                        @error('position')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="salary" placeholder="Your salary" required autofocus>
                        @error('salary')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input id="amount" type="text" name="amount" placeholder="Loan Amount"  required autofocus>
                        @error('amount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <h5 class="text-white">Per Installment Amount: <span style="color: dodgerblue" id="perInstallmentAmount"></span></h5>

                        <hr>
                        <h3 class="text-white mb-3">Optional (only if you have any Gaurantor)</h3>
                        <input type="text" name="g_name" placeholder="Gaurantor Name" autofocus>
                        @error('g_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <input type="text" name="g_account_no" placeholder="Gaurantor Account Number" autofocus>
                        @error('g_account_no')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
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
    <script>
        $('input').keyup(function(){ // run anytime the value changes
            var amount  = Number($('#amount').val());   // get value of field

            $('#perInstallmentAmount').html(amount / 10); // add them and output it
        // add them and output it
        });
    </script>
	<!-- Hero Section end -->
@endsection


