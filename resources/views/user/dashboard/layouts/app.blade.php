<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <title>GNO</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/dashboard/">

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
  </head>

  <body>
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
        <div class="row">
            <div class="col-md-4">
                <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Company name</a>
            </div>
            <div class="col-md-5">
            </div>
        </div>
      <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="text-white" href="/logout" role="submit">Logout</a>
        </li>
        <li class="nav-item text-nowrap">
            <a onclick="myFunction()" class="text-white" id="mobile_menu" style="display: none; font-size: 1.5em">Menu</a>
        </li>
      </ul>
    </nav>

    <div id="trigger" style="display: none; background-color: #262626;">
        <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link active text-white" href="/dashboard">
                <span data-feather="home"></span>
                Dashboard <span class="sr-only">(current)</span>
              </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="/user/savings">
                  <span data-feather="shopping-cart"></span>
                  Savings
                </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link text-white" href="/user/accounts">
                    <span data-feather="shopping-cart"></span>
                    Accounts
                  </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="/loans/{{ Auth::user()->id }}">
                  <span data-feather="layers"></span>
                  Loans
                </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link text-white" href="/approveLoans">
                    <span data-feather="layers"></span>
                    Approve Loans as Garauantor
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white" href="/garantor_list">
                    <span data-feather="layers"></span>
                    Garantor
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white" href="/">
                    <span data-feather="layers"></span>
                    Save Money
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white" href="/withdraws">
                    <span data-feather="layers"></span>
                    Withdraw money
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white" href="/">
                    <span data-feather="layers"></span>
                    Home Page
                  </a>
                </li>
            </ul>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>Member Transactions</span>
              <a class="d-flex align-items-center text-muted text-white" href="#">
                <span data-feather="plus-circle"></span>
              </a>
            </h6>
            <ul class="nav flex-column mb-2">
              <li class="nav-item">
                <a class="nav-link text-white" href="/admin/service_charge">
                  <span data-feather="file-text"></span>
                  Service Charge
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="#">
                  <span data-feather="file-text"></span>
                  Last quarter
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="#">
                  <span data-feather="file-text"></span>
                  Social engagement
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="#">
                  <span data-feather="file-text"></span>
                  Year-end sale
                </a>
              </li>
        </ul>
    </div>

    <script>
        var x = document.getElementById("mobile_menu");

        if(window.innerWidth < 766) {
            x.style.display = "block";
        }

        function myFunction() {
            var ex = document.getElementById("trigger");

            if(ex.style.display == "none") {
                ex.style.display = "block";
            }   else {
                ex.style.display = "none";
            }
        }
    </script>

    <main>
        @if (session('status'))
        <div class="alert alert-primary" role="alert" style="z-index: 9999">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3>{{ session('status') }}</h3>
        </div>
        @endif
        <div class="container-fluid">
            <div class="row">
              <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                  <ul class="nav flex-column">
                    <li class="nav-item">
                      <a class="nav-link active" href="/dashboard">
                        <span data-feather="home"></span>
                        Dashboard <span class="sr-only">(current)</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="/user/savings">
                        <span data-feather="shopping-cart"></span>
                        Savings
                      </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/user/accounts">
                          <span data-feather="shopping-cart"></span>
                          Accounts
                        </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="/loans/{{ Auth::user()->id }}">
                        <span data-feather="layers"></span>
                        Loans
                      </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/approveLoans">
                          <span data-feather="layers"></span>
                          Approve Loans as Garauantor
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="/garantor_list">
                          <span data-feather="layers"></span>
                          Garantor
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="/">
                          <span data-feather="layers"></span>
                          Save Money
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="/withdraws">
                          <span data-feather="layers"></span>
                          Withdraw money
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="/">
                          <span data-feather="layers"></span>
                          Home Page
                        </a>
                      </li>
                  </ul>

                  <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Member Transactions</span>
                    <a class="d-flex align-items-center text-muted" href="#">
                      <span data-feather="plus-circle"></span>
                    </a>
                  </h6>
                  <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                      <a class="nav-link" href="/admin/service_charge">
                        <span data-feather="file-text"></span>
                        Service Charge
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#">
                        <span data-feather="file-text"></span>
                        Last quarter
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#">
                        <span data-feather="file-text"></span>
                        Social engagement
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#">
                        <span data-feather="file-text"></span>
                        Year-end sale
                      </a>
                    </li>
                  </ul>
                </div>
              </nav>
        @yield('content')
    </main>

    	<!-- Footer Section -->
	   <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>

    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    <script>
      feather.replace()
    </script>
  </body>
</html>
