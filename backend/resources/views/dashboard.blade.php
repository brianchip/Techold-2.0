<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Techold Engineering - ERP Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .stat-number {
            animation: countUp 1s ease-out;
        }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-building text-2xl"></i>
                        <h1 class="text-2xl font-bold">Techold Engineering</h1>
                    </div>
                    <div class="hidden md:block">
                        <span class="text-sm opacity-90">ERP Project Management System</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-2">
                        <i class="fas fa-circle text-green-400 text-xs"></i>
                        <span class="text-sm">System Online</span>
                    </div>
                    <button class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-user"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Dashboard -->
    <main class="container mx-auto px-6 py-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome to Your Dashboard</h2>
            <p class="text-gray-600">Overview of your project management system performance</p>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Projects -->
            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Projects</p>
                        <p class="text-3xl font-bold text-blue-600 stat-number">{{ $statistics['total_projects'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-project-diagram text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-xs text-green-600">
                        <i class="fas fa-arrow-up"></i> 12% from last month
                    </span>
                </div>
            </div>

            <!-- Active Projects -->
            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Active Projects</p>
                        <p class="text-3xl font-bold text-green-600 stat-number">{{ $statistics['active_projects'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-play-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-xs text-green-600">
                        <i class="fas fa-arrow-up"></i> Running smoothly
                    </span>
                </div>
            </div>

            <!-- Total Budget -->
            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Budget</p>
                        <p class="text-3xl font-bold text-purple-600 stat-number">${{ number_format($statistics['total_budget']) }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-xs text-gray-600">
                        <i class="fas fa-chart-line"></i> {{ $statistics['budget_utilization'] }}% utilized
                    </span>
                </div>
            </div>

            <!-- Overdue Projects -->
            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Overdue Projects</p>
                        <p class="text-3xl font-bold text-red-600 stat-number">{{ $statistics['overdue_projects'] }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    @if($statistics['overdue_projects'] == 0)
                        <span class="text-xs text-green-600">
                            <i class="fas fa-check-circle"></i> All on track
                        </span>
                    @else
                        <span class="text-xs text-red-600">
                            <i class="fas fa-clock"></i> Needs attention
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Charts and Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Project Status Chart -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Project Overview</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="relative w-20 h-20 mx-auto mb-2">
                            <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-gray-300" stroke="currentColor" stroke-width="3" fill="none" d="M18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831"></path>
                                <path class="text-blue-600" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="{{ $statistics['completion_rate'] }}, 100" d="M18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831"></path>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-sm font-bold text-gray-800">{{ $statistics['completion_rate'] }}%</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">Completion Rate</p>
                    </div>
                    <div class="text-center">
                        <div class="relative w-20 h-20 mx-auto mb-2">
                            <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-gray-300" stroke="currentColor" stroke-width="3" fill="none" d="M18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831"></path>
                                <path class="text-green-600" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="{{ $statistics['budget_utilization'] }}, 100" d="M18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831"></path>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-sm font-bold text-gray-800">{{ $statistics['budget_utilization'] }}%</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">Budget Used</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>New Project</span>
                    </button>
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-chart-bar"></i>
                        <span>View Reports</span>
                    </button>
                    <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-users"></i>
                        <span>Manage Team</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- API Access and System Status -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- API Endpoints -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">API Endpoints</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">Dashboard Overview</p>
                            <p class="text-sm text-gray-600">/api/dashboard/overview</p>
                        </div>
                        <button class="bg-blue-100 text-blue-600 px-3 py-1 rounded text-sm hover:bg-blue-200 transition-colors">
                            <i class="fas fa-external-link-alt"></i>
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">Projects API</p>
                            <p class="text-sm text-gray-600">/api/projects</p>
                        </div>
                        <button class="bg-blue-100 text-blue-600 px-3 py-1 rounded text-sm hover:bg-blue-200 transition-colors">
                            <i class="fas fa-external-link-alt"></i>
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">Laravel Welcome</p>
                            <p class="text-sm text-gray-600">/welcome</p>
                        </div>
                        <button class="bg-gray-100 text-gray-600 px-3 py-1 rounded text-sm hover:bg-gray-200 transition-colors">
                            <i class="fas fa-external-link-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">System Status</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Database</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-green-600">Connected</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">API Services</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-green-600">Operational</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">File Storage</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-green-600">Available</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Last Backup</span>
                        <span class="text-sm text-gray-600">{{ now()->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="container mx-auto px-6 py-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    © {{ date('Y') }} Techold Engineering. ERP Project Management System v2.0
                </div>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <span>Laravel {{ app()->version() }}</span>
                    <span>•</span>
                    <span>PHP {{ phpversion() }}</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Animate numbers on load
            const numbers = document.querySelectorAll('.stat-number');
            numbers.forEach(num => {
                const value = parseInt(num.textContent.replace(/[^0-9]/g, ''));
                let current = 0;
                const increment = value / 30;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= value) {
                        current = value;
                        clearInterval(timer);
                    }
                    if (num.textContent.includes('$')) {
                        num.textContent = '$' + Math.floor(current).toLocaleString();
                    } else {
                        num.textContent = Math.floor(current);
                    }
                }, 50);
            });

            // Add click handlers for API buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('button') && e.target.closest('button').querySelector('.fa-external-link-alt')) {
                    const endpoint = e.target.closest('.flex').querySelector('p:last-child').textContent;
                    if (endpoint.startsWith('/api/')) {
                        window.open(endpoint, '_blank');
                    } else {
                        window.open(endpoint, '_blank');
                    }
                }
            });
        });
    </script>
</body>
</html>
