<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\Resource;
use App\Models\Risk;
use App\Models\BudgetLine;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get dashboard statistics
        $statistics = [
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'In Progress')->count(),
            'completed_projects' => Project::where('status', 'Completed')->count(),
            'overdue_projects' => Project::where('end_date', '<', Carbon::now())
                ->whereNotIn('status', ['Completed', 'Cancelled'])
                ->count(),
            'total_budget' => Project::sum('total_budget'),
            'actual_cost' => Project::sum('actual_cost'),
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::where('status', 'Completed')->count(),
            'overdue_tasks' => Task::where('end_date', '<', Carbon::now())
                ->whereNotIn('status', ['Completed', 'Cancelled'])
                ->count(),
            'active_risks' => Risk::whereIn('status', ['Identified', 'In Progress'])->count(),
        ];

        // Get recent projects
        $recent_projects = Project::with(['client', 'projectManager'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get overdue projects
        $overdue_projects = Project::where('end_date', '<', Carbon::now())
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->with(['client', 'projectManager'])
            ->take(5)
            ->get();

        // Get project progress data for charts
        $project_progress_data = Project::selectRaw('
            COUNT(*) as count,
            AVG(progress_percent) as avg_progress,
            status
        ')
        ->groupBy('status')
        ->get();

        // Get monthly project creation trend
        $monthly_data = Project::selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as month,
            COUNT(*) as count
        ')
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return view('dashboard.index', compact(
            'statistics',
            'recent_projects',
            'overdue_projects',
            'project_progress_data',
            'monthly_data'
        ));
    }

    public function api()
    {
        // API version of dashboard data
        $statistics = [
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'In Progress')->count(),
            'completed_projects' => Project::where('status', 'Completed')->count(),
            'overdue_projects' => Project::where('end_date', '<', Carbon::now())
                ->whereNotIn('status', ['Completed', 'Cancelled'])
                ->count(),
            'total_budget' => Project::sum('total_budget'),
            'actual_cost' => Project::sum('actual_cost'),
            'budget_variance' => Project::sum('total_budget') - Project::sum('actual_cost'),
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::where('status', 'Completed')->count(),
            'overdue_tasks' => Task::where('end_date', '<', Carbon::now())
                ->whereNotIn('status', ['Completed', 'Cancelled'])
                ->count(),
            'active_risks' => Risk::whereIn('status', ['Identified', 'In Progress'])->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }
}