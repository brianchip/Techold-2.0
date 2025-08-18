<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donezo - Project Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .gradient-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .gradient-dark { background: linear-gradient(135deg, #1f2937 0%, #111827 100%); }
        .sidebar-item:hover { background-color: rgba(255, 255, 255, 0.1); }
        .progress-circle { 
            transform: rotate(-90deg);
            stroke-dasharray: 283;
            stroke-dashoffset: 167;
            transition: stroke-dashoffset 0.6s ease;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar', ['active' => 'dashboard'])

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                        <p class="text-gray-600 text-sm">Plan, prioritize, and accomplish your tasks with ease.</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>Add Project</span>
                        </button>
                        <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                            Import Data
                        </button>
                        @include('partials.user-menu')
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Projects -->
                    <div class="gradient-green text-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm mb-1">Total Projects</p>
                                <p class="text-3xl font-bold">{{ $statistics['total_projects'] ?? 24 }}</p>
                                <p class="text-green-100 text-sm mt-2">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    Increased from last month
                                </p>
                            </div>
                            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                                <i class="fas fa-arrow-trend-up text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Ended Projects -->
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm mb-1">Ended Projects</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $statistics['completed_projects'] ?? 10 }}</p>
                                <p class="text-gray-500 text-sm mt-2">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    Increased from last month
                                </p>
                            </div>
                            <div class="bg-gray-100 p-3 rounded-lg">
                                <i class="fas fa-arrow-trend-up text-2xl text-gray-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Running Projects -->
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm mb-1">Running Projects</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $statistics['active_projects'] ?? 12 }}</p>
                                <p class="text-gray-500 text-sm mt-2">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    Decreased from last month
                                </p>
                            </div>
                            <div class="bg-gray-100 p-3 rounded-lg">
                                <i class="fas fa-arrow-trend-up text-2xl text-gray-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Projects -->
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm mb-1">Pending Project</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $statistics['overdue_projects'] ?? 2 }}</p>
                                <p class="text-gray-500 text-sm mt-2">On Process</p>
                            </div>
                            <div class="bg-gray-100 p-3 rounded-lg">
                                <i class="fas fa-arrow-trend-up text-2xl text-gray-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Dashboard Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Project Analytics -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Project Analytics</h3>
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <span>24%</span>
                                </div>
                            </div>
                            <div class="h-64">
                                <canvas id="projectChart"></canvas>
                            </div>
                        </div>

                        <!-- Team Collaboration -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Team Collaboration</h3>
                                <button class="text-green-600 text-sm font-medium">+ Add Member</button>
                            </div>
                            <div class="space-y-4">
                                @if(isset($recent_projects) && $recent_projects->count() > 0)
                                    @foreach($recent_projects->take(4) as $index => $project)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <img src="https://via.placeholder.com/40x40/{{ ['10b981', '3b82f6', '8b5cf6', 'f59e0b'][$index % 4] }}/ffffff?text={{ substr($project->project_name ?? 'P', 0, 1) }}" class="w-10 h-10 rounded-full">
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $project->project_name ?? 'Project Manager' }}</h4>
                                                <p class="text-sm text-gray-500">Working on {{ $project->project_code ?? 'Project Repository' }}</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full
                                            @if($project->status === 'Completed') bg-green-100 text-green-800
                                            @elseif($project->status === 'In Progress') bg-blue-100 text-blue-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $project->status ?? 'Completed' }}
                                        </span>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <img src="https://via.placeholder.com/40x40/10b981/ffffff?text=AD" class="w-10 h-10 rounded-full">
                                            <div>
                                                <h4 class="font-medium text-gray-900">Alexandra Deff</h4>
                                                <p class="text-sm text-gray-500">Working on GitHub Project Repository</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Completed</span>
                                    </div>
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <img src="https://via.placeholder.com/40x40/3b82f6/ffffff?text=EA" class="w-10 h-10 rounded-full">
                                            <div>
                                                <h4 class="font-medium text-gray-900">Edwin Adenike</h4>
                                                <p class="text-sm text-gray-500">Working on Integrate User Authorization System</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">In Progress</span>
                                    </div>
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <img src="https://via.placeholder.com/40x40/8b5cf6/ffffff?text=IO" class="w-10 h-10 rounded-full">
                                            <div>
                                                <h4 class="font-medium text-gray-900">Isaac Oluwatemilorun</h4>
                                                <p class="text-sm text-gray-500">Working on Develop Add and Edit Functionality</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                                    </div>
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <img src="https://via.placeholder.com/40x40/f59e0b/ffffff?text=DO" class="w-10 h-10 rounded-full">
                                            <div>
                                                <h4 class="font-medium text-gray-900">David Oshodi</h4>
                                                <p class="text-sm text-gray-500">Working on Responsive Layout for Homepage</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">In Progress</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Reminders -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Reminders</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900 mb-2">Meeting with Arc Company</h4>
                                <p class="text-sm text-gray-600 mb-3">Time: 10:00 pm - 04:00 pm</p>
                                <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm flex items-center space-x-2 hover:bg-green-700 transition-colors">
                                    <i class="fas fa-video"></i>
                                    <span>Start Meeting</span>
                                </button>
                            </div>
                        </div>

                        <!-- Project Progress -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Project Progress</h3>
                            <div class="flex items-center justify-center mb-6">
                                <div class="relative w-32 h-32">
                                    <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="45" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                                        <circle cx="50" cy="50" r="45" stroke="#10b981" stroke-width="8" fill="none" 
                                                stroke-dasharray="283" stroke-dashoffset="167" stroke-linecap="round"/>
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="text-2xl font-bold text-gray-900">41%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mb-4">
                                <p class="text-sm text-gray-600">Project Ended</p>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-gray-600">Completed</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <span class="text-gray-600">In Progress</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                                        <span class="text-gray-600">Pending</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Project List -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Project</h3>
                                <button class="text-green-600 text-sm font-medium">+ New</button>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                                    <i class="fas fa-code text-blue-600"></i>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 text-sm">Develop API Endpoints</h4>
                                        <p class="text-xs text-gray-500">Due date: Nov 30, 2024</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                                    <i class="fas fa-users text-green-600"></i>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 text-sm">Onboarding Flow</h4>
                                        <p class="text-xs text-gray-500">Due date: Nov 10, 2024</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 p-3 bg-orange-50 rounded-lg">
                                    <i class="fas fa-chart-line text-orange-600"></i>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 text-sm">Build Dashboard</h4>
                                        <p class="text-xs text-gray-500">Due date: Nov 20, 2024</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 p-3 bg-purple-50 rounded-lg">
                                    <i class="fas fa-tachometer-alt text-purple-600"></i>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 text-sm">Optimize Page Load</h4>
                                        <p class="text-xs text-gray-500">Due date: Nov 25, 2024</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
                                    <i class="fas fa-bug text-red-600"></i>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 text-sm">Cross-Browser Testing</h4>
                                        <p class="text-xs text-gray-500">Due date: Dec 5, 2024</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Time Tracker -->
                        <div class="gradient-dark text-white p-6 rounded-xl shadow-lg">
                            <h3 class="text-lg font-semibold mb-4">Time Tracker</h3>
                            <div class="text-center mb-4">
                                <div class="text-3xl font-bold">01:24:08</div>
                            </div>
                            <div class="flex justify-center space-x-3">
                                <button class="bg-white bg-opacity-20 hover:bg-opacity-30 p-3 rounded-lg transition-colors">
                                    <i class="fas fa-pause"></i>
                                </button>
                                <button class="bg-red-500 hover:bg-red-600 p-3 rounded-lg transition-colors">
                                    <i class="fas fa-stop"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Project Analytics Chart
        const ctx = document.getElementById('projectChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
                datasets: [{
                    data: [20, 45, 30, 60, 25, 40, 35],
                    backgroundColor: [
                        'rgba(156, 163, 175, 0.5)',
                        '#10b981',
                        '#10b981',
                        '#10b981',
                        'rgba(156, 163, 175, 0.5)',
                        'rgba(156, 163, 175, 0.5)',
                        'rgba(156, 163, 175, 0.5)'
                    ],
                    borderColor: [
                        'rgba(156, 163, 175, 1)',
                        '#10b981',
                        '#10b981', 
                        '#10b981',
                        'rgba(156, 163, 175, 1)',
                        'rgba(156, 163, 175, 1)',
                        'rgba(156, 163, 175, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        display: false,
                        beginAtZero: true
                    },
                    x: {
                        display: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 8
                    }
                }
            }
        });
    </script>
</body>
</html>