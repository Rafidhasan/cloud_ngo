<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//admin routes
Route::get('/admin', 'AdminController@index')->middleware('admin');
Route::get('/admin/registerUsers', 'AdminController@show')->middleware('admin');
Route::get('/admin/showUsers', 'AdminController@showUsers')->middleware('admin');
Route::get('/admin/showSingleUserEditForm/{id}', 'AdminController@showSingleUserEditForm')->middleware('admin');
Route::get('/userInfo/{id}','AdminController@showSingleUser')->middleware('admin');
Route::put('/admin/user/update/{id}', 'AdminController@updateSingleUser')->middleware('admin');
Route::get('/admin/deleteUser/{id}', 'AdminController@deleteUser')->middleware('admin');
Route::get('/admin/accounts', 'AdminController@accounts')->middleware('admin');

//admin forget pass section
Route::get('/admin/forgetPass', 'AdminController@forgetPass')->middleware('admin');
Route::get('/admin/approvePass/{id}', 'AdminController@approvePass')->middleware('admin');
Route::get('/admin/rejectPass/{id}', 'AdminController@rejectPass')->middleware('admin');

//user approve routes
Route::get('/approve/{id}','AdminController@approveSingleUser')->middleware('admin');
Route::get('/reject/{id}','AdminController@rejectSingleUser')->middleware('admin');
Route::get('/deleteSingleUser/{id}', 'AdminController@deleteSingleUser')->middleware('admin');

//admin user record controller routes
Route::get('/adm/approveLoans/showUser/{id}', 'AdminController@editUser')->middleware('admin');
Route::put('/admin/userUpdate/{id}', 'AdminController@updateUser')->middleware('admin');

// admin savings form routes
Route::get('/admin/approve/savings/{id}', 'AdminController@accptSavings')->middleware('admin');
Route::get('/admin/reject/savings/{id}', 'AdminController@rejectSavings')->middleware('admin');
Route::get('/admin/approveSavings', 'AdminController@approveSavings')->middleware('admin');
Route::get('/admin/savings', 'AdminController@savings')->middleware('admin');
Route::get('/admin/savings/edit/{tracking_number}/{id}', 'AdminController@editSavingsIndex')->middleware('admin');
Route::put('/admin/savings/update/{user_id}/{total}/{id}/{track}', 'AdminController@updateSavings')->middleware('admin');
Route::get('/admin/savings/delete/{track}', 'AdminController@deleteSavings')->middleware('admin');

//admin loan routes
Route::get('/admin/loans','AdminController@showLoans')->middleware('admin');
Route::get('/admin/singleShowLoanBusiness/{id}/{token}', 'AdminController@showSingleMemberLoansBusiness')->middleware('admin');
Route::get('/admin/singleShowLoanEmployee/{id}/{token}', 'AdminController@showSingleMemberLoansEmployee')->middleware('admin');
Route::get('/admin/singleShowLoanEdu/{id}/{token}', 'AdminController@showSingleMemberLoansEdu')->middleware('admin');
Route::get('/admin/approveBusinessLoan/loan/{id}/{token}', 'AdminController@approveBusinessLoan')->middleware('admin');
Route::get('/admin/approveEmployeeLoan/loan/{id}/{token}', 'AdminController@approveEmployeeLoan')->middleware('admin');
Route::get('/admin/rejectEmployeeLoan/loan/{id}/{token}', 'AdminController@approveEmployeeLoan')->middleware('admin');
Route::get('/admin/approveEduLoan/loan/{id}/{token}','AdminController@approveEduLoan')->middleware('admin');
Route::get('/admin/rejectEduLoan/loan/{id}/{token}','AdminController@rejectEduLoan')->middleware('admin');

Route::get('/admin/singleShowBusinessLoanEdit/{id}/{token}', 'AdminController@editBusinessLoan')->middleware('admin');
Route::get('/admin/singleShowEmployeeLoanEdit/{id}/{token}', 'AdminController@editEmployeeLoan')->middleware('admin');
Route::get('/admin/singleShowEducationLoanEdit/{id}/{token}', 'AdminController@editEducationLoan')->middleware('admin');

Route::put('/admin/BusinessLoan/update/{id}/{token}', 'AdminController@storeBusinessLoan')->middleware('admin');
Route::put('/admin/EmployeeLoan/update/{id}/{token}', 'AdminController@storeEmployeeLoan')->middleware('admin');
Route::put('/admin/EducationLoan/update/{id}/{token}', 'AdminController@storeEducationLoan')->middleware('admin');

Route::get('/admin/singleShowBusinessLoanDelete/{id}/{token}', 'AdminController@deletebusinessLoan')->middleware('admin');
Route::get('/admin/singleShowEmployeeLoanDelete/{id}/{token}', 'AdminController@deleteEmployeeLoan')->middleware('admin');
Route::get('/admin/singleShowEducationLoanDelete/{id}/{token}', 'AdminController@deleteEducationLoan')->middleware('admin');

Route::get('/admin/showGProfile/{number}', 'AdminController@showGProfile')->middleware('admin');

