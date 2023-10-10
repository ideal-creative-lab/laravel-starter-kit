<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::get('/confirm/{token}', [\App\Http\Controllers\AuthController::class, 'confirm'])->name('confirm');
Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::view('login','auth.login')->name('login.form');
Route::view('register','auth.register')->name('register.form');
Route::view('password-forget','auth.password-forget')->name('password.forget.form');
Route::view('password-reset/{token}','auth.password-reset')->name('password.reset.form');
