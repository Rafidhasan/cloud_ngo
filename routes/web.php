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

Route::get('/', function () {
    return view('index');
});

Auth::routes();

// user authetication routes
Route::get('/register', 'UserRecordController@create');
Route::post('/register', 'UserRecordController@store');
Route::get('/login', function() {
    return view('login.index');
});
Route::get('/logout', 'UserRecordController@logout');

Route::post('/login','UserRecordController@login');

//user profile routes
Route::get('/profile/{id}','UserRecordController@show');
Route::patch('/update/{id}', 'UserRecordController@update');

//saving form routes
Route::post('/saving/{id}', 'SavingAcountController@store');
Route::post('/saving', 'SavingAcountController@store');
