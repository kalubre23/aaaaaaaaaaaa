<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
