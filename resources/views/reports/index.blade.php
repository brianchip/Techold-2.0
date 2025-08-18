<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Donezo Project Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .gradient-dark { background: linear-gradient(135deg, #1f2937 0%, #111827 100%); }
        .sidebar-item:hover { background-color: rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar', ['active' => 'reports'])

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
                        <p class="text-gray-600 text-sm">Comprehensive project analytics and reporting</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>Custom Report</span>
                        </button>
                        <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Export All
                        </button>
                        @include('partials.user-menu')
                    </div>
                </div>
            </header>

            <!-- Reports Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Quick Reports -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl shadow-lg cursor-pointer hover:shadow-xl transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-blue-100 rounded-lg mr-4">
                                <i class="fas fa-chart-bar text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Project Dashboard</h3>
                                <p class="text-sm text-gray-600">Overview of all projects</p>
                            </div>
                        </div>
                        <button class="w-full bg-blue-50 text-blue-700 py-2 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                            Generate Report
                        </button>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg cursor-pointer hover:shadow-xl transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-green-100 rounded-lg mr-4">
                                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Financial Report</h3>
                                <p class="text-sm text-gray-600">Budget vs actual analysis</p>
                            </div>
                        </div>
                        <button class="w-full bg-green-50 text-green-700 py-2 rounded-lg text-sm font-medium hover:bg-green-100 transition-colors">
                            Generate Report
                        </button>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg cursor-pointer hover:shadow-xl transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-purple-100 rounded-lg mr-4">
                                <i class="fas fa-users text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Resource Report</h3>
                                <p class="text-sm text-gray-600">Team utilization analysis</p>
                            </div>
                        </div>
                        <button class="w-full bg-purple-50 text-purple-700 py-2 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors">
                            Generate Report
                        </button>
                    </div>
                </div>

                <!-- Charts and Analytics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Project Performance -->
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Performance Trends</h3>
                        <div class="h-64">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>

                    <!-- Budget Analysis -->
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Budget Utilization</h3>
                        <div class="h-64">
                            <canvas id="budgetChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Reports -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Reports</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @for($i = 0; $i < 10; $i++)
                                @php
                                $reports = [
                                    ['name' => 'Q4 Project Summary', 'type' => 'Project Dashboard'],
                                    ['name' => 'Budget Variance Analysis', 'type' => 'Financial'],
                                    ['name' => 'Resource Utilization Report', 'type' => 'Resource'],
                                    ['name' => 'Risk Assessment Summary', 'type' => 'Risk'],
                                    ['name' => 'Timeline Performance Report', 'type' => 'Schedule'],
                                ];
                                $report = $reports[rand(0, 4)];
                                $users = ['John Smith', 'Sarah Davis', 'Mike Wilson', 'Emma Johnson'];
                                $statuses = ['Generated', 'Processing', 'Ready', 'Expired'];
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-file-pdf text-red-500 mr-3"></i>
                                            <span class="text-sm font-medium text-gray-900">{{ $report['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-900">{{ $report['type'] }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img src="https://via.placeholder.com/24x24/{{ ['10b981', '3b82f6', '8b5cf6', 'f59e0b'][rand(0, 3)] }}/ffffff?text={{ chr(65 + rand(0, 25)) }}" class="w-6 h-6 rounded-full mr-2">
                                            <span class="text-sm text-gray-900">{{ $users[rand(0, 3)] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-900">{{ \Carbon\Carbon::now()->subDays(rand(1, 30))->format('M d, Y') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php $status = $statuses[rand(0, 3)]; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($status === 'Generated') bg-green-100 text-green-800
                                            @elseif($status === 'Processing') bg-yellow-100 text-yellow-800
                                            @elseif($status === 'Ready') bg-blue-100 text-blue-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <button class="text-blue-600 hover:text-blue-800 transition-colors">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800 transition-colors">
                                                <i class="fas fa-share"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-800 transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Performance Chart
        const ctx1 = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Projects Completed',
                    data: [5, 8, 12, 15, 18, 22],
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4
                }, {
                    label: 'On-Time Delivery %',
                    data: [85, 88, 92, 89, 95, 91],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Budget Chart
        const ctx2 = document.getElementById('budgetChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Used', 'Remaining', 'Overrun'],
                datasets: [{
                    data: [65, 25, 10],
                    backgroundColor: ['#10b981', '#e5e7eb', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>

