<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers; 
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ElectricPoleController;
use App\Http\Controllers\Api\LampuController;
use App\Http\Controllers\Api\IotController;
use App\Http\Controllers\Api\CctvController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Electric Poles - Tiang Listrik
    Route::resource('electric-poles', ElectricPoleController::class)->except(['create', 'edit']);
    
    // Lampu
    Route::resource('lampus', LampuController::class)->except(['create', 'edit']);

    // IoT
    Route::resource('iots', IotController::class)->except(['create', 'edit']);
    
    // CCTV
    Route::resource('cctvs', CctvController::class)->except(['create', 'edit']);
});