<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Grouped under /accounts prefix
Route::prefix('accounts')->group(function () {

    // 🔹 Guest routes (only for users who are NOT logged in)
    Route::middleware('guest')->group(function () {
        Route::get('registration', [AccountController::class, 'registration'])->name('accounts.registration');
        Route::post('process-register', [AccountController::class, 'processRegistration'])->name('accounts.processRegistration');

        Route::get('login', [AccountController::class, 'login'])->name('accounts.login');
        Route::post('authenticate', [AccountController::class, 'authenticate'])->name('accounts.authenticate');
    });

    // 🔹 Authenticated routes (only for logged-in users)
    Route::middleware('auth')->group(function () {
        Route::get('profile', [AccountController::class, 'profile'])->name('accounts.profile');
        Route::get('logout', [AccountController::class, 'logout'])->name('accounts.logout');
    });
});
