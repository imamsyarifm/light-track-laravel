<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\LampuController;
use App\Http\Controllers\Admin\ElectricPoleController;


Route::middleware('guest')->get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('content.dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

Route::resource('lampu', LampuController::class);
Route::resource('tiang-lampu', ElectricPoleController::class);

