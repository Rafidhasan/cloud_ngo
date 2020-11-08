<!DOCTYPE html>
<html lang="zxx">
<head>
	<title>NGO</title>
	<meta charset="UTF-8">
	<meta name="description" content="loans HTML Template">
	<meta name="keywords" content="loans, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Favicon -->
	<link href="img/favicon.ico" rel="shortcut icon"/>

    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">

	<!-- Stylesheets -->
	<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}"/>
	<link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}"/>
	<link rel="stylesheet" href="{{ asset('css/flaticon.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/slicknav.min.css') }}"/>
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>

	<!-- Main Stylesheets -->
	<link rel="stylesheet" href="{{ asset('css/style.css') }}" />
</head>
<body style="background-color: #000;">
	<!-- Page Preloder -->
	<div id="preloder">
		<div class="loader"></div>
    </div>
    <!-- Header Section -->
	<header class="header-section">
        @if (session('status'))
        <div class="alert alert-primary" role="alert" style="z-index: 9999">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3>{!! session('status') !!}</h3>
        </div>
        @endif
		<a href="/" class="site-logo">
			<img src="{{ asset('img/loan_logo.png') }}" width="70">
		</a>
		<nav class="header-nav">
			<ul class="main-menu">
                <li><a href="/" class="active">Home</a></li>
                @guest
                    <li><a href="/register">Registration</a></li>
                    <li><a href="/login">Login</a>
                @else
                    <li><a class="text-white" href="/profile/{{ Auth::user()->id }}" role="button">{{ Auth::user()->name }}'s Profile</a></li>
                    <li class="text-white">
                        <div class="dropdown mr-5">
                            <button class="site-btn dropdown-toggle" style="padding: 10px 10px" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              Loans
                            </button>
                            <div class="dropdown-menu" style="z-index: 999;" aria-labelledby="dropdownMenuButton">
                              <a class="dropdown-item" style="color: #262626" href="/business_loan">Business Loan</a>
                              <a class="dropdown-item" style="color: #262626" href="/emp_loan">Employee Loan</a>
                              <a class="dropdown-item" style="color: #262626" href="/edu_loan">Educational Loan</a>
                            </div>
                          </div>
                    </li>
                    @if (Auth::user()->checkAdmin())
                        <li><a href="/admin" class="text-white">Admin</a></li>
                    @endif
                    <li><a href="/withdraw" class="text-white">Withdraw</a></li>
                    <li><a class="text-white" href="/logout" role="submit">Logout</a></li>
                    <li><a class="text-white" href="/dashboard" class="text-white">Dashboard</a></li>
                @endguest
			</ul>
			<div class="header-right">
				<a href="#" class="hr-btn"><i class="flaticon-029-telephone-1"></i>Call us now!</a>
				<div class="hr-btn hr-btn-2">+880 1772974123</div>
			</div>
		</nav>
	</header>
	<!-- Header Section end -->

    <main>
        @yield('content')
    </main>

	<!--====== Javascripts & Jquery ======-->
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
	<script src="{{ asset('js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('js/jquery.slicknav.min.js') }}"></script>
	<script src="{{ asset('js/owl.carousel.min.js') }}"></script>
	<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>

	</body>
</html>

