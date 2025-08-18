<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Management - Donezo Project Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .gradient-dark { background: linear-gradient(135deg, #1f2937 0%, #111827 100%); }
        .sidebar-item:hover { background-color: rgba(255, 255, 255, 0.1); }
        .risk-critical { border-left: 4px solid #dc2626; }
        .risk-high { border-left: 4px solid #ea580c; }
        .risk-medium { border-left: 4px solid #ca8a04; }
        .risk-low { border-left: 4px solid #16a34a; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar', ['active' => 'risks'])

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Risk Management</h1>
                        <p class="text-gray-600 text-sm">Identify, assess, and mitigate project risks</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>Add Risk</span>
                        </button>
                        <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Risk Report
                        </button>
                        @include('partials.user-menu')
                    </div>
                </div>
            </header>

            <!-- Risk Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Risk Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-100 rounded-lg mr-4">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Critical Risks</p>
                                <p class="text-2xl font-bold text-gray-900">3</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-orange-100 rounded-lg mr-4">
                                <i class="fas fa-exclamation-circle text-orange-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">High Risks</p>
                                <p class="text-2xl font-bold text-gray-900">7</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-100 rounded-lg mr-4">
                                <i class="fas fa-exclamation text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Medium Risks</p>
                                <p class="text-2xl font-bold text-gray-900">12</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-lg mr-4">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Mitigated</p>
                                <p class="text-2xl font-bold text-gray-900">45</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk List -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Risk Register</h3>
                            <div class="flex items-center space-x-4">
                                <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option>All Projects</option>
                                    <option>ERP System</option>
                                    <option>Website Redesign</option>
                                    <option>Mobile App</option>
                                </select>
                                <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option>All Severities</option>
                                    <option>Critical</option>
                                    <option>High</option>
                                    <option>Medium</option>
                                    <option>Low</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            @for($i = 0; $i < 15; $i++)
                            @php
                            $risks = [
                                ['title' => 'Budget Overrun', 'severity' => 'critical', 'probability' => 'High', 'impact' => 'High'],
                                ['title' => 'Schedule Delay', 'severity' => 'high', 'probability' => 'Medium', 'impact' => 'High'],
                                ['title' => 'Resource Unavailability', 'severity' => 'medium', 'probability' => 'Medium', 'impact' => 'Medium'],
                                ['title' => 'Technical Complexity', 'severity' => 'high', 'probability' => 'High', 'impact' => 'Medium'],
                                ['title' => 'Scope Creep', 'severity' => 'medium', 'probability' => 'Medium', 'impact' => 'Medium'],
                                ['title' => 'Weather Conditions', 'severity' => 'low', 'probability' => 'Low', 'impact' => 'Medium'],
                                ['title' => 'Regulatory Changes', 'severity' => 'high', 'probability' => 'Low', 'impact' => 'High'],
                            ];
                            $risk = $risks[rand(0, 6)];
                            $projects = ['ERP System', 'Website Redesign', 'Mobile App', 'Data Migration'];
                            $statuses = ['Identified', 'Analyzing', 'Mitigating', 'Monitoring', 'Closed'];
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow risk-{{ $risk['severity'] }}">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <h4 class="text-lg font-semibold text-gray-900 mr-3">{{ $risk['title'] }}</h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($risk['severity'] === 'critical') bg-red-100 text-red-800
                                                @elseif($risk['severity'] === 'high') bg-orange-100 text-orange-800
                                                @elseif($risk['severity'] === 'medium') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ ucfirst($risk['severity']) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3">
                                            Risk of {{ strtolower($risk['title']) }} due to various project factors and external dependencies.
                                        </p>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Project:</span>
                                                <span class="font-medium text-gray-900 ml-1">{{ $projects[rand(0, 3)] }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Probability:</span>
                                                <span class="font-medium text-gray-900 ml-1">{{ $risk['probability'] }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Impact:</span>
                                                <span class="font-medium text-gray-900 ml-1">{{ $risk['impact'] }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Status:</span>
                                                <span class="font-medium text-gray-900 ml-1">{{ $statuses[rand(0, 4)] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2 ml-4">
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
                                </div>
                                
                                <!-- Mitigation Plan -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h5 class="font-medium text-gray-900 mb-2">Mitigation Plan:</h5>
                                    <p class="text-sm text-gray-600 mb-3">
                                        {{ ['Implement strict budget monitoring and approval processes', 'Establish clear project milestones and regular progress reviews', 'Maintain resource allocation buffer and backup plans', 'Break down complex tasks and conduct proof of concepts'][rand(0, 3)] }}
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm">
                                            <span class="text-gray-500">Owner:</span>
                                            <div class="flex items-center">
                                                <img src="https://via.placeholder.com/24x24/{{ ['10b981', '3b82f6', '8b5cf6', 'f59e0b'][rand(0, 3)] }}/ffffff?text={{ chr(65 + rand(0, 25)) }}" class="w-6 h-6 rounded-full mr-2">
                                                <span class="font-medium text-gray-900">{{ ['John Smith', 'Sarah Davis', 'Mike Wilson', 'Emma Johnson'][rand(0, 3)] }}</span>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Due: {{ \Carbon\Carbon::now()->addDays(rand(7, 30))->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>

