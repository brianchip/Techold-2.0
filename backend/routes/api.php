<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\BudgetLineController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\RiskController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public dashboard access (for testing/monitoring)
Route::get('/dashboard/overview', [DashboardController::class, 'overview']);
Route::get('/dashboard/stats', [DashboardController::class, 'projectStats']);

// Protected routes (auth:sanctum temporarily disabled until Sanctum is configured)
Route::middleware('api')->group(function () {
    
    // User profile (requires auth setup)
    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });
    
    Route::post('/logout', [AuthController::class, 'logout']);

    // Project Management Routes
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::post('/', [ProjectController::class, 'store']);
        Route::get('/statistics', [ProjectController::class, 'statistics']);
        Route::get('/export', [ProjectController::class, 'export']);
        
        Route::prefix('{project}')->group(function () {
            Route::get('/', [ProjectController::class, 'show']);
            Route::put('/', [ProjectController::class, 'update']);
            Route::delete('/', [ProjectController::class, 'destroy']);
            
            // Project-specific tasks
            Route::get('/tasks', [TaskController::class, 'index']);
            Route::post('/tasks', [TaskController::class, 'store']);
            
            // Project-specific resources
            Route::get('/resources', [ResourceController::class, 'index']);
            Route::post('/resources', [ResourceController::class, 'store']);
            
            // Project-specific budget lines
            Route::get('/budget-lines', [BudgetLineController::class, 'index']);
            Route::post('/budget-lines', [BudgetLineController::class, 'store']);
            
            // Project-specific documents
            Route::get('/documents', [DocumentController::class, 'index']);
            Route::post('/documents', [DocumentController::class, 'store']);
            
            // Project-specific risks
            Route::get('/risks', [RiskController::class, 'index']);
            Route::post('/risks', [RiskController::class, 'store']);
        });
    });

    // Task Management Routes
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'store']);
        
        Route::prefix('{task}')->group(function () {
            Route::get('/', [TaskController::class, 'show']);
            Route::put('/', [TaskController::class, 'update']);
            Route::delete('/', [TaskController::class, 'destroy']);
            
            // Task-specific resources
            Route::get('/resources', [ResourceController::class, 'index']);
            Route::post('/resources', [ResourceController::class, 'store']);
            
            // Task-specific documents
            Route::get('/documents', [DocumentController::class, 'index']);
            Route::post('/documents', [DocumentController::class, 'store']);
        });
    });

    // Resource Management Routes
    Route::prefix('resources')->group(function () {
        Route::get('/', [ResourceController::class, 'index']);
        Route::post('/', [ResourceController::class, 'store']);
        
        Route::prefix('{resource}')->group(function () {
            Route::get('/', [ResourceController::class, 'show']);
            Route::put('/', [ResourceController::class, 'update']);
            Route::delete('/', [ResourceController::class, 'destroy']);
        });
    });

    // Budget Management Routes
    Route::prefix('budget-lines')->group(function () {
        Route::get('/', [BudgetLineController::class, 'index']);
        Route::post('/', [BudgetLineController::class, 'store']);
        
        Route::prefix('{budgetLine}')->group(function () {
            Route::get('/', [BudgetLineController::class, 'show']);
            Route::put('/', [BudgetLineController::class, 'update']);
            Route::delete('/', [BudgetLineController::class, 'destroy']);
        });
    });

    // Document Management Routes
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::post('/', [DocumentController::class, 'store']);
        
        Route::prefix('{document}')->group(function () {
            Route::get('/', [DocumentController::class, 'show']);
            Route::put('/', [DocumentController::class, 'update']);
            Route::delete('/', [DocumentController::class, 'destroy']);
            Route::get('/download', [DocumentController::class, 'download']);
        });
    });

    // Risk Management Routes
    Route::prefix('risks')->group(function () {
        Route::get('/', [RiskController::class, 'index']);
        Route::post('/', [RiskController::class, 'store']);
        
        Route::prefix('{risk}')->group(function () {
            Route::get('/', [RiskController::class, 'show']);
            Route::put('/', [RiskController::class, 'update']);
            Route::delete('/', [RiskController::class, 'destroy']);
        });
    });

    // Dashboard and Reporting Routes (handled by public routes above)
    Route::prefix('dashboard')->group(function () {
        // Dashboard overview handled by public routes above
        
        Route::get('/reports', function () {
            // Reports endpoint
            return response()->json([
                'success' => true,
                'message' => 'Reports endpoint'
            ]);
        });
    });

    // Integration Routes (for other ERP modules)
    Route::prefix('integrations')->group(function () {
        // CRM Integration
        Route::get('/crm/clients', function () {
            // Get clients from CRM module
            return response()->json([
                'success' => true,
                'message' => 'CRM clients integration endpoint'
            ]);
        });
        
        // HR Integration
        Route::get('/hr/employees', function () {
            // Get employees from HR module
            return response()->json([
                'success' => true,
                'message' => 'HR employees integration endpoint'
            ]);
        });
        
        // Finance Integration
        Route::get('/finance/costs', function () {
            // Get cost data from Finance module
            return response()->json([
                'success' => true,
                'message' => 'Finance costs integration endpoint'
            ]);
        });
        
        // SHEQ Integration
        Route::get('/sheq/incidents', function () {
            // Get SHEQ incidents
            return response()->json([
                'success' => true,
                'message' => 'SHEQ incidents integration endpoint'
            ]);
        });
    });
});

// Fallback route for undefined endpoints
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'error' => 'The requested API endpoint does not exist'
    ], 404);
});
