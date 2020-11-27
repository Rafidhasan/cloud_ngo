@extends('user.dashboard.layouts.app')

@section('content')

    <!-- Hero Section end -->
    <div class="container">
        <div class="row">
            <div class="mx-auto col-lg-9">
                <h2 class="mt-3">Your Saving is {{  $user->total }}TK.</h2>
                <form method="post" action="/withdraw/{{ Auth::user()->id }}">
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
                    <input type="text" class="form-control" name="amount" placeholder="Enter Amount"  required autofocus>
                    @error('amount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <input type="password" class="form-control" name="password" placeholder="Enter Password" required autofocus>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <div id="show">
                        <input type="text" class="form-control" name="number" placeholder="Enter Bikash Account Number"  required autofocus>
                        @error('number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button class="btn btn-primary mt-3">Withdraw</button>
                </form>
                <script>
                    const selectElement = document.getElementById("method");

                    selectElement.addEventListener('change', function (event)  {
                        if(event.target.value == 'bikash') {
                            document.getElementById("show").innerHTML = '<input type="text" name="number"  class="form-control"  placeholder="Enter Bikash Account Number"  required autofocus>'+
                                '@error('number')'+
                                    '<span class="text-danger">{{ $message }}</span>'+
                                '@enderror';
                        }   else if(event.target.value == 'nogod') {
                            document.getElementById("show").innerHTML = '<input type="text" name="number"  class="form-control"  placeholder="Enter Nagad Account Number"  required autofocus>'+
                                '@error('number')'+
                                    '<span class="text-danger">{{ $message }}</span>'+
                                '@enderror';
                        }   else if(event.target.value == 'rocket') {
                            document.getElementById("show").innerHTML = '<input type="text" name="number"  class="form-control"  placeholder="Enter Rocket Account Number"  required autofocus>'+
                                '@error('number')'+
                                    '<span class="text-danger">{{ $message }}</span>'+
                                '@enderror';
                        }   else {
                            document.getElementById("show").innerHTML = '<input type="text" name="number"  class="form-control"  placeholder="Enter Bank Account Number"  required autofocus>'+
                                '@error('number')'+
                                    '<span class="text-danger">{{ $message }}</span>'+
                                '@enderror';
                        }
                    });
                </script>
            </div>
        </div>
    </div>
	<!-- Hero Section end -->
@endsection


