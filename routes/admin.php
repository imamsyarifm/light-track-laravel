<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ElectricPoleController;
use App\Http\Controllers\Admin\LampuController;
use App\Http\Controllers\Admin\IotController;
use App\Http\Controllers\Admin\CctvController;
use App\Http\Controllers\Admin\CmsUserController;
use App\Http\Controllers\Admin\MobileUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return "Admin Dashboard (Backend Only)";
    })->name('dashboard');

    Route::resource('poles', ElectricPoleController::class);
    Route::get('/admin/poles/{id}', [ElectricPoleController::class, 'show'])->name('admin.poles.show');
    
    Route::resource('lampus', LampuController::class);
    Route::resource('iots', IotController::class);
    Route::resource('cctvs', CctvController::class);
    Route::resource('users', UserController::class);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});