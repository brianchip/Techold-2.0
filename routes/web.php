<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

// Main dashboard route - use the controller for full functionality
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Project Management Routes
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

// Task Management Routes
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// Resources Management
Route::get('/resources', function () {
    return view('resources.index');
})->name('resources.index');

// Budget Management
Route::get('/budget', function () {
    return view('budget.index');
})->name('budget.index');

// Document Management
Route::get('/documents', function () {
    return view('documents.index');
})->name('documents.index');

// Risk Management
Route::get('/risks', function () {
    return view('risks.index');
})->name('risks.index');

// Reports and Analytics
Route::get('/reports', function () {
    return view('reports.index');
})->name('reports.index');

// Settings
Route::get('/settings', function () {
    return view('settings.index');
})->name('settings.index');

// Keep the original welcome route for reference
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');
