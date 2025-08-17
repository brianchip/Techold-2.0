<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Dashboard - Techold Engineering</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-blue-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <i class="fas fa-building text-2xl mr-3"></i>
                    <h1 class="text-xl font-bold">Techold ERP - Project Management</h1>
                </div>
                <div class="flex space-x-4">
                    <a href="/api/projects" class="hover:bg-blue-800 px-3 py-2 rounded">API</a>
                    <a href="/debug-deploy.php" class="hover:bg-blue-800 px-3 py-2 rounded">Debug</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Projects -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-project-diagram text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Projects</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $statistics['total_projects'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Projects -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-play-circle text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Projects</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $statistics['active_projects'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Budget -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-dollar-sign text-yellow-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Budget</dt>
                                <dd class="text-2xl font-bold text-gray-900">${{ number_format($statistics['total_budget'], 0) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue Projects -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Overdue Projects</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $statistics['overdue_projects'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Lists -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Projects -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Projects</h3>
                </div>
                <div class="px-6 py-4">
                    @if($recent_projects->count() > 0)
                        <div class="space-y-4">
                            @foreach($recent_projects as $project)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $project->project_name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $project->project_code }}</p>
                                    <p class="text-sm text-gray-500">Client: {{ $project->client->company_name ?? 'N/A' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($project->status === 'In Progress') bg-green-100 text-green-800
                                        @elseif($project->status === 'Planning') bg-blue-100 text-blue-800
                                        @elseif($project->status === 'Completed') bg-gray-100 text-gray-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ $project->status }}
                                    </span>
                                    <p class="text-sm text-gray-500 mt-1">{{ $project->progress_percent }}% Complete</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-folder-open text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">No projects yet. Start by creating your first project!</p>
                            <a href="/api/projects" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i>
                                View API
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Project Status Distribution -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">System Status</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">API Endpoints</span>
                            <span class="text-green-600 font-semibold">✓ Active</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Database</span>
                            <span class="text-green-600 font-semibold">✓ Connected</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Tasks</span>
                            <span class="text-gray-900 font-semibold">{{ $statistics['total_tasks'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Active Risks</span>
                            <span class="text-{{ $statistics['active_risks'] > 0 ? 'yellow' : 'green' }}-600 font-semibold">
                                {{ $statistics['active_risks'] }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Budget Utilization</span>
                            <span class="text-gray-900 font-semibold">
                                {{ $statistics['total_budget'] > 0 ? number_format(($statistics['actual_cost'] / $statistics['total_budget']) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="/api/projects" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fas fa-project-diagram text-blue-600 text-xl mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">View Projects API</h4>
                            <p class="text-sm text-gray-500">Access project data via API</p>
                        </div>
                    </a>
                    <a href="/api/dashboard/overview" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <i class="fas fa-chart-bar text-green-600 text-xl mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">Dashboard API</h4>
                            <p class="text-sm text-gray-500">Get dashboard statistics</p>
                        </div>
                    </a>
                    <a href="/debug-deploy.php" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                        <i class="fas fa-bug text-yellow-600 text-xl mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">System Debug</h4>
                            <p class="text-sm text-gray-500">Check system status</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} Techold Engineering ERP System. Built with Laravel {{ app()->version() }}</p>
        </div>
    </div>
</body>
</html>
