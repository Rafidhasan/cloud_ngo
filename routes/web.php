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
Route::get('/admin/showUsers', 'AdminController@showUsers')->middleware('admin');
Route::get('/userInfo/{id}','AdminController@showSingleUser')->middleware('admin');

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
Route::get('/admin/showUser/{id}', 'AdminController@editUser')->middleware('admin');
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

Route::get('/admin/showGProfile/{number}', 'AdminController@showGProfile')->middleware('admin');

//admin Approved Loans routes
Route::get('admin/approvedLoans', 'AdminController@approvedLoans')->middleware('admin');

//service charge routes
Route::get('/admin/service_charge', 'AdminController@showServiceCharge')->middleware('admin');

Route::get('/', 'UserRecordController@index');

Auth::routes();

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
Route::get('/business_loan','LoanController@businessLoanIndex');
Route::post('/business_loan/{id}','LoanController@businessLoanCreate');

Route::get('/emp_loan', 'LoanController@employeeLoanIndex');
Route::post('/emp_loan/{id}', 'LoanController@employeeLoanCreate');

Route::get('/edu_loan', 'LoanController@educationLoanIndex');
Route::post('/edu_loan/{id}', 'LoanController@educationLoanCreate');

// user dashboard show loan routes
Route::get('/loans/{id}', 'userDashboard@showLoans');

// Gaurantor loan routes
Route::get('/g_loan/business/accept/{id}/{token}', 'userDashboard@g_acceptB');
Route::get('/g_loan/emp/accept/{id}/{token}', 'userDashboard@g_acceptEm');
Route::get('/g_loan/edu/accept/{id}/{token}', 'userDashboard@g_acceptEd');
Route::get('/g_loan/reject/{id}', 'userDashboard@g_reject');

// user fill Loan installment form routes
Route::get('/loans/singleShowLoanEdu/{id}/{token}', 'LoanInstallmentController@eduLoanInstallmentIndex');
Route::get('/loans/singleShowLoanEmployee/{id}/{token}', 'LoanInstallmentController@employeeLoanInstallmentIndex');
Route::get('/loans/singleShowLoanBusiness/{id}/{token}', 'LoanInstallmentController@businessLoanInstallmentIndex');

Route::post('/loan_installment/{id}/{token}', 'LoanInstallmentController@create');

Route::post('/first_loan_installment/{id}/{token}/{month}/{total}', "LoanInstallmentController@firstStore");
Route::get('/prev_loan_details/{id}', 'LoanInstallmentController@showPrevLoanInstallments');
