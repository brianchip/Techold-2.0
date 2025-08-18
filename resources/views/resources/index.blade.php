<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources - Donezo Project Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        @include('partials.sidebar', ['active' => 'resources'])

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Resource Management</h1>
                        <p class="text-gray-600 text-sm">Manage human resources and equipment allocation</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>Add Resource</span>
                        </button>
                        @include('partials.user-menu')
                    </div>
                </div>
            </header>

            <!-- Resources Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Resource Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-lg mr-4">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Staff</p>
                                <p class="text-2xl font-bold text-gray-900">24</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-lg mr-4">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Available</p>
                                <p class="text-2xl font-bold text-gray-900">18</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-100 rounded-lg mr-4">
                                <i class="fas fa-tools text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Equipment</p>
                                <p class="text-2xl font-bold text-gray-900">156</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-lg mr-4">
                                <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Cost</p>
                                <p class="text-2xl font-bold text-gray-900">$45,230</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resource Tabs -->
                <div class="bg-white rounded-xl shadow-lg mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8 px-6">
                            <button class="py-4 px-1 border-b-2 border-green-500 font-medium text-sm text-green-600">
                                Human Resources
                            </button>
                            <button class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                                Equipment
                            </button>
                            <button class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                                Allocation History
                            </button>
                        </nav>
                    </div>

                    <!-- Human Resources Tab -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @for($i = 0; $i < 12; $i++)
                            <div class="bg-gray-50 rounded-lg p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-center mb-4">
                                    <img src="https://via.placeholder.com/48x48/{{ ['10b981', '3b82f6', '8b5cf6', 'f59e0b'][rand(0, 3)] }}/ffffff?text={{ chr(65 + rand(0, 25)) }}" class="w-12 h-12 rounded-full mr-4">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ ['John Smith', 'Sarah Davis', 'Mike Wilson', 'Emma Johnson', 'David Brown', 'Lisa Anderson'][rand(0, 5)] }}</h3>
                                        <p class="text-sm text-gray-500">{{ ['Project Manager', 'Senior Engineer', 'Developer', 'Designer', 'QA Specialist', 'Business Analyst'][rand(0, 5)] }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="font-medium text-{{ rand(0, 1) ? 'green' : 'yellow' }}-600">
                                            {{ rand(0, 1) ? 'Available' : 'Assigned' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Rate:</span>
                                        <span class="font-medium">${{ rand(50, 150) }}/hour</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Utilization:</span>
                                        <span class="font-medium">{{ rand(60, 100) }}%</span>
                                    </div>
                                </div>
                                <div class="mt-4 flex space-x-2">
                                    <button class="flex-1 bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 transition-colors">
                                        Assign
                                    </button>
                                    <button class="px-3 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50 transition-colors">
                                        View
                                    </button>
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

