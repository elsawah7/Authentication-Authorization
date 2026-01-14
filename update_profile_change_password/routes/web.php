<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\UpdateProfileController;




Route::view('/', 'index');
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');


Route::post('/register', RegisterController::class);
Route::post('/login', LoginController::class);
Route::middleware('auth')->group(function () {
    Route::view('profile', 'auth.profile');
    Route::put('profile', UpdateProfileController::class)->name('profile');
    Route::post('change-password', ChangePasswordController::class);
    Route::post('logout', LogoutController::class)->name('logout');
});
