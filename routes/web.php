<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\AdminController;

// Home routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/user-management', [AdminController::class, 'userManagement'])->name('user-management');
        Route::post('/create-user', [AdminController::class, 'createUser'])->name('create-user');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
        
        // Relief Camps routes
        Route::get('/relief-camps', [AdminController::class, 'reliefCamps'])->name('relief-camps');
        Route::post('/relief-camps', [AdminController::class, 'createReliefCamp'])->name('relief-camps.create');
        Route::put('/relief-camps/{id}', [AdminController::class, 'updateReliefCamp'])->name('relief-camps.update');
        Route::delete('/relief-camps/{id}', [AdminController::class, 'deleteReliefCamp'])->name('relief-camps.delete');
        
        // Chat routes
        Route::get('/chat/messages/{volunteerId}', [AdminController::class, 'getChatMessages'])->name('chat.messages');
        Route::post('/chat/send', [AdminController::class, 'sendChatMessage'])->name('chat.send');
        Route::get('/chat/volunteers', [AdminController::class, 'getChatVolunteers'])->name('chat.volunteers');
        
        Route::post('/transfer-admin', [AdminController::class, 'transferAdmin'])->name('transfer-admin');
        Route::post('/donation/update-status', [AdminController::class, 'updateDonationStatus'])->name('donation.update-status');
        Route::post('/add-victim', [AdminController::class, 'addVictim'])->name('add-victim');
        Route::post('/add-volunteer', [AdminController::class, 'addVolunteer'])->name('add-volunteer');
        Route::post('/assign-task', [AdminController::class, 'assignTask'])->name('assign-task');
    });
    
    // Donor routes
    Route::prefix('donor')->name('donor.')->group(function () {
        Route::get('/dashboard', [DonorController::class, 'dashboard'])->name('dashboard');
        Route::post('/submit-donation', [DonorController::class, 'submitDonation'])->name('submit-donation');
        Route::get('/donations', [DonorController::class, 'donations'])->name('donations');
        Route::get('/donation/{id}', [DonorController::class, 'viewDonation'])->name('view-donation');
        Route::get('/distribution-repository', [DonorController::class, 'distributionRepository'])->name('distribution-repository');
        Route::get('/profile/edit', [DonorController::class, 'editProfile'])->name('edit-profile');
        Route::post('/profile/update', [DonorController::class, 'updateProfile'])->name('update-profile');
    });
    
    // Volunteer routes
    Route::prefix('volunteer')->name('volunteer.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\VolunteerController::class, 'dashboard'])->name('dashboard');
        Route::post('/toggle-availability', [App\Http\Controllers\VolunteerController::class, 'toggleAvailability'])->name('toggle-availability');
        Route::post('/complete-task', [App\Http\Controllers\VolunteerController::class, 'completeTask'])->name('complete-task');
        Route::get('/profile/edit', [App\Http\Controllers\VolunteerController::class, 'editProfile'])->name('edit-profile');
        Route::post('/profile/update', [App\Http\Controllers\VolunteerController::class, 'updateProfile'])->name('update-profile');
        Route::get('/relief-camps', [App\Http\Controllers\VolunteerController::class, 'reliefCamps'])->name('relief-camps');
        Route::post('/relief-camps/update-occupancy', [App\Http\Controllers\VolunteerController::class, 'updateCampOccupancy'])->name('update-camp-occupancy');
        Route::post('/relief-camps/generate-report', [App\Http\Controllers\VolunteerController::class, 'generateCampReport'])->name('generate-camp-report');
        Route::get('/relief-camps/download-report/{camp_id}', [App\Http\Controllers\VolunteerController::class, 'downloadCampReport'])->name('download-camp-report');
        Route::get('/distribution-repository', [App\Http\Controllers\VolunteerController::class, 'distributionRepository'])->name('distribution-repository');
        Route::get('/inventory', [App\Http\Controllers\VolunteerController::class, 'inventory'])->name('inventory');
        Route::post('/inventory', [App\Http\Controllers\VolunteerController::class, 'storeInventory'])->name('inventory.store');
        Route::delete('/inventory/{id}', [App\Http\Controllers\VolunteerController::class, 'deleteInventory'])->name('inventory.delete');
        Route::get('/victims', [App\Http\Controllers\VolunteerController::class, 'victims'])->name('victims');
        Route::post('/victims', [App\Http\Controllers\VolunteerController::class, 'storeVictim'])->name('victims.store');
        
        // Chat routes
        Route::get('/chat/messages', [App\Http\Controllers\VolunteerController::class, 'getChatMessages'])->name('chat.messages');
        Route::post('/chat/send', [App\Http\Controllers\VolunteerController::class, 'sendChatMessage'])->name('chat.send');
    });
});