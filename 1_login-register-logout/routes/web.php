<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

Route::view('/', 'index');
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');


Route::post('/register', RegisterController::class);
Route::post('/login', LoginController::class);
Route::middleware('auth')->group(function () {
    Route::view('profile', 'auth.profile');
    Route::post('logout', LogoutController::class)->name('logout');
});
