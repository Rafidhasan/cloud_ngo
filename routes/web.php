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

use App\Http\Controllers\UserRecordController;

Route::get('/', [UserRecordController::class, 'index']);

// USer Registraion routes
Route::get('/registration', [UserRecordController::class, 'create']);
Route::post('/registration', [UserRecordController::class, 'store']);
