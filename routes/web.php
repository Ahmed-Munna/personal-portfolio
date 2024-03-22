<?php

use App\Http\Controllers\UserAuthController;
use App\Http\Middleware\HasUser;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/registration', 'Auth.registration');
Route::view('/login', 'Auth.login')->name('login');
Route::view('/forget-password', 'Auth.forget-password');

Route::post('/login-user', [UserAuthController::class, 'LoginUser']);
Route::post('/send-otp', [UserAuthController::class, 'SendOTP']);
Route::post('/verify-otp', [UserAuthController::class, 'VerifyOTP']);

Route::post('/create-user', [UserAuthController::class, 'CreateUser'])->middleware(HasUser::class);
Route::post('/reset-password', [UserAuthController::class, 'ResetPassword'])->middleware('auth:sanctum');
Route::post('/logout', [UserAuthController::class, 'LogoutUser'])->middleware('auth:sanctum');
