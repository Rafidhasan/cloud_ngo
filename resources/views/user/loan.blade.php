@extends('layouts.app')

@section('content')
    <!-- Hero Section end -->
	<section class="hero-section">
		<div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="mb-4 text-white text-center">Loan Form</h2>
                </div>
            </div>
			<div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label class="text-white">Loan Categories</label>
                        <select id="method" name="method" class="form-control">
                            <option value="businessLoan">Business Loan</option>
                            <option value="employeeLoan">Emlpoyee Loan</option>
                            <option value="eduLoan">Education Loan</option>
                        </select>
                    </div>

                    <a id="routeController" class="site-btn"  href="#">Next</a>
                    <script>
                        const selectElement = document.getElementById("method");
                            selectElement.addEventListener('change', function (event)  {
                            if(event.target.value == 'businessLoan') {
                                document.getElementById("routeController").href="/business_loan";
                            }   else if(event.target.value == 'employeeLoan') {
                                document.getElementById("routeController").href="/emp_loan";
                            }   else if(event.target.value == 'eduLoan') {
                                document.getElementById("routeController").href="/edu_loan";
                            }   else {
                                document.getElementById("show").innerHTML = "Select A Method";
                            }
                        });
                    </script>
                    </div>
                </div>
			</div>
	</section>
	<!-- Hero Section end -->
@endsection


