<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\UpdateProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GithubAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\VerifyAcountController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\Auth\FacebookAuthController;


Route::view('/', 'index');
//Registration Routes
Route::view('/register', 'auth.register')->name('register');
Route::post('/register', RegisterController::class);

//Login Routes
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', LoginController::class);

//Google OAuth Routes
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

//GitHub OAuth Routes
Route::get('/auth/github/redirect', [GithubAuthController::class, 'redirect']);
Route::get('/auth/github/callback', [GithubAuthController::class, 'callback']);

//Facebook OAuth Routes
Route::get('/auth/facebook/redirect', [FacebookAuthController::class, 'redirect']);
Route::get('/auth/facebook/callback', [FacebookAuthController::class, 'callback']);

//Password Reset Routes
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
Route::post('/forgot-password', ForgotPasswordController::class)->name('password.email');

Route::view('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
Route::post('/reset-password', ResetPasswordController::class)->name('password.update');

//Email Verification Routes
Route::view('/verify-email/{email}', 'auth.verify-email')->name('email.verify');
Route::post('/verify-email', VerifyAcountController::class);

//Profile Routes
Route::middleware('auth')->group(function () {
    Route::view('profile', 'auth.profile');
    Route::put('profile', UpdateProfileController::class)->name('profile');
    Route::post('change-password', ChangePasswordController::class);
    Route::post('logout', LogoutController::class)->name('logout');
});
