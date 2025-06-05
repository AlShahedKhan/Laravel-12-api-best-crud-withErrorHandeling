<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\BlogController;

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LogoutController::class, 'logout']);

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/get-all-contact', [ContactController::class, 'index'])->name('contact.index');
    Route::get('/get-contact/{contact}', [ContactController::class, 'show'])->name('contact.show');
});

Route::match(['post', 'put', 'patch', 'HEAD', 'OPTIONS', 'DELETE', 'GET'], 'blogs/{id?}', [BlogController::class, 'getAllOrOneOrDestroy']);
Route::match(['post', 'put', 'patch', 'HEAD', 'OPTIONS'], 'blogs/{id?}', [BlogController::class, 'storeOrUpdate']);
