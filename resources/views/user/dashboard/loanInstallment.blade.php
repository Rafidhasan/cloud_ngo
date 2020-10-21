@extends('admin.layouts.app')

@section('content')
    <!-- Hero Section end -->
          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

            {{-- <canvas class="my-4" id="myChart" width="900" height="380"></canvas> --}}

            <h2>Loan Installment Form</h2><br><br>
            <h4>Your Loan Information before pay: </h4>
            <ul>
                <li><h5>Loan Amount: {{ $user[0]["amount"] }}</h5></li>
                <li><h5>Per Installment Amount: {{ $user[0]["perInstallmentAmount"] }}</h5></li>
                <li><h5>Loan date: {{ $starting_date }}</h5></li>
                <li><h5>Loan last date: {{ $ending }}</h5></li>
                <li><h5 style="color: red">You have to clear your loan before 1st day of the month</h5></li>
                <li><h5><a href="/prev_loan_details/{{ $user[0]['id'] }}">Check Previous Loan payment</a></li></h5>
                <li><h5>Paying for {{ $next_month }}. Last date - {{ $next_month_date }}</h5></li>
            </ul>
            <form method="post" action="/first_loan_installment/{{ $user[0]['id'] }}/{{ $user[0]['token'] }}/{{ $next_month_date }}/{{ $user[0]["amount"] }}">
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
                  <div class="form-group">
                    <input type="text" class="form-control" name="phone_number" placeholder="Enter Mobile Number"  required autofocus>
                    @error('phone_number')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <input type="text" class="form-control" name="tracking_number" placeholder="Enter Tracking Number"  required autofocus>
                    @error('tracking_number')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <input type="text" class="form-control" name="amount" placeholder="Enter Amount"  required autofocus>
                    @error('amount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Enter Password" required autofocus>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                  <p style="font-size: 24px" id="show">Bkash account Number is 01772974123</p>
                  <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <script>
                const selectElement = document.getElementById("method");

                selectElement.addEventListener('change', function (event)  {
                    if(event.target.value == 'bikash') {
                        document.getElementById("show").innerHTML = "Bkash account Number is 01772974123";
                    }   else if(event.target.value == 'nogod') {
                        document.getElementById("show").innerHTML = "Nogod account Number is 01772974123";
                    }   else if(event.target.value == 'rocket') {
                        document.getElementById("show").innerHTML = "Rocket Account Number is 017729741230";
                    }   else {
                        document.getElementById("show").innerHTML = "Select A Method";
                    }
                });
            </script>
          </main>
        </div>
      </div>
@endsection