//admin loan installments routes
Route::get('/admin/loanInstallments', 'LoanInstallmentController@show')->middleware('admin');
Route::get('/admin/loanInstallmentsDetails/{user_id}/{token}', 'LoanInstallmentController@showSingleLoanDetails')->middleware('admin');
Route::get('/admin/loanInstallment/apporve/{tracking_number}/{token}', 'LoanInstallmentController@acceptSingleLoanInstallment')->middleware('admin');

//admin Approved Loans routes
Route::get('/admin/approvedLoans', 'AdminController@approvedLoans')->middleware('admin');

//admin Accounts routes
Route::get('/admin/accounts', 'AdminController@accountsIndex')->middleware('admin');

//admin user withdraw approval
Route::get('/admin/userWithdraw', 'withdrawController@adminApproved')->middleware('admin');
Route::get('/admin/admin/user/withdraw/approve/{id}', 'WithdrawController@accpt');
Route::get('/admin/admin/user/withdraw/reject/{id}', 'WithdrawController@rjct');

//service charge routes
Route::get('/admin/service_charge', 'AdminController@showServiceCharge')->middleware('admin');

//withdraw form routes
Route::get('/admin/withdraw', 'AdminwithdrawController@index')->middleware('admin');
Route::post('/admin/withdraw/create/{id}', 'AdminwithdrawController@store');
Route::get('/admin/withdraws/reject/{id}/{serial}', 'AdminController@rejectWithdraws');
Route::get('/admin/prev-withdraws', 'AdminController@prevWithdraws')->middleware('admin');
Route::get('/admin', 'AdminController@index')->middleware('admin');


//User Routes
Route::get('/', 'UserRecordController@index');
Route::get('/savingsForm', 'UserRecordController@showSavingsForm');

//user accounts routes
Route::get('/user/accounts', 'userRecordController@accounts');

//user garantor list
Route::get('/garantor_list', 'userRecordController@garantorList');

// user show saving
Route::get('/user/savings', 'UserRecordController@showSavings');

//withdraw
Route::get('/withdraws', 'WithdrawController@showUserForm');

// user authetication routes
Route::get('/register', 'UserRecordController@create');
Route::post('/register', 'UserRecordController@store');
Route::get('/login', function() {
    return view('login.index');
});
Route::get('/logout', 'UserRecordController@logout');

Route::post('/login','UserRecordController@login');
Route::get('/forget', 'UserRecordController@forgetPassIndex');
Route::post('/forget', 'UserRecordController@forgetPassStore');
Route::get('/accptApprove/{id}', 'UserRecordController@removeNotification');

//user profile routes
Route::get('/profile/{id}','UserRecordController@show');
Route::patch('/update/{id}', 'UserRecordController@update');

//saving form routes
Route::post('/saving/{id}', 'SavingAcountController@store');
Route::post('/saving', 'SavingAcountController@store');

//user dashboard routes
Route::get('/dashboard', 'userDashboard@index');
Route::get('/approveLoans', 'userDashboard@approvedLoans');

//loan Routes
Route::get('/loans', function() {
    return view('user.loan');
});

Route::get('/business_loan','LoanController@businessLoanIndex');
Route::post('/business_loan/{id}','LoanController@businessLoanCreate');

Route::get('/emp_loan', 'LoanController@employeeLoanIndex');
Route::post('/emp_loan/{id}', 'LoanController@employeeLoanCreate');

Route::get('/edu_loan', 'LoanController@educationLoanIndex');
Route::post('/edu_loan/{id}', 'LoanController@educationLoanCreate');

// user dashboard show loan routes
Route::get('/loans/{id}', 'userDashboard@showLoans');

// Gaurantor loan routes
Route::get('/g_loan/business/accept/{id}/{loan_id}', 'userDashboard@g_acceptB');
Route::get('/g_loan/emp/accept/{id}', 'userDashboard@g_acceptEm');
Route::get('/g_loan/edu/accept/{id}', 'userDashboard@g_acceptEd');
Route::get('/g_loan/reject/{id}', 'userDashboard@g_reject');

// user fill Loan installment form routes
Route::get('/loans/singleShowLoanEdu/{id}', 'LoanInstallmentController@eduLoanInstallmentIndex');
Route::get('/loans/singleShowLoanEmployee/{id}', 'LoanInstallmentController@employeeLoanInstallmentIndex');
Route::get('/loans/singleShowLoanBusiness/{id}/{token}', 'LoanInstallmentController@businessLoanInstallmentIndex');

Route::post('/loan_installment/{id}/{token}', 'LoanInstallmentController@create');

Route::post('/first_loan_installment/{id}/{token}/{month}/{total}/{month_no}', "LoanInstallmentController@firstStore");
Route::get('/prev_loan_details/{id}', 'LoanInstallmentController@showPrevLoanInstallments');

// withdraw form routes
Route::get('/withdraw', 'WithdrawController@index');
Route::post('/withdraw/{id}', 'WithdrawController@create');
