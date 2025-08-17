<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Main dashboard route
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Keep the original welcome route for reference
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');
