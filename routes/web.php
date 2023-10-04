<?php

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

Route::get('/', function () {
    return view('welcome');
});


// Authentication Routes
Route::get('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::get('/confirm/{token}', [\App\Http\Controllers\AuthController::class, 'confirm'])->name('confirm');
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

//Password reset Routes
Route::get('/forget-password', [\App\Http\Controllers\ForgotPasswordController::class, 'forgetPassword'])->name('password.forget');
Route::get('/reset-password', [\App\Http\Controllers\ForgotPasswordController::class, 'resetPassword'])->name('password.reset');
