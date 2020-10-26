@extends('layouts.app')

@section('content')
    <!-- Hero Section end -->
	<section class="hero-section">
		<div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="mb-4 text-white text-center">Fill all the form for Loan as Student</h2>
                </div>
            </div>
			<div class="row">
				<div class="col-lg-12">
					<form class="hero-form" method="post" action="/edu_loan/{{Auth::user()->id}}" enctype="multipart/form-data">
                        @csrf
                        <input type="text" name="name" placeholder="Name of your organization" required autofocus>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="address" placeholder="Address of your organization" required autofocus>
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="contact_no" placeholder="Contact Number of your institute" required autofocus>
                        @error('contact_no')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="text" name="level" placeholder="Level / Class" required autofocus>
                        @error('level')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input id="amount" type="text" name="amount" placeholder="Loan Amount"  required autofocus>
                        @error('amount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <label class="text-white" for="installments">Installments Maximum month : 10</label>
                        <input id="installments" type="text" name="installments" placeholder="Loan installments"  required autofocus>
                        @error('installments')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <h5 class="text-white">Per Installment Amount: <span style="color: dodgerblue" id="perInstallmentAmount"></span></h5>

                        <hr>
                        <h3 class="text-white mb-3">Minimum one Gaurantor is required for any kind of loan</h3>

                        <span class="addRowCol">
                            <div class="row">
                                <div class="col-md-11">
                                    <input type="text" name="g_name[]" placeholder="Gaurantor name" required autofocus>
                                    @error('g_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <input type="text" name="g_account_no[]" placeholder="Gaurantor Account Number" required autofocus>
                                    @error('g_account_no')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-1">
                                    <a class="btn btn-info addRow text-white font-weight-bold">Add</a>
                                </div>
                            </div>
                        </span>
                        <script type="text/javascript">
                            $('.addRow').on('click', function() {
                                addRow();
                            });
                            $('span').on('click', '.removeRow', function() {
                                $(this).parent().parent().remove();
                            });
                            function addRow() {
                                var tr = '<div class="row">'+
                                            '<div class="col-md-11">'+
                                            '<input type="text" name="g_name[]" placeholder="Gaurantor Name" class="mt-3" class="form-control">'+
                                            ' @error('g_name')'+
                                            '<span class="text-danger">{{ $message }}</span>'+
                                            '@enderror'+
                                            '<input type="text" name="g_account_no[]" placeholder="Gaurantor Account Number" required autofocus>'+
                                            '@error('g_account_no')'+
                                            '<span class="text-danger">{{ $message }}</span>'+
                                            '@enderror'+
                                            '</div>'+
                                            '<div class="col-md-1">'+
                                            '<a class="btn btn-danger removeRow text-white font-weight-bold">Remove</a>'+
                                            '</div>'+
                                        '</div>';
                                $('.addRowCol').append(tr);
                            }
                        </script>
                        <button type="submit" class="site-btn">Apply</button>
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
            var amount  = Number($('#amount').val());
            var installments  = Number($('#installments').val());   // get value of field

            $('#perInstallmentAmount').html(amount / installments); // add them and output it
        // add them and output it
        });
    </script>


	<!-- Hero Section end -->
@endsection
