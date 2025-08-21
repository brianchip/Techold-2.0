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
Route::get('/projects/{project}/costing', [ProjectController::class, 'costing'])->name('projects.costing');
Route::get('/projects/{project}/export-pdf', [ProjectController::class, 'exportPdf'])->name('projects.export-pdf');
Route::post('/projects/{project}/share', [ProjectController::class, 'share'])->name('projects.share');
Route::post('/projects/{project}/submit-approval', [ProjectController::class, 'submitApproval'])->name('projects.submit-approval');
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

// Budget Lines Routes
Route::post('/budget-lines', [App\Http\Controllers\BudgetLineController::class, 'store'])->name('budget-lines.store');
Route::put('/budget-lines/{budgetLine}', [App\Http\Controllers\BudgetLineController::class, 'update'])->name('budget-lines.update');
Route::delete('/budget-lines/{budgetLine}', [App\Http\Controllers\BudgetLineController::class, 'destroy'])->name('budget-lines.destroy');

// Documents Routes
Route::post('/documents', [App\Http\Controllers\DocumentController::class, 'store'])->name('documents.store');
Route::get('/documents/{document}/view', [App\Http\Controllers\DocumentController::class, 'view'])->name('documents.view');
Route::get('/documents/{document}/download', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');
Route::delete('/documents/{document}', [App\Http\Controllers\DocumentController::class, 'destroy'])->name('documents.destroy');

// Risks Routes
Route::post('/risks', [App\Http\Controllers\RiskController::class, 'store'])->name('risks.store');
Route::put('/risks/{risk}', [App\Http\Controllers\RiskController::class, 'update'])->name('risks.update');
Route::delete('/risks/{risk}', [App\Http\Controllers\RiskController::class, 'destroy'])->name('risks.destroy');

// Quotes Routes
Route::post('/quotes', [App\Http\Controllers\QuoteController::class, 'store'])->name('quotes.store');
Route::put('/quotes/{quote}', [App\Http\Controllers\QuoteController::class, 'update'])->name('quotes.update');
Route::delete('/quotes/{quote}', [App\Http\Controllers\QuoteController::class, 'destroy'])->name('quotes.destroy');

// BOQ Management Routes
Route::prefix('projects/{project}/boq')->group(function () {
    Route::get('/sections', [App\Http\Controllers\BOQSectionController::class, 'index'])->name('boq.sections.index');
    Route::post('/sections', [App\Http\Controllers\BOQSectionController::class, 'store'])->name('boq.sections.store');
    Route::get('/sections/{section}', [App\Http\Controllers\BOQSectionController::class, 'show'])->name('boq.sections.show');
    Route::put('/sections/{section}', [App\Http\Controllers\BOQSectionController::class, 'update'])->name('boq.sections.update');
    Route::delete('/sections/{section}', [App\Http\Controllers\BOQSectionController::class, 'destroy'])->name('boq.sections.destroy');
    Route::post('/sections/reorder', [App\Http\Controllers\BOQSectionController::class, 'reorder'])->name('boq.sections.reorder');
    Route::get('/export', [App\Http\Controllers\BOQSectionController::class, 'export'])->name('boq.export');
});

Route::prefix('boq/sections/{section}/items')->group(function () {
    Route::post('/', [App\Http\Controllers\BOQItemController::class, 'store'])->name('boq.items.store');
    Route::put('/{item}', [App\Http\Controllers\BOQItemController::class, 'update'])->name('boq.items.update');
    Route::delete('/{item}', [App\Http\Controllers\BOQItemController::class, 'destroy'])->name('boq.items.destroy');
    Route::post('/{item}/approve', [App\Http\Controllers\BOQItemController::class, 'approve'])->name('boq.items.approve');
    Route::post('/bulk-update', [App\Http\Controllers\BOQItemController::class, 'bulkUpdate'])->name('boq.items.bulk-update');
});

// Milestone Management Routes
Route::prefix('projects/{project}/milestones')->group(function () {
    Route::get('/', [App\Http\Controllers\MilestoneController::class, 'index'])->name('milestones.index');
    Route::post('/', [App\Http\Controllers\MilestoneController::class, 'store'])->name('milestones.store');
    Route::put('/{milestone}', [App\Http\Controllers\MilestoneController::class, 'update'])->name('milestones.update');
    Route::delete('/{milestone}', [App\Http\Controllers\MilestoneController::class, 'destroy'])->name('milestones.destroy');
    Route::post('/{milestone}/progress', [App\Http\Controllers\MilestoneController::class, 'updateProgress'])->name('milestones.progress');
    Route::post('/{milestone}/complete', [App\Http\Controllers\MilestoneController::class, 'markCompleted'])->name('milestones.complete');
    Route::get('/upcoming', [App\Http\Controllers\MilestoneController::class, 'upcoming'])->name('milestones.upcoming');
    Route::get('/overdue', [App\Http\Controllers\MilestoneController::class, 'overdue'])->name('milestones.overdue');
});

// BOQ Version Management Routes
Route::prefix('projects/{project}/boq/versions')->group(function () {
    Route::get('/', [App\Http\Controllers\BOQVersionController::class, 'index'])->name('boq.versions.index');
    Route::post('/', [App\Http\Controllers\BOQVersionController::class, 'store'])->name('boq.versions.store');
    Route::get('/{version}', [App\Http\Controllers\BOQVersionController::class, 'show'])->name('boq.versions.show');
    Route::put('/{version}', [App\Http\Controllers\BOQVersionController::class, 'update'])->name('boq.versions.update');
    Route::delete('/{version}', [App\Http\Controllers\BOQVersionController::class, 'destroy'])->name('boq.versions.destroy');
    Route::post('/{version}/make-current', [App\Http\Controllers\BOQVersionController::class, 'makeCurrent'])->name('boq.versions.make-current');
    Route::post('/{version}/restore', [App\Http\Controllers\BOQVersionController::class, 'restore'])->name('boq.versions.restore');
    Route::get('/{version1}/compare/{version2}', [App\Http\Controllers\BOQVersionController::class, 'compare'])->name('boq.versions.compare');
    Route::get('/{version}/export', [App\Http\Controllers\BOQVersionController::class, 'export'])->name('boq.versions.export');
});

// BOQ Approval Management Routes
Route::prefix('projects/{project}/boq/approvals')->group(function () {
    Route::get('/', [App\Http\Controllers\BOQApprovalController::class, 'index'])->name('boq.approvals.index');
    Route::post('/submit', [App\Http\Controllers\BOQApprovalController::class, 'submit'])->name('boq.approvals.submit');
    Route::get('/workflow', [App\Http\Controllers\BOQApprovalController::class, 'workflow'])->name('boq.approvals.workflow');
    Route::get('/history', [App\Http\Controllers\BOQApprovalController::class, 'history'])->name('boq.approvals.history');
    Route::get('/dashboard', [App\Http\Controllers\BOQApprovalController::class, 'dashboard'])->name('boq.approvals.dashboard');
});

Route::prefix('boq/approvals/{approval}')->group(function () {
    Route::post('/approve', [App\Http\Controllers\BOQApprovalController::class, 'approve'])->name('boq.approvals.approve');
    Route::post('/reject', [App\Http\Controllers\BOQApprovalController::class, 'reject'])->name('boq.approvals.reject');
    Route::post('/request-revision', [App\Http\Controllers\BOQApprovalController::class, 'requestRevision'])->name('boq.approvals.request-revision');
});

// Global approval routes
Route::get('/boq/approvals/pending', [App\Http\Controllers\BOQApprovalController::class, 'pending'])->name('boq.approvals.pending');

// BOQ Sequence Number API
Route::get('/api/projects/{project}/next-boq-sequence', [App\Http\Controllers\BOQVersionController::class, 'getNextSequence'])->name('api.boq.next-sequence');



// BOQ Library Management Routes
Route::prefix('boq/library')->group(function () {
    Route::get('/', [App\Http\Controllers\BOQLibraryController::class, 'index'])->name('boq.library.index');
    Route::post('/', [App\Http\Controllers\BOQLibraryController::class, 'store'])->name('boq.library.store');
    Route::get('/categories', [App\Http\Controllers\BOQLibraryController::class, 'categories'])->name('boq.library.categories');
    Route::get('/popular', [App\Http\Controllers\BOQLibraryController::class, 'popular'])->name('boq.library.popular');
    Route::get('/templates', [App\Http\Controllers\BOQLibraryController::class, 'templates'])->name('boq.library.templates');
    Route::get('/prices-need-update', [App\Http\Controllers\BOQLibraryController::class, 'pricesNeedUpdate'])->name('boq.library.prices-need-update');
    Route::get('/export', [App\Http\Controllers\BOQLibraryController::class, 'export'])->name('boq.library.export');
});

Route::prefix('boq/library/{item}')->group(function () {
    Route::get('/', [App\Http\Controllers\BOQLibraryController::class, 'show'])->name('boq.library.show');
    Route::put('/', [App\Http\Controllers\BOQLibraryController::class, 'update'])->name('boq.library.update');
    Route::delete('/', [App\Http\Controllers\BOQLibraryController::class, 'destroy'])->name('boq.library.destroy');
    Route::post('/add-to-boq', [App\Http\Controllers\BOQLibraryController::class, 'addToBOQ'])->name('boq.library.add-to-boq');
    Route::post('/duplicate', [App\Http\Controllers\BOQLibraryController::class, 'duplicate'])->name('boq.library.duplicate');
    Route::put('/update-price', [App\Http\Controllers\BOQLibraryController::class, 'updatePrice'])->name('boq.library.update-price');
});

// Resources Routes
Route::get('/resources', [App\Http\Controllers\ResourceController::class, 'index'])->name('resources.index');
Route::post('/resources', [App\Http\Controllers\ResourceController::class, 'store'])->name('resources.store');
Route::put('/resources/{resource}', [App\Http\Controllers\ResourceController::class, 'update'])->name('resources.update');
Route::delete('/resources/{resource}', [App\Http\Controllers\ResourceController::class, 'destroy'])->name('resources.destroy');
Route::get('/api/resources/available', [App\Http\Controllers\ResourceController::class, 'getAvailableResources'])->name('resources.available');
Route::get('/api/tasks/{task}/resources', [App\Http\Controllers\ResourceController::class, 'getTaskResources'])->name('tasks.resources');
Route::get('/api/projects/{project}/resources/summary', [App\Http\Controllers\ResourceController::class, 'getProjectResourceSummary'])->name('projects.resources.summary');



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
