<?php

use App\Http\Controllers\ProfileController;
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

Route::controller(UserAuthController::class)->group(function () {
    Route::post('/login-user','LoginUser');
    Route::post('/send-otp', 'SendOTP');
    Route::post('/verify-otp', 'VerifyOTP');

    Route::post('/create-user', 'CreateUser')->middleware(HasUser::class);
    Route::post('/reset-password', 'ResetPassword')->middleware('auth:sanctum');
    Route::post('/logout', 'LogoutUser')->middleware('auth:sanctum');
});

Route::controller(ProfileController::class)->group(function () {
    
    Route::get('/profile', 'UserProfile')->middleware('auth:sanctum');
    Route::post('/update-profile', 'UpdateProfile')->middleware('auth:sanctum');
});
