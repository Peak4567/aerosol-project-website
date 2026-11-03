<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SensorApiController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SimulatorController;
use App\Http\Controllers\SmartfarmController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::middleware(['check.maintenance'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/simulator', [SimulatorController::class, 'index'])->name('simulator');
});

Route::get('/maintenance', [HomeController::class, 'maintenance'])->name('maintenance');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated dashboard routes
Route::middleware(['auth', 'check.maintenance'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/smartfarm', [SmartfarmController::class, 'index'])->name('smartfarm');

    // Admin only routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingsController::class, 'update']);
        Route::get('/users', [UsersController::class, 'index'])->name('users');
        Route::post('/users', [UsersController::class, 'store']);
        Route::match(['put', 'patch'], '/users/{id}', [UsersController::class, 'update']);
        Route::delete('/users/{id}', [UsersController::class, 'destroy']);
    });
});

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/health', [SensorApiController::class, 'health']);
    Route::get('/settings', [SensorApiController::class, 'settings']);
    Route::get('/sensors', [SensorApiController::class, 'index']);
    Route::post('/sensor-data', [SensorApiController::class, 'store']);
    Route::get('/simulate', [SensorApiController::class, 'simulate']);

    // API Auth
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Authenticated API routes
    Route::middleware(['auth'])->group(function () {
        Route::middleware(['admin'])->group(function () {
            Route::post('/settings', [SettingsController::class, 'updateApi']);
            Route::get('/users', [UsersController::class, 'index']);
            Route::post('/users', [UsersController::class, 'store']);
            Route::match(['put', 'patch'], '/users/{id}', [UsersController::class, 'update']);
            Route::delete('/users/{id}', [UsersController::class, 'destroy']);
        });
    });
});
