<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/aacounts/registration', [AccountController::class, 'registration'])->name('accounts.registration');
Route::post('/accounts/process-register', [AccountController::class, 'processRegistration'])->name('accounts.processRegistration');
Route::get('/aacounts/login', [AccountController::class, 'login'])->name('accounts.login');

