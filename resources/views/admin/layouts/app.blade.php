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
    <link href="{{ asset('css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
  </head>

  <body>
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
      <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Company name</a>
      <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
      <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="text-white" href="/logout" role="submit">Logout</a>
        </li>
      </ul>
    </nav>


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
                      <a class="nav-link active" href="/admin">
                        <span data-feather="home"></span>
                        Dashboard <span class="sr-only">(current)</span>
                      </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/withdraw">
                        <span data-feather="file"></span>
                          Withdraw
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/showUsers">
                          <span data-feather="users"></span>
                          Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/registerUsers">
                          <span data-feather="users"></span>
                          Member Register
                        </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="/admin/approveSavings">
                        <span data-feather="file"></span>
                        Approve Savings
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="/admin/savings">
                        <span data-feather="shopping-cart"></span>
                        Savings
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="/admin/accounts">
                        <span data-feather="bar-chart-2"></span>
                        Accounts
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="/admin/loans">
                        <span data-feather="layers"></span>
                        Loans
                      </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/approvedLoans">
                          <span data-feather="layers"></span>
                          Approved Loans
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="/admin/loanInstallments">
                          <span data-feather="layers"></span>
                          Loan Installments
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="/admin/withdraw">
                          <span data-feather="layers"></span>
                          Approved Withdraws
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="/admin/forgetPass">
                          <span data-feather="layers"></span>
                          Forget Pass
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
    <script>window.jQuery || document.write('<script src="<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js" integrity="sha512-/DXTXr6nQodMUiq+IUJYCt2PPOUjrHJ9wFrqpJ3XkgPNOZVfMok7cRw6CSxyCQxXn6ozlESsSh1/sMCTF1rL/g==" crossorigin="anonymous"></script>"><\/script>')</script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>

    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    <script>
      feather.replace()
    </script>

    <!-- Graphs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script>
      var ctx = document.getElementById("myChart");
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
          datasets: [{
            data: [15339, 21345, 18483, 24003, 23489, 24092, 12034],
            lineTension: 0,
            backgroundColor: 'transparent',
            borderColor: '#007bff',
            borderWidth: 4,
            pointBackgroundColor: '#007bff'
          }]
        },
        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: false
              }
            }]
          },
          legend: {
            display: false,
          }
        }
      });
    </script>
  </body>
</html>
