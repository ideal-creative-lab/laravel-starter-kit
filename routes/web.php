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
})->name('home');
// Authentication Routes
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::get('/confirm/{token}', [\App\Http\Controllers\AuthController::class, 'confirm'])->name('confirm');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::view('login','auth.login')->name('login.form');
Route::view('register','auth.register')->name('register.form');
Route::view('password-forget','auth.password-forget')->name('password.forget.form');
Route::view('password-reset/{token}','auth.password-reset')->name('password.reset.form');
