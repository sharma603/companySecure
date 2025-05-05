<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserModalController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SubUserController;

// Guest routes (for non-authenticated users)
Route::middleware(['guest'])->group(function () {
     // Login routes - keep both for compatibility
    Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserLoginController::class, 'login'])->name('login.submit');
    
    // Registration routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Protected routes (for authenticated users)
Route::middleware(['auth'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Company routes
    Route::get('/add-company', function () {
        return view('add-company');
    })->name('add-company');

    Route::resource('companies', CompanyController::class);
    Route::get('/companies/{company}/dashboard', [CompanyController::class, 'dashboard'])->name('companies.dashboard');

    // Notes routes - define before other resource routes
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::get('/notes/{note}', [NoteController::class, 'show'])->name('notes.show');
    Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // Logout route
    Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');

    // Reminder Routes
    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders.index');
    Route::get('/reminders/create', [ReminderController::class, 'create'])->name('reminders.create');
    Route::post('/reminders', [ReminderController::class, 'store'])->name('reminders.store');
    Route::get('/reminders/{reminder}/edit', [ReminderController::class, 'edit'])->name('reminders.edit');
    Route::put('/reminders/{reminder}', [ReminderController::class, 'update'])->name('reminders.update');
    Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');

    // Role & Permission Management
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('role:admin');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('role:admin');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('role:admin');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('role:admin');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('role:admin');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('role:admin');

    // Permission Management
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('role:admin');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create')->middleware('role:admin');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store')->middleware('role:admin');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit')->middleware('role:admin');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update')->middleware('role:admin');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy')->middleware('role:admin');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('role:admin');
    
    // Sub-User Management
    Route::get('/users/create-sub', [SubUserController::class, 'create'])->name('users.create.sub');
    Route::post('/users/create-sub', [SubUserController::class, 'store'])->name('users.store.sub');
    
    // User Modal Creation
    Route::post('/users/modal-create', [UserModalController::class, 'store'])->name('users.modal.store')->middleware('role:admin');
    
    // Other User routes with parameters
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('role:admin');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('role:admin');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('role:admin');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('role:admin');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('role:admin');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('role:admin');
});

// Test route - should show the same view without controller
Route::get('/test-sub-user', function () {
    return view('roles.create_sub_user');
})->name('test.sub.user');

// Redirect root to login or dashboard based on auth status
Route::get('/', function () {
    // Avoid potential redirect loops by directly returning views
    if (Auth::guard('register')->check() || Auth::guard('web')->check()) {
        return redirect()->route('dashboard');
    } else {
        // Return the login view directly instead of redirect
        return view('auth.login');
    }
});

// update 2