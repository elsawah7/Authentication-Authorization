<?php

use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\UpdateProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\VerifyAcountController;
use App\Http\Controllers\Auth\MagicLoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Models\Session;

Route::view('/', 'index');
//Registration Routes
Route::view('/register', 'auth.register')->name('register');
Route::post('/register', RegisterController::class);

//Login Routes
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', LoginController::class);

//Login Without Password Routes
Route::view('/login/magic', 'auth.passwordless-login')->name('login.magic');
Route::post('/login/magic', [MagicLoginController::class, 'sendMagicLink']);
Route::get('/login/magic/{user}', [MagicLoginController::class, 'loginHandler'])->name('login.magic.handler');

//Social OAuth Routes
Route::get('/auth/{driver}/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{driver}/callback', [SocialAuthController::class, 'callback']);

//Password Reset Routes
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
Route::post('/forgot-password', ForgotPasswordController::class)->name('password.email');

Route::view('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
Route::post('/reset-password', ResetPasswordController::class)->name('password.update');

//Acount Verification Routes
Route::view('/verify-account/{identifier}', 'auth.verify-account')->name('account.verify');
Route::post('/verify-account', [VerifyAcountController::class, 'verifyOtp']);
Route::post('/send-verification-otp', [VerifyAcountController::class, 'sendOtp'])->name('account.resend-otp');

//Profile Routes
Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::view('profile', 'auth.profile');
    Route::put('profile', UpdateProfileController::class)->name('profile');
    Route::post('change-password', ChangePasswordController::class);
    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');
    Route::post('logout/{session}', [LogoutController::class, 'logoutDevice'])->name('logout_device');

    //PAGES ROUTE
    Route::view('/student', 'pages.student')->middleware('role:student');
    Route::view('/teacher', 'pages.teacher')->middleware('role:teacher');
    Route::view('/admin', 'pages.admin')->middleware('role:admin');
});
Route::get('users', [UserController::class, 'index']);
Route::get('users/{user}/change-role', [UserController::class, 'changeRole']);
Route::resource('roles', RolesController::class);
