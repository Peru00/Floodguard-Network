<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonorController;

// Home routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Donor routes
    Route::prefix('donor')->name('donor.')->group(function () {
        Route::get('/dashboard', [DonorController::class, 'dashboard'])->name('dashboard');
        Route::post('/submit-donation', [DonorController::class, 'submitDonation'])->name('submit-donation');
        Route::get('/donations', [DonorController::class, 'donations'])->name('donations');
        Route::get('/donation/{id}', [DonorController::class, 'viewDonation'])->name('view-donation');
        Route::get('/distribution-repository', [DonorController::class, 'distributionRepository'])->name('distribution-repository');
    });
});