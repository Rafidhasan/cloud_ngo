@extends('layouts.app')

@section('content')

    <!-- Hero Section end -->
	<section class="hero-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 mx-auto">
                    <form class="hero-form" method="post" action="/withdraw/{{ Auth::user()->id }}">
                        @csrf
                        <div class="form-group">
                            <label class="text-white">Payment Method</label>
                            <select id="method" name="method" class="form-control">
                                <option value="bikash">Bikash</option>
                                <option value="nogod">Nogod</option>
                                <option value="rocket">Rocket</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>
                        <input type="text" name="amount" placeholder="Enter Amount"  required autofocus>
                        @error('amount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <input type="password" name="password" placeholder="Enter Password" required autofocus>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <div id="show">
                            <input type="text" name="number" placeholder="Enter Bikash Account Number"  required autofocus>
                            @error('number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button class="site-btn">Withdraw</button>
                    </form>
                    <script>
                        const selectElement = document.getElementById("method");

                        selectElement.addEventListener('change', function (event)  {
                            if(event.target.value == 'bikash') {
                                document.getElementById("show").innerHTML = '<input type="text" name="number" placeholder="Enter Bikash Account Number"  required autofocus>'+
                                    '@error('number')'+
                                        '<span class="text-danger">{{ $message }}</span>'+
                                    '@enderror';
                            }   else if(event.target.value == 'nogod') {
                                document.getElementById("show").innerHTML = '<input type="text" name="number" placeholder="Enter Nagad Account Number"  required autofocus>'+
                                    '@error('number')'+
                                        '<span class="text-danger">{{ $message }}</span>'+
                                    '@enderror';
                            }   else if(event.target.value == 'rocket') {
                                document.getElementById("show").innerHTML = '<input type="text" name="number" placeholder="Enter Rocket Account Number"  required autofocus>'+
                                    '@error('number')'+
                                        '<span class="text-danger">{{ $message }}</span>'+
                                    '@enderror';
                            }   else {
                                document.getElementById("show").innerHTML = '<input type="text" name="number" placeholder="Enter Bank Account Number"  required autofocus>'+
                                    '@error('number')'+
                                        '<span class="text-danger">{{ $message }}</span>'+
                                    '@enderror';
                            }
                        });
                    </script>
				</div>
			</div>
		</div>
	</section>
	<!-- Hero Section end -->
@endsection


