<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Management - Donezo Project Management</title>
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
        @include('partials.sidebar', ['active' => 'budget'])

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Budget Management</h1>
                        <p class="text-gray-600 text-sm">Track project costs, budgets, and financial performance</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>Add Budget Line</span>
                        </button>
                        <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Export Report
                        </button>
                        @include('partials.user-menu')
                    </div>
                </div>
            </header>

            <!-- Budget Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Budget Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-lg mr-4">
                                <i class="fas fa-wallet text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Budget</p>
                                <p class="text-2xl font-bold text-gray-900">$2,456,789</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-lg mr-4">
                                <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Actual Spent</p>
                                <p class="text-2xl font-bold text-gray-900">$1,875,432</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-100 rounded-lg mr-4">
                                <i class="fas fa-chart-line text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Variance</p>
                                <p class="text-2xl font-bold text-green-600">$581,357</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-lg mr-4">
                                <i class="fas fa-percentage text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Utilization</p>
                                <p class="text-2xl font-bold text-gray-900">76.3%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Budget Analysis -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Budget vs Actual Chart -->
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Budget vs Actual Spending</h3>
                        <div class="h-64">
                            <canvas id="budgetChart"></canvas>
                        </div>
                    </div>

                    <!-- Cost Breakdown -->
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cost Breakdown by Category</h3>
                        <div class="space-y-4">
                            @php
                            $categories = [
                                ['name' => 'Labor Costs', 'budget' => 1200000, 'actual' => 950000, 'color' => 'blue'],
                                ['name' => 'Materials', 'budget' => 800000, 'actual' => 620000, 'color' => 'green'],
                                ['name' => 'Equipment', 'budget' => 300000, 'actual' => 210000, 'color' => 'yellow'],
                                ['name' => 'Overhead', 'budget' => 156789, 'actual' => 95432, 'color' => 'purple'],
                            ];
                            @endphp
                            @foreach($categories as $category)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-medium text-gray-900">{{ $category['name'] }}</h4>
                                    <span class="text-sm text-gray-500">
                                        {{ number_format(($category['actual'] / $category['budget']) * 100, 1) }}%
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mb-2">
                                    <span>Budget: ${{ number_format($category['budget']) }}</span>
                                    <span>Actual: ${{ number_format($category['actual']) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-{{ $category['color'] }}-600 h-2 rounded-full" 
                                         style="width: {{ ($category['actual'] / $category['budget']) * 100 }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Budget Lines Table -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Budget Lines</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variance</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @for($i = 0; $i < 15; $i++)
                                @php
                                $budget = rand(10000, 100000);
                                $actual = rand(5000, $budget);
                                $variance = $budget - $actual;
                                $projects = ['ERP System', 'Website Redesign', 'Mobile App', 'Data Migration'];
                                $categories = ['Labor', 'Materials', 'Equipment', 'Overhead', 'Travel'];
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-gray-900">{{ $projects[rand(0, 3)] }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-900">{{ $categories[rand(0, 4)] }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-900">{{ ['Development costs', 'Design fees', 'Testing services', 'Consulting'][rand(0, 3)] }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-gray-900">${{ number_format($budget) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-900">${{ number_format($actual) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium {{ $variance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            ${{ number_format(abs($variance)) }} {{ $variance >= 0 ? 'under' : 'over' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $variance >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $variance >= 0 ? 'On Track' : 'Over Budget' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <button class="text-blue-600 hover:text-blue-800 transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800 transition-colors">
                                                <i class="fas fa-edit"></i>
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
        // Budget vs Actual Chart
        const ctx = document.getElementById('budgetChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Q1', 'Q2', 'Q3', 'Q4'],
                datasets: [{
                    label: 'Budget',
                    data: [600000, 650000, 580000, 626789],
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }, {
                    label: 'Actual',
                    data: [520000, 475000, 380000, 500432],
                    backgroundColor: 'rgba(16, 185, 129, 0.5)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + (value / 1000) + 'K';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    </script>
</body>
</html>

