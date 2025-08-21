<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $project->project_name }} - Project Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .gradient-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .gradient-dark { background: linear-gradient(135deg, #1f2937 0%, #111827 100%); }
        .sidebar-item:hover { background-color: rgba(255, 255, 255, 0.1); }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar', ['active' => 'projects'])

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <!-- Breadcrumb -->
                <nav class="mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-gray-500">
                        <li>
                            <a href="/" class="hover:text-gray-700">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-chevron-right mx-2 text-xs"></i>
                            <a href="{{ route('projects.index') }}" class="hover:text-gray-700">Projects</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-chevron-right mx-2 text-xs"></i>
                            <span class="text-gray-900 font-medium">{{ $project->project_name }}</span>
                        </li>
                    </ol>
                </nav>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $project->project_name }}</h1>
                            <p class="text-gray-600 text-sm">{{ $project->project_code }} • {{ $project->project_type }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            @if($project->status === 'In Progress') bg-green-100 text-green-800
                            @elseif($project->status === 'Planned') bg-blue-100 text-blue-800
                            @elseif($project->status === 'Completed') bg-gray-100 text-gray-800
                            @elseif($project->status === 'On Hold') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $project->status }}
                        </span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('projects.edit', $project) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-blue-700 transition-colors">
                            <i class="fas fa-edit"></i>
                            <span>Edit Project</span>
                        </a>
                        
                        <!-- Quick Actions Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2">
                                <i class="fas fa-ellipsis-v"></i>
                                <span>Quick Actions</span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                <div class="py-1">
                                    <a href="{{ route('projects.costing', $project) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-calculator mr-3"></i>
                                        Costing Dashboard
                                    </a>
                                    <a href="#" onclick="openAddTaskModal()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-plus mr-3"></i>
                                        Add Task
                                    </a>
                                    <a href="#" onclick="openAddBudgetModal()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-dollar-sign mr-3"></i>
                                        Add Budget Line
                                    </a>
                                    <a href="#" onclick="openUploadDocumentModal()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-upload mr-3"></i>
                                        Upload Document
                                    </a>
                                    <a href="#" onclick="openAddRiskModal()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-exclamation-triangle mr-3"></i>
                                        Add Risk
                                    </a>
                                    <hr class="my-1">
                                    <a href="{{ route('projects.export-pdf', $project) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-download mr-3"></i>
                                        Export PDF Report
                                    </a>
                                    <a href="#" onclick="shareProject()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-share mr-3"></i>
                                        Share Project
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        @include('partials.user-menu')
                    </div>
                </div>
            </header>

            <!-- Project Overview Cards -->
            <div class="p-6 border-b border-gray-200 bg-white">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Progress Card -->
                    <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-xl text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm">Project Progress</p>
                                <p class="text-2xl font-bold">{{ $project->progress_percent ?? 0 }}%</p>
                            </div>
                            <i class="fas fa-chart-line text-2xl text-green-200"></i>
                        </div>
                        <div class="mt-4 bg-green-400 rounded-full h-2">
                            <div class="bg-white rounded-full h-2 transition-all duration-300" style="width: {{ $project->progress_percent ?? 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Budget Card -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-xl text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm">Total Budget</p>
                                <p class="text-2xl font-bold">${{ number_format($project->total_budget ?? 0) }}</p>
                                <p class="text-blue-200 text-xs">Spent: ${{ number_format($project->actual_cost ?? 0) }}</p>
                            </div>
                            <i class="fas fa-dollar-sign text-2xl text-blue-200"></i>
                        </div>
                    </div>

                    <!-- Timeline Card -->
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 rounded-xl text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm">Days Remaining</p>
                                <p class="text-2xl font-bold">{{ $project->days_remaining ?? 0 }}</p>
                                <p class="text-purple-200 text-xs">Due: {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <i class="fas fa-calendar text-2xl text-purple-200"></i>
                        </div>
                    </div>

                    <!-- Team Card -->
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 rounded-xl text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm">Team Members</p>
                                <p class="text-2xl font-bold">{{ $project->resources->count() ?? 0 }}</p>
                                <p class="text-orange-200 text-xs">{{ $project->tasks->count() ?? 0 }} Active Tasks</p>
                            </div>
                            <i class="fas fa-users text-2xl text-orange-200"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Tab Navigation -->
            <div class="bg-white border-b border-gray-200">
                <nav class="flex space-x-2 px-6 overflow-x-auto">
                    <button class="tab-button py-4 px-3 border-b-2 border-green-500 text-green-600 font-medium whitespace-nowrap" data-tab="dashboard">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </button>
                    <button class="tab-button py-4 px-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap" data-tab="setup">
                        <i class="fas fa-cog mr-2"></i>Setup
                    </button>
                    <button class="tab-button py-4 px-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap" data-tab="wbs-tasks">
                        <i class="fas fa-sitemap mr-2"></i>WBS & Tasks ({{ $project->tasks->count() }})
                    </button>
                    <button class="tab-button py-4 px-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap" data-tab="gantt">
                        <i class="fas fa-chart-gantt mr-2"></i>Gantt
                    </button>
                    <button class="tab-button py-4 px-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap" data-tab="resources">
                        <i class="fas fa-users mr-2"></i>Resources
                    </button>
                    <button class="tab-button py-4 px-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap" data-tab="milestones">
                        <i class="fas fa-flag-checkered mr-2"></i>Milestones
                    </button>
                    <button class="tab-button py-4 px-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap" data-tab="budget">
                        <i class="fas fa-dollar-sign mr-2"></i>Budget
                    </button>
                    <button class="tab-button py-4 px-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap" data-tab="progress">
                        <i class="fas fa-chart-line mr-2"></i>Progress
                    </button>
                    <button class="tab-button py-4 px-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap" data-tab="boq">
                        <i class="fas fa-list-alt mr-2"></i>BOQ
                    </button>
                    <button class="tab-button py-4 px-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap" data-tab="documents">
                        <i class="fas fa-file-alt mr-2"></i>Documents ({{ $project->documents->count() }})
                    </button>
                    <button class="tab-button py-4 px-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap" data-tab="tracking">
                        <i class="fas fa-map-marked-alt mr-2"></i>Tracking
                    </button>
                    <button class="tab-button py-4 px-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap" data-tab="costing">
                        <i class="fas fa-calculator mr-2"></i>Costing
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Dashboard Tab -->
                <div id="dashboard" class="tab-content active">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Project Information -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Information</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Client:</span>
                                    <span class="font-medium">{{ $project->client->company_name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Project Manager:</span>
                                    <span class="font-medium">{{ $project->projectManager->full_name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Location:</span>
                                    <span class="font-medium">{{ $project->location ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Start Date:</span>
                                    <span class="font-medium">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">End Date:</span>
                                    <span class="font-medium">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d, Y') : 'N/A' }}</span>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <span class="text-gray-500">Description:</span>
                                    <p class="mt-2 text-gray-900">{{ $project->description ?? 'No description available' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-plus text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-900">Project created</p>
                                        <p class="text-xs text-gray-500">{{ $project->created_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                                @if($project->updated_at != $project->created_at)
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-edit text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-900">Project updated</p>
                                        <p class="text-xs text-gray-500">{{ $project->updated_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tasks Tab -->
                <div id="tasks" class="tab-content">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Work Breakdown Structure (WBS)</h3>
                            <button class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                                <i class="fas fa-plus"></i>
                                <span>Add Task</span>
                            </button>
                        </div>
                        
                        @if($project->tasks->count() > 0)
                            <div class="space-y-4">
                                @foreach($project->tasks as $task)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-tasks text-blue-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $task->task_name }}</h4>
                                                <p class="text-sm text-gray-500">{{ $task->description ?? 'No description' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <span class="text-sm font-medium text-gray-900">{{ $task->progress_percent ?? 0 }}%</span>
                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $task->progress_percent ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-tasks text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Tasks Yet</h3>
                                <p class="text-gray-500 mb-6">Start by creating your first task to break down the project work.</p>
                                <button class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Create First Task
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Budget Tab -->
                <div id="budget" class="tab-content">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Budget Management</h3>
                            <button onclick="openAddBudgetModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                                <i class="fas fa-plus"></i>
                                <span>Add Budget Line</span>
                            </button>
                        </div>
                        
                        <!-- Budget Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-blue-900">Total Budget</h4>
                                <p class="text-2xl font-bold text-blue-600">${{ number_format($project->total_budget ?? 0) }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-green-900">Actual Cost</h4>
                                <p class="text-2xl font-bold text-green-600">${{ number_format($project->actual_cost ?? 0) }}</p>
                            </div>
                            <div class="bg-{{ $project->variance >= 0 ? 'green' : 'red' }}-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-{{ $project->variance >= 0 ? 'green' : 'red' }}-900">Variance</h4>
                                <p class="text-2xl font-bold text-{{ $project->variance >= 0 ? 'green' : 'red' }}-600">
                                    {{ $project->variance >= 0 ? '+' : '' }}${{ number_format($project->variance ?? 0) }}
                                </p>
                            </div>
                        </div>

                        <!-- Budget Lines -->
                        @if($project->budgetLines->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full table-auto">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budgeted</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variance</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($project->budgetLines as $budgetLine)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $budgetLine->category }}</div>
                                                    <div class="text-sm text-gray-500">{{ $budgetLine->description }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ${{ number_format($budgetLine->planned_amount ?? 0) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ${{ number_format($budgetLine->actual_amount ?? 0) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="text-{{ $budgetLine->variance >= 0 ? 'green' : 'red' }}-600">
                                                    {{ $budgetLine->variance >= 0 ? '+' : '' }}${{ number_format($budgetLine->variance ?? 0) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $budgetLine->status ?? 'Active' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-dollar-sign text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Budget Lines Yet</h3>
                                <p class="text-gray-500 mb-6">Create budget lines to track project costs and expenses.</p>
                                <button class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Create First Budget Line
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Resources Tab -->
                <div id="resources" class="tab-content">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Resource Allocation</h3>
                            <button onclick="openAllocateResourceModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                                <i class="fas fa-plus"></i>
                                <span>Allocate Resource</span>
                            </button>
                        </div>
                        
                        @if($project->resources->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($project->resources as $resource)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-{{ $resource->resource_type === 'Human' ? 'user' : 'cog' }} text-purple-600"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $resource->resource_name }}</h4>
                                                <p class="text-sm text-gray-500">{{ $resource->resource_type }}</p>
                                                @if($resource->role)
                                                    <p class="text-xs text-gray-400">{{ $resource->role }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex space-x-1">
                                            <button onclick="editResource({{ $resource->id }})" 
                                                    class="text-blue-600 hover:text-blue-800 p-1 rounded"
                                                    title="Edit Resource">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="removeResource({{ $resource->id }})" 
                                                    class="text-red-600 hover:text-red-800 p-1 rounded"
                                                    title="Remove Resource">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Allocated:</span>
                                            <span class="font-medium">{{ $resource->allocated_hours ?? 0 }}h</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Actual:</span>
                                            <span class="font-medium">{{ $resource->actual_hours ?? 0 }}h</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Rate:</span>
                                            <span class="font-medium">${{ number_format($resource->hourly_rate ?? 0, 2) }}/h</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Total Cost:</span>
                                            <span class="font-medium text-green-600">${{ number_format($resource->total_cost ?? 0, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Period:</span>
                                            <span class="text-xs">{{ $resource->allocation_start_date?->format('M j') }} - {{ $resource->allocation_end_date?->format('M j') }}</span>
                                        </div>
                                        <div class="mt-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $resource->status === 'Active' ? 'bg-green-100 text-green-800' : 
                                                   ($resource->status === 'Allocated' ? 'bg-blue-100 text-blue-800' : 
                                                   ($resource->status === 'Completed' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800')) }}">
                                                {{ $resource->status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Resources Allocated</h3>
                                <p class="text-gray-500 mb-6">Allocate human and equipment resources to this project.</p>
                                <button onclick="openAllocateResourceModal()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Allocate First Resource
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Documents Tab -->
                <div id="documents" class="tab-content">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Project Documents</h3>
                            <button onclick="openUploadDocumentModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                                <i class="fas fa-upload"></i>
                                <span>Upload Document</span>
                            </button>
                        </div>
                        
                        @if($project->documents->count() > 0)
                            <div class="space-y-4">
                                @foreach($project->documents as $document)
                                <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            @php
                                                $iconClass = match($document->file_type) {
                                                    'pdf' => 'fa-file-pdf text-red-600',
                                                    'doc', 'docx' => 'fa-file-word text-blue-600',
                                                    'xls', 'xlsx' => 'fa-file-excel text-green-600',
                                                    'ppt', 'pptx' => 'fa-file-powerpoint text-orange-600',
                                                    'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image text-purple-600',
                                                    'mp4', 'avi', 'mov' => 'fa-file-video text-pink-600',
                                                    'mp3', 'wav' => 'fa-file-audio text-yellow-600',
                                                    'zip', 'rar' => 'fa-file-archive text-gray-600',
                                                    'txt' => 'fa-file-alt text-gray-600',
                                                    default => 'fa-file text-blue-600'
                                                };
                                            @endphp
                                            <i class="fas {{ $iconClass }}"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $document->original_file_name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $document->category }} • {{ $document->file_size_formatted ?? number_format($document->file_size / 1024, 1) . ' KB' }}</p>
                                            @if($document->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $document->description }}</p>
                                            @endif
                                            <p class="text-xs text-gray-400 mt-1">
                                                Uploaded {{ $document->uploaded_at->diffForHumans() }} by {{ $document->uploader->first_name ?? 'Unknown' }} {{ $document->uploader->last_name ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button onclick="viewDocument({{ $document->id }})" 
                                                class="text-green-600 hover:text-green-800 p-2 rounded-lg hover:bg-green-50 transition-colors"
                                                title="View Document">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('documents.download', $document) }}" 
                                           class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                           title="Download Document">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button onclick="deleteDocument({{ $document->id }})" 
                                                class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                title="Delete Document">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Documents Yet</h3>
                                <p class="text-gray-500 mb-6">Upload project documents, drawings, contracts, and reports.</p>
                                <button class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-upload mr-2"></i>
                                    Upload First Document
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Risks Tab -->
                <div id="risks" class="tab-content">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Risk Management</h3>
                            <button class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                                <i class="fas fa-plus"></i>
                                <span>Add Risk</span>
                            </button>
                        </div>
                        
                        @if($project->risks->count() > 0)
                            <div class="space-y-4">
                                @foreach($project->risks as $risk)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $risk->risk_title }}</h4>
                                                <p class="text-sm text-gray-500 mt-1">{{ $risk->description }}</p>
                                                <div class="flex items-center space-x-4 mt-2">
                                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">
                                                        {{ $risk->severity }}
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        Risk Score: {{ $risk->risk_score ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($risk->status === 'Open') bg-red-100 text-red-800
                                            @elseif($risk->status === 'Mitigated') bg-green-100 text-green-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $risk->status ?? 'Open' }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-exclamation-triangle text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Risks Identified</h3>
                                <p class="text-gray-500 mb-6">Identify and track project risks to ensure successful delivery.</p>
                                <button class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Identify First Risk
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Setup Tab -->
                <div id="setup" class="tab-content">
                    <div class="space-y-6">
                        <!-- Project Initialization -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Project Initialization</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                <div class="text-center p-4 border-2 border-green-500 bg-green-50 rounded-lg">
                                    <i class="fas fa-info-circle text-2xl text-green-600 mb-2"></i>
                                    <h4 class="font-medium text-green-900">Basic Info</h4>
                                    <p class="text-sm text-green-700">Project details</p>
                                </div>
                                <div class="text-center p-4 border-2 border-gray-300 bg-gray-50 rounded-lg">
                                    <i class="fas fa-file-alt text-2xl text-gray-400 mb-2"></i>
                                    <h4 class="font-medium text-gray-600">Template</h4>
                                    <p class="text-sm text-gray-500">Project template</p>
                                </div>
                                <div class="text-center p-4 border-2 border-gray-300 bg-gray-50 rounded-lg">
                                    <i class="fas fa-users text-2xl text-gray-400 mb-2"></i>
                                    <h4 class="font-medium text-gray-600">Team Setup</h4>
                                    <p class="text-sm text-gray-500">Assign team members</p>
                                </div>
                                <div class="text-center p-4 border-2 border-gray-300 bg-gray-50 rounded-lg">
                                    <i class="fas fa-cog text-2xl text-gray-400 mb-2"></i>
                                    <h4 class="font-medium text-gray-600">Configuration</h4>
                                    <p class="text-sm text-gray-500">Project settings</p>
                                </div>
                            </div>
                            
                            <!-- Project Details Form -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                                        <input type="text" value="{{ $project->project_name }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Project Code</label>
                                        <input type="text" value="{{ $project->project_code }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                                        <input type="text" value="{{ $project->client->company_name ?? 'N/A' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Contract Type</label>
                                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                            <option>{{ $project->costing_type ?? 'Tender/Proposal' }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WBS & Tasks Tab -->
                <div id="wbs-tasks" class="tab-content">
                    <div class="space-y-6">
                        <!-- WBS Structure -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Work Breakdown Structure</h3>
                                <div class="flex space-x-2">
                                    <button onclick="addPhase()" class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                                        <i class="fas fa-plus"></i>
                                        <span>Add Phase</span>
                                    </button>
                                    <button onclick="openAddTaskModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-tasks"></i>
                                        <span>Add Task</span>
                                    </button>
                                </div>
                            </div>

                            @if($project->tasks->count() > 0)
                                <div class="space-y-4">
                                    @foreach($project->tasks as $task)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-tasks text-blue-600"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-medium text-gray-900">{{ $task->task_name }}</h4>
                                                    <p class="text-sm text-gray-500">{{ $task->description ?? 'No description' }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $task->status === 'Completed' ? 'bg-green-100 text-green-800' : 
                                                       ($task->status === 'In Progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ $task->status }}
                                                </span>
                                                <span class="text-sm text-gray-500">{{ $task->progress_percent ?? 0 }}%</span>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Start:</span>
                                                <span class="font-medium">{{ $task->start_date?->format('M j, Y') ?? 'Not set' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">End:</span>
                                                <span class="font-medium">{{ $task->end_date?->format('M j, Y') ?? 'Not set' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Hours:</span>
                                                <span class="font-medium">{{ $task->estimated_hours ?? 0 }}h</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Cost:</span>
                                                <span class="font-medium">${{ number_format($task->planned_cost ?? 0, 2) }}</span>
                                            </div>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="mt-3">
                                            <div class="bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $task->progress_percent ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <i class="fas fa-sitemap text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Tasks Created</h3>
                                    <p class="text-gray-500 mb-6">Create your work breakdown structure by adding project phases and tasks.</p>
                                    <button onclick="openAddTaskModal()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create First Task
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Gantt Tab -->
                <div id="gantt" class="tab-content">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Enhanced Gantt & Scheduling</h3>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm text-gray-600">View:</label>
                                    <select class="border border-gray-300 rounded px-2 py-1 text-sm">
                                        <option>Weeks</option>
                                        <option>Days</option>
                                        <option>Months</option>
                                    </select>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" id="showCriticalPath" class="rounded">
                                    <label for="showCriticalPath" class="text-sm text-gray-600">Show Critical Path</label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" id="autoSchedule" class="rounded">
                                    <label for="autoSchedule" class="text-sm text-gray-600">Auto Schedule</label>
                                </div>
                                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-sync-alt mr-2"></i>Auto Reschedule
                                </button>
                            </div>
                        </div>

                        <!-- Gantt Chart Placeholder -->
                        <div class="border border-gray-200 rounded-lg p-8 text-center">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                    <div class="flex items-center space-x-3">
                                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                        <span class="font-medium">Project Planning Phase</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-gray-600">15d</span>
                                        <div class="w-32 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                                        </div>
                                        <span class="text-sm text-green-600">100%</span>
                                    </div>
                                </div>
                                
                                @foreach($project->tasks->take(3) as $task)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                    <div class="flex items-center space-x-3">
                                        <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                                        <span class="font-medium">{{ $task->task_name }}</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-gray-600">{{ $task->estimated_hours ?? 0 }}h</span>
                                        <div class="w-32 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $task->progress_percent ?? 0 }}%"></div>
                                        </div>
                                        <span class="text-sm text-blue-600">{{ $task->progress_percent ?? 0 }}%</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-8 text-gray-500">
                                <i class="fas fa-chart-gantt text-4xl mb-4"></i>
                                <p>Interactive Gantt chart will be implemented here</p>
                                <p class="text-sm mt-2">Features: Drag-drop scheduling, critical path, resource loading</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Milestones Tab -->
                <div id="milestones" class="tab-content">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Project Milestones</h3>
                            <button onclick="openAddMilestoneModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                                <i class="fas fa-plus"></i>
                                <span>Add Milestone</span>
                            </button>
                        </div>

                        <!-- Milestones List -->
                        <div class="space-y-4">
                            <div class="flex items-center p-4 border border-gray-200 rounded-lg">
                                <div class="w-4 h-4 bg-green-500 rounded-full mr-4"></div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">Project Kickoff</h4>
                                    <p class="text-sm text-gray-500">Initial project setup and team alignment</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-medium text-green-600">Completed</span>
                                    <p class="text-xs text-gray-500">{{ $project->start_date?->format('M j, Y') }}</p>
                                </div>
                            </div>

                            <div class="flex items-center p-4 border border-gray-200 rounded-lg">
                                <div class="w-4 h-4 bg-blue-500 rounded-full mr-4"></div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">Design Phase Complete</h4>
                                    <p class="text-sm text-gray-500">All design documents approved and finalized</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-medium text-blue-600">In Progress</span>
                                    <p class="text-xs text-gray-500">Target: {{ now()->addDays(30)->format('M j, Y') }}</p>
                                </div>
                            </div>

                            <div class="flex items-center p-4 border border-gray-200 rounded-lg">
                                <div class="w-4 h-4 bg-gray-300 rounded-full mr-4"></div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">Project Completion</h4>
                                    <p class="text-sm text-gray-500">Final deliverables and project closure</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-medium text-gray-600">Planned</span>
                                    <p class="text-xs text-gray-500">{{ $project->end_date?->format('M j, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Tab -->
                <div id="progress" class="tab-content">
                    <div class="space-y-6">
                        <!-- Real-time Project Status -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Real-time Project Status Tracking</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    On Track
                                </span>
                            </div>

                            <!-- Project Phases -->
                            <div class="space-y-4 mb-8">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">Site Survey</h4>
                                            <p class="text-sm text-gray-500">Site assessment and measurements</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="w-32 bg-gray-200 rounded-full h-2 mb-1">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">100%</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">Design</h4>
                                            <p class="text-sm text-gray-500">Technical drawings and specifications</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="w-32 bg-gray-200 rounded-full h-2 mb-1">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">100%</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-clock text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">Procurement</h4>
                                            <p class="text-sm text-gray-500">Material sourcing and delivery</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="w-32 bg-gray-200 rounded-full h-2 mb-1">
                                            <div class="bg-yellow-500 h-2 rounded-full" style="width: 85%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">85%</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-play text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">Installation</h4>
                                            <p class="text-sm text-gray-500">On-site installation work</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="w-32 bg-gray-200 rounded-full h-2 mb-1">
                                            <div class="bg-blue-500 h-2 rounded-full" style="width: 60%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">60%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Overall Progress -->
                            <div class="border-t pt-6">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-900">Overall Progress</h4>
                                    <span class="text-2xl font-bold text-green-600">{{ $project->progress_percent ?? 75 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-green-500 h-3 rounded-full" style="width: {{ $project->progress_percent ?? 75 }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Project Information Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Timeline -->
                            <div class="bg-white rounded-xl shadow-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-gray-900">Timeline</h4>
                                    <i class="fas fa-calendar text-blue-600"></i>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Planned:</span>
                                        <span>{{ $project->start_date?->format('M j') }} - {{ $project->end_date?->format('M j') }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Current:</span>
                                        <span>{{ $project->start_date?->format('M j') }} - {{ now()->format('M j') }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Variance:</span>
                                        <span class="text-green-600">0 days</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Budget -->
                            <div class="bg-white rounded-xl shadow-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-gray-900">Budget</h4>
                                    <i class="fas fa-dollar-sign text-green-600"></i>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Planned:</span>
                                        <span>${{ number_format($project->total_budget ?? 0, 0) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Actual:</span>
                                        <span>${{ number_format($project->actual_cost ?? 0, 0) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Savings:</span>
                                        <span class="text-green-600">25%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Team -->
                            <div class="bg-white rounded-xl shadow-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-gray-900">Team</h4>
                                    <i class="fas fa-users text-purple-600"></i>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Assigned:</span>
                                        <span>{{ $project->resources->count() }} members</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Active:</span>
                                        <span>{{ $project->resources->where('status', 'Active')->count() }} members</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Utilization:</span>
                                        <span>75%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOQ Tab -->
                <div id="boq" class="tab-content">
                    <div class="space-y-6">
                        <!-- BOQ Hub Navigation -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Bill of Quantities (BOQ) Management</h3>
                                <div class="flex space-x-2">
                                    <button onclick="openAddBOQSectionModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                                        <i class="fas fa-plus"></i>
                                        <span>Add BOQ</span>
                                    </button>
                                    <button onclick="importBOQ()" class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-upload"></i>
                                        <span>Import</span>
                                    </button>
                                    <button onclick="exportBOQ()" class="bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-download"></i>
                                        <span>Export</span>
                                    </button>
                                </div>
                            </div>

                            <!-- BOQ Tabs -->
                            <div class="border-b border-gray-200 mb-6">
                                <nav class="flex space-x-8">
                                    <button class="boq-tab-button py-2 px-1 border-b-2 border-green-500 text-green-600 font-medium" data-boq-tab="hub">
                                        <i class="fas fa-home mr-2"></i>Hub
                                    </button>
                                    <button class="boq-tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-boq-tab="create">
                                        <i class="fas fa-plus mr-2"></i>Create
                                    </button>
                                    <button class="boq-tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-boq-tab="import">
                                        <i class="fas fa-upload mr-2"></i>Import
                                    </button>
                                    <button class="boq-tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-boq-tab="library">
                                        <i class="fas fa-book mr-2"></i>Library
                                    </button>
                                    <button class="boq-tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-boq-tab="sections">
                                        <i class="fas fa-layer-group mr-2"></i>Sections
                                    </button>
                                    <button class="boq-tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-boq-tab="versions">
                                        <i class="fas fa-code-branch mr-2"></i>Versions
                                    </button>
                                    <button class="boq-tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-boq-tab="approvals">
                                        <i class="fas fa-check-circle mr-2"></i>Approvals
                                    </button>
                                </nav>
                            </div>

                            <!-- BOQ Tab Content Container -->
                            <div id="boq-tab-content">
                                <!-- Hub Tab Content (Default) -->
                                <div id="boq-hub" class="boq-tab-content active">
                                    <!-- BOQ Summary -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-green-600 text-sm font-medium">Phase 1 - Foundation & Structure</p>
                                            <p class="text-2xl font-bold text-green-900">$15,000</p>
                                            <p class="text-green-700 text-xs">23 items</p>
                                        </div>
                                        <div class="bg-green-100 p-2 rounded-lg">
                                            <span class="text-green-600 text-xs font-medium bg-green-200 px-2 py-1 rounded">Approved</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-yellow-600 text-sm font-medium">Phase 1 - Electrical Installation</p>
                                            <p class="text-2xl font-bold text-yellow-900">$22,250</p>
                                            <p class="text-yellow-700 text-xs">45 items</p>
                                        </div>
                                        <div class="bg-yellow-100 p-2 rounded-lg">
                                            <span class="text-yellow-600 text-xs font-medium bg-yellow-200 px-2 py-1 rounded">In Review</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-gray-600 text-sm font-medium">Phase 2 - System Integration</p>
                                            <p class="text-2xl font-bold text-gray-900">$8,500</p>
                                            <p class="text-gray-700 text-xs">18 items</p>
                                        </div>
                                        <div class="bg-gray-100 p-2 rounded-lg">
                                            <span class="text-gray-600 text-xs font-medium bg-gray-200 px-2 py-1 rounded">Draft</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BOQ Items Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Item Code</th>
                                            <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                            <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                                            <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                            <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                            <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                            <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-4 py-4 text-sm font-medium text-gray-900">ELC001</td>
                                            <td class="px-4 py-4 text-sm text-gray-900">Solar Panel 400W</td>
                                            <td class="px-4 py-4 text-sm">
                                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Electrical</span>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-900">Each</td>
                                            <td class="px-4 py-4 text-sm text-gray-900">50</td>
                                            <td class="px-4 py-4 text-sm text-gray-900">250</td>
                                            <td class="px-4 py-4 text-sm font-medium text-gray-900">$12500</td>
                                            <td class="px-4 py-4 text-sm text-gray-500">
                                                <button class="text-blue-600 hover:text-blue-800 mr-2">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-red-600 hover:text-red-800">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-4 text-sm font-medium text-gray-900">ELC002</td>
                                            <td class="px-4 py-4 text-sm text-gray-900">Inverter 10kW</td>
                                            <td class="px-4 py-4 text-sm">
                                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Electrical</span>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-900">Each</td>
                                            <td class="px-4 py-4 text-sm text-gray-900">2</td>
                                            <td class="px-4 py-4 text-sm text-gray-900">2500</td>
                                            <td class="px-4 py-4 text-sm font-medium text-gray-900">$5000</td>
                                            <td class="px-4 py-4 text-sm text-gray-500">
                                                <button class="text-blue-600 hover:text-blue-800 mr-2">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-red-600 hover:text-red-800">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                                </div>
                                
                                <!-- Other BOQ Tab Contents -->
                                <div id="boq-create" class="boq-tab-content hidden">
                                    <div class="max-w-6xl mx-auto space-y-6">
                                        <!-- BOQ Header Information -->
                                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                            <h4 class="text-lg font-medium text-gray-900 mb-6">BOQ Document Information</h4>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">BOQ Reference Number *</label>
                                                    <div class="flex items-center space-x-2">
                                                        <input type="text" id="boq_reference" name="boq_reference" required 
                                                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" 
                                                               placeholder="BOQ-{{ $project->project_code }}-001">
                                                        <button type="button" onclick="generateBOQNumber()" 
                                                                class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 text-sm whitespace-nowrap"
                                                                title="Generate automatic BOQ number">
                                                            <i class="fas fa-magic mr-1"></i>Auto
                                                        </button>
                                                    </div>
                                                    <div class="mt-2">
                                                        <label class="flex items-center text-sm">
                                                            <input type="checkbox" id="auto_generate_boq" name="auto_generate_boq" 
                                                                   class="mr-2" onchange="toggleAutoGenerate()">
                                                            <span class="text-gray-600">Auto-generate BOQ numbers for this project</span>
                                                        </label>
                                                    </div>
                                                    <div id="numbering-format" class="mt-2 hidden">
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Numbering Format</label>
                                                        <select id="numbering_format_select" class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500" onchange="updateNumberingFormat()">
                                                            <option value="datetime">Date-Time Format (BOQ-PROJ-YYYYMMDD-XXXX)</option>
                                                            <option value="sequential">Sequential Format (BOQ-PROJ-001)</option>
                                                            <option value="yearseq">Year-Sequential (BOQ-PROJ-2024-001)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">BOQ Version</label>
                                                    <input type="text" id="boq_version" name="boq_version" 
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" 
                                                           placeholder="1.0" value="1.0">
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Preparation *</label>
                                                    <input type="date" id="preparation_date" name="preparation_date" required 
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" 
                                                           value="{{ date('Y-m-d') }}">
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Prepared By *</label>
                                                    <div class="space-y-2">
                                                        <select id="prepared_by" name="prepared_by" required 
                                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                                                onchange="handlePreparedByChange()">
                                                            <option value="">Select Engineer</option>
                                                            @foreach(\App\Models\Employee::where('status', 'active')->get() as $employee)
                                                                <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->position }})</option>
                                                            @endforeach
                                                            <option value="add_new">+ Add New Engineer</option>
                                                        </select>
                                                        
                                                        <!-- New Engineer Input Fields (Hidden by default) -->
                                                        <div id="new_engineer_fields" class="hidden space-y-3 p-4 bg-gray-50 rounded-lg border">
                                                            <div class="flex items-center justify-between mb-2">
                                                                <h6 class="text-sm font-medium text-gray-900">Add New Engineer</h6>
                                                                <button type="button" onclick="cancelAddNewEngineer()" class="text-gray-400 hover:text-gray-600">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                            
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">First Name *</label>
                                                                    <input type="text" id="new_engineer_first_name" name="new_engineer_first_name" 
                                                                           class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500" 
                                                                           placeholder="Enter first name">
                                                                </div>
                                                                
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Last Name *</label>
                                                                    <input type="text" id="new_engineer_last_name" name="new_engineer_last_name" 
                                                                           class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500" 
                                                                           placeholder="Enter last name">
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Email *</label>
                                                                    <input type="email" id="new_engineer_email" name="new_engineer_email" 
                                                                           class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500" 
                                                                           placeholder="engineer@techold.com">
                                                                </div>
                                                                
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Position *</label>
                                                                    <select id="new_engineer_position" name="new_engineer_position" 
                                                                            class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500">
                                                                        <option value="">Select Position</option>
                                                                        <option value="Project Engineer">Project Engineer</option>
                                                                        <option value="Senior Engineer">Senior Engineer</option>
                                                                        <option value="Lead Engineer">Lead Engineer</option>
                                                                        <option value="Principal Engineer">Principal Engineer</option>
                                                                        <option value="Engineering Manager">Engineering Manager</option>
                                                                        <option value="Design Engineer">Design Engineer</option>
                                                                        <option value="Site Engineer">Site Engineer</option>
                                                                        <option value="Systems Engineer">Systems Engineer</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                                                                    <input type="tel" id="new_engineer_phone" name="new_engineer_phone" 
                                                                           class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500" 
                                                                           placeholder="+1-555-0000">
                                                                </div>
                                                                
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                                                                    <select id="new_engineer_department" name="new_engineer_department" 
                                                                            class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500">
                                                                        <option value="">Select Department</option>
                                                                        <option value="Engineering">Engineering</option>
                                                                        <option value="Design">Design</option>
                                                                        <option value="Operations">Operations</option>
                                                                        <option value="Project Management">Project Management</option>
                                                                        <option value="Quality Assurance">Quality Assurance</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="flex items-center justify-end space-x-2 pt-2">
                                                                <button type="button" onclick="cancelAddNewEngineer()" 
                                                                        class="px-3 py-1 text-sm border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                                                                    Cancel
                                                                </button>
                                                                <button type="button" onclick="saveNewEngineer()" 
                                                                        class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                                                                    <i class="fas fa-plus mr-1"></i>Add Engineer
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                                    <select id="currency" name="currency" 
                                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                        <option value="USD">USD ($)</option>
                                                        <option value="EUR">EUR (€)</option>
                                                        <option value="GBP">GBP (£)</option>
                                                        <option value="ZWL">ZWL (Z$)</option>
                                                    </select>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                                                    <input type="date" id="valid_until" name="valid_until" 
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" 
                                                           value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Section Management -->
                                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                            <div class="flex items-center justify-between mb-6">
                                                <h4 class="text-lg font-medium text-gray-900">BOQ Sections & Items</h4>
                                                <button onclick="addBOQSection()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                                    <i class="fas fa-plus mr-2"></i>Add Section
                                                </button>
                                            </div>
                                            
                                            <div id="boq-sections-container" class="space-y-6">
                                                <!-- Dynamic sections will be added here -->
                                            </div>
                                        </div>

                                        <!-- Terms and Conditions -->
                                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                            <h4 class="text-lg font-medium text-gray-900 mb-6">Terms and Conditions</h4>
                                            
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                                                    <select id="payment_terms" name="payment_terms" 
                                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                        <option value="30_days">Net 30 Days</option>
                                                        <option value="15_days">Net 15 Days</option>
                                                        <option value="advance_50">50% Advance, 50% on Completion</option>
                                                        <option value="milestone">Milestone-based Payments</option>
                                                        <option value="custom">Custom Terms</option>
                                                    </select>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Timeline</label>
                                                    <input type="text" id="delivery_timeline" name="delivery_timeline" 
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" 
                                                           placeholder="e.g., 8-12 weeks from order confirmation">
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Warranty Period</label>
                                                    <input type="text" id="warranty_period" name="warranty_period" 
                                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" 
                                                           placeholder="e.g., 12 months from installation completion">
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Special Conditions</label>
                                                    <textarea id="special_conditions" name="special_conditions" rows="4" 
                                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" 
                                                              placeholder="Additional terms, exclusions, or special requirements..."></textarea>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="flex items-center">
                                                            <input type="checkbox" id="include_taxes" name="include_taxes" class="mr-2">
                                                            <span class="text-sm text-gray-700">Prices include applicable taxes</span>
                                                        </label>
                                                    </div>
                                                    <div>
                                                        <label class="flex items-center">
                                                            <input type="checkbox" id="subject_to_approval" name="subject_to_approval" class="mr-2">
                                                            <span class="text-sm text-gray-700">Subject to management approval</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Summary and Actions -->
                                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                            <div class="flex items-center justify-between mb-6">
                                                <h4 class="text-lg font-medium text-gray-900">BOQ Summary</h4>
                                                <div class="text-right">
                                                    <div class="text-sm text-gray-600">Total Estimated Cost</div>
                                                    <div id="total-cost" class="text-2xl font-bold text-green-600">$0.00</div>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                                    <div class="text-sm text-gray-600">Total Sections</div>
                                                    <div id="section-count" class="text-xl font-semibold">0</div>
                                                </div>
                                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                                    <div class="text-sm text-gray-600">Total Items</div>
                                                    <div id="item-count" class="text-xl font-semibold">0</div>
                                                </div>
                                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                                    <div class="text-sm text-gray-600">Average Cost per Item</div>
                                                    <div id="avg-cost" class="text-xl font-semibold">$0.00</div>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center justify-end space-x-3">
                                                <button onclick="saveBOQDraft()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-save mr-2"></i>Save as Draft
                                                </button>
                                                <button onclick="previewBOQ()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                    <i class="fas fa-eye mr-2"></i>Preview BOQ
                                                </button>
                                                <button onclick="submitBOQ()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                                    <i class="fas fa-check mr-2"></i>Create BOQ
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="boq-import" class="boq-tab-content hidden">
                                    <div class="text-center py-12">
                                        <i class="fas fa-upload text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Import BOQ Data</h3>
                                        <p class="text-gray-500 mb-6">Import BOQ data from CSV or Excel files.</p>
                                        <button onclick="importBOQ()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-upload mr-2"></i>Import File
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="boq-library" class="boq-tab-content hidden">
                                    <div class="text-center py-12">
                                        <i class="fas fa-book text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">BOQ Item Library</h3>
                                        <p class="text-gray-500 mb-6">Browse and manage your reusable BOQ items.</p>
                                        <button onclick="openBOQLibraryModal()" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                                            <i class="fas fa-book mr-2"></i>Browse Library
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="boq-sections" class="boq-tab-content hidden">
                                    <div class="space-y-6">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-lg font-medium text-gray-900">Manage BOQ Sections</h4>
                                            <button onclick="switchBOQTab('create')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                                                <i class="fas fa-plus mr-2"></i>Create New BOQ
                                            </button>
                                        </div>
                                        
                                        <div class="space-y-4" id="boqSectionsList">
                                            @if($project->boqSections->count() > 0)
                                                @foreach($project->boqSections as $section)
                                                <div class="bg-white border border-gray-200 rounded-lg p-6">
                                                    <div class="flex items-center justify-between mb-4">
                                                        <div>
                                                            <h5 class="font-medium text-gray-900">{{ $section->section_name }}</h5>
                                                            <p class="text-sm text-gray-600">{{ $section->section_code }}</p>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                                                {{ $section->status }}
                                                            </span>
                                                            <button onclick="editBOQSection({{ $section->id }})" class="text-blue-600 hover:text-blue-800">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button onclick="deleteBOQSection({{ $section->id }})" class="text-red-600 hover:text-red-800">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                                        <div>
                                                            <span class="text-gray-500">Items:</span>
                                                            <span class="font-medium">{{ $section->items->count() }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-500">Total:</span>
                                                            <span class="font-medium">${{ number_format($section->total_amount, 2) }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-500">Order:</span>
                                                            <span class="font-medium">#{{ $section->display_order }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @else
                                                <div class="text-center py-8">
                                                    <i class="fas fa-layer-group text-4xl text-gray-300 mb-4"></i>
                                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Sections Created</h3>
                                                    <p class="text-gray-500 mb-4">Create your first BOQ section to organize your items</p>
                                                    <button onclick="switchBOQTab('create')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                                                        <i class="fas fa-plus mr-2"></i>Create First BOQ
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="boq-versions" class="boq-tab-content hidden">
                                    <div class="text-center py-12">
                                        <i class="fas fa-code-branch text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">BOQ Version Control</h3>
                                        <p class="text-gray-500 mb-6">Track and manage different versions of your BOQ.</p>
                                        <button onclick="createBOQVersion()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-code-branch mr-2"></i>Create Version
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="boq-approvals" class="boq-tab-content hidden">
                                    <div class="text-center py-12">
                                        <i class="fas fa-check-circle text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">BOQ Approval Workflow</h3>
                                        <p class="text-gray-500 mb-6">Submit BOQ for approval and track approval status.</p>
                                        <button onclick="submitBOQForApproval()" class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition-colors">
                                            <i class="fas fa-paper-plane mr-2"></i>Submit for Approval
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tracking Tab -->
                <div id="tracking" class="tab-content">
                    <div class="space-y-6">
                        <!-- Digital Commissioning Checklists -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Digital Commissioning Checklists</h3>
                                <div class="flex space-x-2">
                                    <button onclick="exportTrackingReport()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-download mr-2"></i>Export Report
                                    </button>
                                    <button onclick="saveProgress()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-save mr-2"></i>Save Progress
                                    </button>
                                </div>
                            </div>

                            <!-- Active Checklists -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                                <!-- Solar System Pre-Commissioning -->
                                <div class="border border-blue-200 bg-blue-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="font-medium text-blue-900">Solar System Pre-Commissioning</h4>
                                            <p class="text-sm text-blue-700">{{ $project->project_name }}</p>
                                        </div>
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">In Progress</span>
                                    </div>
                                    <div class="mb-3">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-blue-700">Progress</span>
                                            <span class="text-blue-900 font-medium">18/24 items</span>
                                        </div>
                                        <div class="w-full bg-blue-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- HVAC System Testing -->
                                <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="font-medium text-yellow-900">HVAC System Testing</h4>
                                            <p class="text-sm text-yellow-700">HVAC Retrofit - Mall Complex</p>
                                        </div>
                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-medium">In Progress</span>
                                    </div>
                                    <div class="mb-3">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-yellow-700">Progress</span>
                                            <span class="text-yellow-900 font-medium">14/32 items</span>
                                        </div>
                                        <div class="w-full bg-yellow-200 rounded-full h-2">
                                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 44%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Center Final Inspection -->
                                <div class="border border-green-200 bg-green-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="font-medium text-green-900">Data Center Final Inspection</h4>
                                            <p class="text-sm text-green-700">Data Center Cooling System</p>
                                        </div>
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-medium">Near Completion</span>
                                    </div>
                                    <div class="mb-3">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-green-700">Progress</span>
                                            <span class="text-green-900 font-medium">25/28 items</span>
                                        </div>
                                        <div class="w-full bg-green-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: 89%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Checklist Items -->
                            <div class="border-t pt-6">
                                <h4 class="font-medium text-gray-900 mb-4">Checklist Items</h4>
                                
                                <!-- Safety Checks -->
                                <div class="mb-6">
                                    <h5 class="font-medium text-gray-800 mb-3">Safety Checks</h5>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                            <div class="flex items-center space-x-3">
                                                <i class="fas fa-check-circle text-green-600"></i>
                                                <div>
                                                    <p class="font-medium text-gray-900">Verify all electrical connections are secure</p>
                                                    <p class="text-sm text-gray-600">All connections checked and tightened</p>
                                                </div>
                                            </div>
                                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-medium">high</span>
                                        </div>

                                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                            <div class="flex items-center space-x-3">
                                                <i class="fas fa-check-circle text-green-600"></i>
                                                <div>
                                                    <p class="font-medium text-gray-900">Test emergency shutdown procedures</p>
                                                    <p class="text-sm text-gray-600">Emergency stops working correctly</p>
                                                </div>
                                            </div>
                                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-medium">high</span>
                                        </div>

                                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                            <div class="flex items-center space-x-3">
                                                <i class="fas fa-check-circle text-green-600"></i>
                                                <div>
                                                    <p class="font-medium text-gray-900">Verify grounding system integrity</p>
                                                    <p class="text-sm text-gray-600">Ground resistance: 2.1Ω</p>
                                                </div>
                                            </div>
                                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-medium">high</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Performance Testing -->
                                <div>
                                    <h5 class="font-medium text-gray-800 mb-3">Performance Testing</h5>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                            <div class="flex items-center space-x-3">
                                                <i class="fas fa-check-circle text-green-600"></i>
                                                <div>
                                                    <p class="font-medium text-gray-900">Measure DC voltage output</p>
                                                    <p class="text-sm text-gray-600">Output: 850V DC nominal</p>
                                                </div>
                                            </div>
                                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-medium">medium</span>
                                        </div>

                                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                            <div class="flex items-center space-x-3">
                                                <i class="fas fa-clock text-yellow-600"></i>
                                                <div>
                                                    <p class="font-medium text-gray-900">Test inverter efficiency</p>
                                                    <p class="text-sm text-gray-600">Currently testing under various loads</p>
                                                </div>
                                            </div>
                                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-medium">medium</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Site Survey Forms -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Mobile Site Survey Forms</h3>
                                <button onclick="createNewSurvey()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>New Survey
                                </button>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Survey List -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-4">Survey List</h4>
                                    <div class="space-y-4">
                                        <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <h5 class="font-medium text-gray-900">Solar Installation Survey</h5>
                                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">In Progress</span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                                <i class="fas fa-map-marker-alt mr-2"></i>
                                                <span>Bangalore Tech Park</span>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                <p>Survey Date: 2024-06-08</p>
                                                <div class="flex items-center space-x-4 mt-2">
                                                    <span><i class="fas fa-camera mr-1"></i>12 photos</span>
                                                    <span><i class="fas fa-map-pin mr-1"></i>8 GPS points</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border border-green-200 bg-green-50 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <h5 class="font-medium text-gray-900">HVAC Assessment</h5>
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Completed</span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                                <i class="fas fa-map-marker-alt mr-2"></i>
                                                <span>Chennai Mall Complex</span>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                <p>Survey Date: 2024-06-10</p>
                                                <div class="flex items-center space-x-4 mt-2">
                                                    <span><i class="fas fa-camera mr-1"></i>25 photos</span>
                                                    <span><i class="fas fa-map-pin mr-1"></i>15 GPS points</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border border-blue-200 bg-blue-50 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <h5 class="font-medium text-gray-900">Data Center Survey</h5>
                                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Scheduled</span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                                <i class="fas fa-map-marker-alt mr-2"></i>
                                                <span>Mumbai Office Tower</span>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                <p>Survey Date: 2024-06-12</p>
                                                <div class="flex items-center space-x-4 mt-2">
                                                    <span><i class="fas fa-camera mr-1"></i>0 photos</span>
                                                    <span><i class="fas fa-map-pin mr-1"></i>0 GPS points</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Survey Form Preview -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-4">Survey Form</h4>
                                    <div class="border border-gray-200 rounded-lg p-6 text-center">
                                        <i class="fas fa-camera text-6xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500 mb-2">Select a survey from the list to view details</p>
                                        <p class="text-sm text-gray-400">Form builder and mobile interface coming soon</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Costing Tab -->
                <div id="costing" class="tab-content">
                    <div class="space-y-6">
                        <!-- Enhanced Budget Overview Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Current Project Budget -->
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $project->project_name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $project->client->company_name ?? 'N/A' }}</p>
                                    </div>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                        Under Budget
                                    </span>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm text-gray-600">Budget Utilization</span>
                                        <span class="text-sm font-medium">{{ $project->progress_percent ?? 75 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $project->progress_percent ?? 75 }}%"></div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Total Budget</span>
                                        <p class="font-semibold text-lg">${{ number_format($project->total_budget ?? 150000, 0) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Actual Cost</span>
                                        <p class="font-semibold text-lg">${{ number_format($project->actual_cost ?? 112500, 0) }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Variance</span>
                                        <div class="flex items-center">
                                            <i class="fas fa-arrow-down text-green-500 mr-1"></i>
                                            <span class="text-green-600 font-medium">+$37,500</span>
                                            <span class="text-green-600 text-sm ml-1">+25%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BOQ Summary -->
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">BOQ Summary</h3>
                                        <p class="text-sm text-gray-500">{{ $project->boqSections->count() }} sections</p>
                                    </div>
                                    <i class="fas fa-list-alt text-2xl text-blue-600"></i>
                                </div>
                                
                                <div class="space-y-3">
                                    @if($project->boqSections->count() > 0)
                                        @foreach($project->boqSections->take(3) as $section)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm">{{ $section->section_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $section->items_count ?? 0 }} items</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-sm">${{ number_format($section->total_amount, 0) }}</p>
                                                <span class="bg-{{ $section->status === 'Active' ? 'green' : 'gray' }}-100 text-{{ $section->status === 'Active' ? 'green' : 'gray' }}-800 px-2 py-1 rounded text-xs">
                                                    {{ $section->status }}
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500 text-sm">No BOQ sections created yet</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Cost Performance -->
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">Cost Performance</h3>
                                        <p class="text-sm text-gray-500">vs. planned budget</p>
                                    </div>
                                    <i class="fas fa-chart-line text-2xl text-green-600"></i>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                        <div>
                                            <p class="text-green-800 font-medium text-sm">Material Savings</p>
                                            <p class="text-green-600 text-xs">Under budget</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-green-600">25%</p>
                                            <p class="text-green-600 text-xs">$22,500</p>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                        <div>
                                            <p class="text-blue-800 font-medium text-sm">Labor Efficiency</p>
                                            <p class="text-blue-600 text-xs">Ahead of schedule</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-blue-600">15%</p>
                                            <p class="text-blue-600 text-xs">$7,500</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Cost Breakdown -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Detailed Cost Breakdown</h3>
                                <div class="flex space-x-2">
                                    <button onclick="exportBudgetReport()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        <i class="fas fa-download mr-2"></i>Export Report
                                    </button>
                                    <a href="{{ route('projects.costing', $project) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                                        <i class="fas fa-external-link-alt mr-2"></i>Full Dashboard
                                    </a>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Category</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budgeted</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variance</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php
                                            $categories = [
                                                ['name' => 'Materials', 'budgeted' => 90000, 'actual' => 67500, 'variance' => 22500],
                                                ['name' => 'Labor', 'budgeted' => 30000, 'actual' => 22500, 'variance' => 7500],
                                                ['name' => 'Equipment', 'budgeted' => 20000, 'actual' => 15000, 'variance' => 5000],
                                                ['name' => 'Overheads', 'budgeted' => 10000, 'actual' => 7500, 'variance' => 2500]
                                            ];
                                        @endphp
                                        @foreach($categories as $category)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category['name'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($category['budgeted']) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($category['actual']) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">${{ number_format($category['variance']) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Under Budget
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Budget Alerts & Recommendations -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Budget Alerts -->
                            <div class="bg-white rounded-xl shadow-lg p-6">
                                <div class="flex items-center mb-6">
                                    <i class="fas fa-bell text-2xl text-orange-600 mr-3"></i>
                                    <h3 class="text-lg font-semibold text-gray-900">Budget Alerts</h3>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start p-4 bg-green-50 rounded-lg border border-green-200">
                                        <i class="fas fa-check-circle text-green-600 mr-3 mt-1"></i>
                                        <div>
                                            <p class="font-medium text-green-800">Cost Optimization Success</p>
                                            <p class="text-green-700 text-sm">Material costs 25% under budget - excellent procurement</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <i class="fas fa-info-circle text-blue-600 mr-3 mt-1"></i>
                                        <div>
                                            <p class="font-medium text-blue-800">Budget Reallocation Opportunity</p>
                                            <p class="text-blue-700 text-sm">Consider reallocating savings to quality improvements</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cost Recommendations -->
                            <div class="bg-white rounded-xl shadow-lg p-6">
                                <div class="flex items-center mb-6">
                                    <i class="fas fa-lightbulb text-2xl text-yellow-600 mr-3"></i>
                                    <h3 class="text-lg font-semibold text-gray-900">Cost Recommendations</h3>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                        <p class="font-medium text-yellow-800 mb-1">Bulk Purchase Opportunity</p>
                                        <p class="text-yellow-700 text-sm">Consider bulk ordering for Phase 2 to maximize discounts</p>
                                        <span class="inline-block mt-2 text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded">
                                            Potential savings: $5,000
                                        </span>
                                    </div>

                                    <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                                        <p class="font-medium text-purple-800 mb-1">Labor Optimization</p>
                                        <p class="text-purple-700 text-sm">Current efficiency ahead of schedule - maintain pace</p>
                                        <span class="inline-block mt-2 text-xs bg-purple-200 text-purple-800 px-2 py-1 rounded">
                                            On track for early completion
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabId = button.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and contents
                    tabButtons.forEach(btn => {
                        btn.classList.remove('border-green-500', 'text-green-600');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    });
                    tabContents.forEach(content => {
                        content.classList.remove('active');
                    });
                    
                    // Add active class to clicked tab
                    button.classList.remove('border-transparent', 'text-gray-500');
                    button.classList.add('border-green-500', 'text-green-600');
                    
                    // Show corresponding content
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>

    <!-- Add Task Modal -->
    <div id="addTaskModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Add New Task</h3>
                    <button onclick="closeAddTaskModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="addTaskForm" method="POST" action="{{ route('tasks.store') }}">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Task Name *</label>
                            <input type="text" name="task_name" required 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                            <select name="priority" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="Not Started" selected>Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="On Hold">On Hold</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                                <input type="date" name="start_date" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                                <input type="date" name="end_date" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Hours</label>
                            <input type="number" name="estimated_hours" min="0" step="1" placeholder="0"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3" placeholder="Task description..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeAddTaskModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-plus mr-1"></i>
                            Add Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddTaskModal() {
            document.getElementById('addTaskModal').classList.remove('hidden');
        }
        
        function closeAddTaskModal() {
            document.getElementById('addTaskModal').classList.add('hidden');
            document.getElementById('addTaskForm').reset();
        }
        
        // Handle form submission
        document.getElementById('addTaskForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    closeAddTaskModal();
                    location.reload(); // Refresh to show new task
                } else {
                    alert('Error creating task. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating task. Please try again.');
            });
        });

        // Budget Line Modal Functions
        function openAddBudgetModal() {
            document.getElementById('addBudgetModal').classList.remove('hidden');
        }
        
        function closeAddBudgetModal() {
            document.getElementById('addBudgetModal').classList.add('hidden');
            document.getElementById('addBudgetForm').reset();
        }
        
        // Handle budget form submission
        document.getElementById('addBudgetForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    closeAddBudgetModal();
                    location.reload(); // Refresh to show new budget line
                } else {
                    alert('Error creating budget line. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating budget line. Please try again.');
            });
        });

        // Share project function
        function shareProject() {
            fetch('{{ route("projects.share", $project) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.share_url) {
                    navigator.clipboard.writeText(data.share_url).then(() => {
                        alert('Share link copied to clipboard: ' + data.share_url);
                    }).catch(() => {
                        prompt('Copy this share link:', data.share_url);
                    });
                } else {
                    alert('Error generating share link. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating share link. Please try again.');
            });
        }

        // Upload Document Modal Functions
        function openUploadDocumentModal() {
            document.getElementById('uploadDocumentModal').classList.remove('hidden');
        }
        
        function closeUploadDocumentModal() {
            document.getElementById('uploadDocumentModal').classList.add('hidden');
            document.getElementById('uploadDocumentForm').reset();
        }
        
        // Handle document upload form submission
        document.getElementById('uploadDocumentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    closeUploadDocumentModal();
                    location.reload(); // Refresh to show new document
                } else {
                    alert('Error uploading document. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error uploading document. Please try again.');
            });
        });

        // View Document Function
        function viewDocument(documentId) {
            // Open document in new tab/window for viewing
            window.open(`/documents/${documentId}/view`, '_blank');
        }

        // Delete Document Function
        function deleteDocument(documentId) {
            if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
                fetch(`/documents/${documentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        location.reload(); // Refresh to remove deleted document
                    } else {
                        alert('Error deleting document. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting document. Please try again.');
                });
            }
        }

        // Add Risk Modal Functions
        function openAddRiskModal() {
            document.getElementById('addRiskModal').classList.remove('hidden');
        }
        
        function closeAddRiskModal() {
            document.getElementById('addRiskModal').classList.add('hidden');
            document.getElementById('addRiskForm').reset();
        }
        
        // Handle risk form submission
        document.getElementById('addRiskForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    closeAddRiskModal();
                    location.reload(); // Refresh to show new risk
                } else {
                    alert('Error creating risk. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating risk. Please try again.');
            });
        });
    </script>

    <!-- Add Budget Line Modal -->
    <div id="addBudgetModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Add Budget Line</h3>
                    <button onclick="closeAddBudgetModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="addBudgetForm" method="POST" action="{{ route('budget-lines.store') }}">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                            <select name="category" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select Category</option>
                                <option value="Material">Material</option>
                                <option value="Labor">Labor</option>
                                <option value="Equipment">Equipment</option>
                                <option value="Subcontractor">Subcontractor</option>
                                <option value="Overhead">Overhead</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="2" placeholder="Budget line description..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Planned Amount *</label>
                                <input type="number" name="planned_amount" required min="0" step="0.01" placeholder="0.00"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                                <select name="currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="USD" selected>USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                    <option value="ZWL">ZWL</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="Planned" selected>Planned</option>
                                <option value="Approved">Approved</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeAddBudgetModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-plus mr-1"></i>
                            Add Budget Line
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload Document Modal -->
    <div id="uploadDocumentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Upload Document</h3>
                    <button onclick="closeUploadDocumentModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="uploadDocumentForm" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                            <select name="category" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select Category</option>
                                <option value="Contracts & BOQs">Contracts & BOQs</option>
                                <option value="Design & Drawings">Design & Drawings</option>
                                <option value="Site Surveys">Site Surveys</option>
                                <option value="Procurement & Invoices">Procurement & Invoices</option>
                                <option value="Progress Reports">Progress Reports</option>
                                <option value="SHEQ">SHEQ</option>
                                <option value="Photos & Media">Photos & Media</option>
                                <option value="Meeting Minutes">Meeting Minutes</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">File *</label>
                            <input type="file" name="document" required 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <p class="text-xs text-gray-500 mt-1">Max size: 10MB. Supported: PDF, DOC, XLS, PPT, TXT, JPG, PNG</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Version</label>
                            <input type="text" name="version" placeholder="1.0"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3" placeholder="Document description..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeUploadDocumentModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-upload mr-1"></i>
                            Upload Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Risk Modal -->
    <div id="addRiskModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Add Risk</h3>
                    <button onclick="closeAddRiskModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="addRiskForm" method="POST" action="{{ route('risks.store') }}">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Risk Title *</label>
                            <input type="text" name="risk_title" required 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                   placeholder="Enter risk title">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Severity *</label>
                                <select name="severity" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">Select Severity</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                                <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="Identified" selected>Identified</option>
                                    <option value="Analyzing">Analyzing</option>
                                    <option value="Mitigating">Mitigating</option>
                                    <option value="Resolved">Resolved</option>
                                    <option value="Accepted">Accepted</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Probability *</label>
                                <select name="probability" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">Select Probability</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Impact *</label>
                                <select name="impact" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">Select Impact</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Owner</label>
                                <input type="text" name="owner" placeholder="Risk owner"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                <input type="date" name="due_date" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="2" placeholder="Risk description..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mitigation Plan</label>
                            <textarea name="mitigation_plan" rows="2" placeholder="How to mitigate this risk..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeAddRiskModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Add Risk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Allocate Resource Modal -->
    <div id="allocateResourceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Allocate Resource</h3>
                    <button onclick="closeAllocateResourceModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="allocateResourceForm" method="POST" action="{{ route('resources.store') }}">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Task *</label>
                            <select name="task_id" id="taskSelect" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select Task</option>
                                @foreach($project->tasks as $task)
                                    <option value="{{ $task->id }}">{{ $task->task_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Resource Type *</label>
                            <select name="resource_type" id="resourceTypeSelect" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select Type</option>
                                <option value="human">Human Resource</option>
                                <option value="equipment">Equipment</option>
                            </select>
                        </div>
                        
                        <div id="humanResourceFields" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee *</label>
                            <select name="employee_id" id="employeeSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select Employee</option>
                                @php
                                    $employees = \App\Models\Employee::where('is_active', true)->get();
                                @endphp
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" data-rate="{{ $employee->hourly_rate ?? 0 }}">
                                        {{ $employee->full_name }} - {{ $employee->position }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div id="equipmentResourceFields" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Equipment *</label>
                            <select name="equipment_id" id="equipmentSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select Equipment</option>
                                <!-- Equipment options will be loaded dynamically -->
                            </select>
                        </div>
                        
                        <div id="roleField" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <input type="text" name="role" placeholder="e.g., Project Manager, Developer"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Allocated Hours *</label>
                                <input type="number" name="allocated_hours" min="1" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hourly Rate ($)</label>
                                <input type="number" name="hourly_rate" min="0" step="0.01" id="hourlyRateInput"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                                <input type="date" name="allocation_start_date" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                                <input type="date" name="allocation_end_date" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="2" placeholder="Additional notes about this allocation..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeAllocateResourceModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-users mr-1"></i>
                            Allocate Resource
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Allocate Resource Modal Functions
        function openAllocateResourceModal() {
            document.getElementById('allocateResourceModal').classList.remove('hidden');
            loadEquipmentOptions();
        }
        
        function closeAllocateResourceModal() {
            document.getElementById('allocateResourceModal').classList.add('hidden');
            document.getElementById('allocateResourceForm').reset();
            hideAllResourceFields();
        }
        
        function hideAllResourceFields() {
            document.getElementById('humanResourceFields').classList.add('hidden');
            document.getElementById('equipmentResourceFields').classList.add('hidden');
            document.getElementById('roleField').classList.add('hidden');
        }
        
        // Resource type change handler
        document.getElementById('resourceTypeSelect').addEventListener('change', function() {
            const resourceType = this.value;
            hideAllResourceFields();
            
            if (resourceType === 'human') {
                document.getElementById('humanResourceFields').classList.remove('hidden');
                document.getElementById('roleField').classList.remove('hidden');
                document.querySelector('[name="employee_id"]').required = true;
                document.querySelector('[name="equipment_id"]').required = false;
            } else if (resourceType === 'equipment') {
                document.getElementById('equipmentResourceFields').classList.remove('hidden');
                document.querySelector('[name="equipment_id"]').required = true;
                document.querySelector('[name="employee_id"]').required = false;
            }
        });
        
        // Employee selection change handler
        document.getElementById('employeeSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const rate = selectedOption.getAttribute('data-rate') || 0;
            document.getElementById('hourlyRateInput').value = rate;
        });
        
        // Equipment selection change handler
        document.getElementById('equipmentSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const rate = selectedOption.getAttribute('data-rate') || 0;
            document.getElementById('hourlyRateInput').value = rate;
        });
        
        // Load equipment options
        function loadEquipmentOptions() {
            const equipmentSelect = document.getElementById('equipmentSelect');
            equipmentSelect.innerHTML = '<option value="">Loading equipment...</option>';
            
            fetch('/api/resources/available?type=equipment')
                .then(response => response.json())
                .then(data => {
                    equipmentSelect.innerHTML = '<option value="">Select Equipment</option>';
                    if (data.equipment && data.equipment.length > 0) {
                        data.equipment.forEach(equipment => {
                            const option = document.createElement('option');
                            option.value = equipment.id;
                            option.textContent = `${equipment.name} - $${equipment.hourly_rate}/hr`;
                            option.setAttribute('data-rate', equipment.hourly_rate);
                            equipmentSelect.appendChild(option);
                        });
                    } else {
                        equipmentSelect.innerHTML = '<option value="">No equipment available</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading equipment:', error);
                    equipmentSelect.innerHTML = '<option value="">Error loading equipment</option>';
                });
        }
        
        // Handle resource allocation form submission
        document.getElementById('allocateResourceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.ok) {
                    closeAllocateResourceModal();
                    location.reload(); // Refresh to show new resource
                } else {
                    alert('Error allocating resource. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error allocating resource. Please try again.');
            });
        });
        
        // Edit Resource Function
        function editResource(resourceId) {
            // This would open an edit modal or redirect to edit page
            alert('Edit resource functionality - Resource ID: ' + resourceId);
        }
        
        // Remove Resource Function
        function removeResource(resourceId) {
            if (confirm('Are you sure you want to remove this resource allocation? This action cannot be undone.')) {
                fetch(`/resources/${resourceId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        location.reload(); // Refresh to remove deleted resource
                    } else {
                        alert('Error removing resource. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing resource. Please try again.');
                });
            }
        }

        // BOQ Section Management
        function openAddBOQSectionModal() {
            // Switch to the comprehensive BOQ creation form instead of showing a simple modal
            switchBOQTab('create');
            showNotification('Switched to comprehensive BOQ creation form', 'success');
            
            // Scroll to top of the create form
            setTimeout(() => {
                const createTab = document.getElementById('boq-create');
                if (createTab) {
                    createTab.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 200);
        }

        function closeAddBOQSectionModal() {
            const modal = document.getElementById('addBOQSectionModal');
            if (modal) {
                modal.remove();
            }
        }

        function submitBOQSection(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch(`/projects/{{ $project->id }}/boq/sections`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAddBOQSectionModal();
                    showNotification('BOQ section created successfully!', 'success');
                    location.reload();
                } else {
                    showNotification('Error creating BOQ section', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error creating BOQ section', 'error');
            });
        }

        function exportBOQ() {
            window.open(`/projects/{{ $project->id }}/boq/export`, '_blank');
            showNotification('Exporting BOQ...', 'info');
        }

        function importBOQ() {
            showNotification('BOQ import functionality coming soon!', 'info');
        }

        // Milestone Management
        function openAddMilestoneModal() {
            const modal = document.createElement('div');
            modal.id = 'addMilestoneModal';
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.innerHTML = `
                <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Add Milestone</h3>
                            <button onclick="closeAddMilestoneModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <form id="addMilestoneForm" onsubmit="submitMilestone(event)">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Milestone Name</label>
                                    <input type="text" name="milestone_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                    <input type="date" name="due_date" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_critical" id="is_critical" class="rounded">
                                    <label for="is_critical" class="ml-2 text-sm text-gray-700">Mark as Critical Milestone</label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end space-x-3 mt-6">
                                <button type="button" onclick="closeAddMilestoneModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    <i class="fas fa-plus mr-1"></i>
                                    Add Milestone
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        function closeAddMilestoneModal() {
            const modal = document.getElementById('addMilestoneModal');
            if (modal) {
                modal.remove();
            }
        }

        function submitMilestone(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch(`/projects/{{ $project->id }}/milestones`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAddMilestoneModal();
                    showNotification('Milestone created successfully!', 'success');
                    location.reload();
                } else {
                    showNotification('Error creating milestone', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error creating milestone', 'error');
            });
        }

        // Enhanced Budget Actions
        function exportBudgetReport() {
            showNotification('Generating budget report...', 'info');
            window.open(`/projects/{{ $project->id }}/budget/export`, '_blank');
        }

        function showBudgetAlerts() {
            showNotification('Budget alerts system active!', 'info');
        }

        // Progress Tracking Actions
        function exportProgressReport() {
            showNotification('Generating progress report...', 'info');
            window.open(`/projects/{{ $project->id }}/progress/export`, '_blank');
        }

        function saveProgress() {
            showNotification('Progress saved successfully!', 'success');
        }

        // Survey Management
        function createNewSurvey() {
            showNotification('New survey creation coming soon!', 'info');
        }

        // Auto Reschedule Functionality
        function autoReschedule() {
            showNotification('Auto-rescheduling project timeline...', 'info');
            // Simulate auto-rescheduling process
            setTimeout(() => {
                showNotification('Project timeline optimized successfully!', 'success');
            }, 2000);
        }

        // Enhanced Gantt Actions
        document.addEventListener('DOMContentLoaded', function() {
            const criticalPathCheckbox = document.getElementById('showCriticalPath');
            const autoScheduleCheckbox = document.getElementById('autoSchedule');
            
            if (criticalPathCheckbox) {
                criticalPathCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        showNotification('Critical path visualization enabled', 'info');
                    } else {
                        showNotification('Critical path visualization disabled', 'info');
                    }
                });
            }
            
            if (autoScheduleCheckbox) {
                autoScheduleCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        showNotification('Auto-scheduling enabled', 'info');
                    } else {
                        showNotification('Auto-scheduling disabled', 'info');
                    }
                });
            }
        });

        // Notification System
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white shadow-lg transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-600' : 
                type === 'error' ? 'bg-red-600' : 
                type === 'warning' ? 'bg-yellow-600' : 'bg-blue-600'
            }`;
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${
                        type === 'success' ? 'check-circle' : 
                        type === 'error' ? 'exclamation-circle' : 
                        type === 'warning' ? 'exclamation-triangle' : 'info-circle'
                    } mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Remove after 5 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }, 5000);
        }

        // Additional button functionalities
        function addPhase() {
            showNotification('Add Phase functionality coming soon!', 'info');
        }

        // BOQ Item Management
        function editBOQItem(itemId) {
            showNotification(`Edit BOQ item ${itemId} functionality coming soon!`, 'info');
        }

        function deleteBOQItem(itemId) {
            if (confirm('Are you sure you want to delete this BOQ item?')) {
                showNotification(`Delete BOQ item ${itemId} functionality coming soon!`, 'info');
            }
        }

        // Enhanced tracking functionality
        function exportTrackingReport() {
            showNotification('Exporting tracking report...', 'info');
            window.open(`/projects/{{ $project->id }}/tracking/export`, '_blank');
        }

        // BOQ Tab Management
        function switchBOQTab(tabName) {
            // Remove active class from all BOQ tab buttons
            document.querySelectorAll('.boq-tab-button').forEach(button => {
                button.classList.remove('border-green-500', 'text-green-600', 'font-medium');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Hide all BOQ tab content
            document.querySelectorAll('.boq-tab-content').forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('active');
            });
            
            // Show selected tab content
            const targetContent = document.getElementById(`boq-${tabName}`);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('active');
            }
            
            // Add active class to clicked tab button
            const targetButton = document.querySelector(`[data-boq-tab="${tabName}"]`);
            if (targetButton) {
                targetButton.classList.remove('border-transparent', 'text-gray-500');
                targetButton.classList.add('border-green-500', 'text-green-600', 'font-medium');
            }
            
            // Load content based on tab
            loadBOQTabContent(tabName);
        }

        function loadBOQTabContent(tabName) {
            switch(tabName) {
                case 'create':
                    showNotification('BOQ Create form ready', 'info');
                    break;
                case 'import':
                    showNotification('BOQ Import functionality ready', 'info');
                    break;
                case 'library':
                    showNotification('Loading BOQ Library...', 'info');
                    break;
                case 'sections':
                    showNotification('Loading BOQ Sections...', 'info');
                    break;
                case 'versions':
                    showNotification('Loading BOQ Versions...', 'info');
                    break;
                case 'approvals':
                    showNotification('Loading BOQ Approvals...', 'info');
                    break;
                default:
                    showNotification('Loading BOQ Hub...', 'info');
            }
        }

        // Initialize BOQ tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event listeners to BOQ tab buttons
            document.querySelectorAll('.boq-tab-button').forEach(button => {
                button.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-boq-tab');
                    switchBOQTab(tabName);
                });
            });
        });

        // BOQ specific functions
        function openBOQLibraryModal() {
            showNotification('BOQ Library modal functionality coming soon!', 'info');
        }

        function editBOQSection(sectionId) {
            showNotification(`Edit BOQ section ${sectionId} functionality coming soon!`, 'info');
        }

        function deleteBOQSection(sectionId) {
            if (confirm('Are you sure you want to delete this BOQ section?')) {
                showNotification(`Delete BOQ section ${sectionId} functionality coming soon!`, 'info');
            }
        }

        function createBOQVersion() {
            showNotification('Creating new BOQ version...', 'info');
        }

        function restoreBOQVersion(version) {
            if (confirm(`Are you sure you want to restore BOQ to version ${version}?`)) {
                showNotification(`Restoring BOQ to version ${version}...`, 'info');
            }
        }

        function submitBOQForApproval() {
            showNotification('Submitting BOQ for approval...', 'info');
        }

        // Enhanced BOQ Creation Functions
        let boqSectionCounter = 0;

        function addBOQSection() {
            boqSectionCounter++;
            const sectionId = `section-${boqSectionCounter}`;
            
            const sectionHTML = `
                <div id="${sectionId}" class="border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-lg font-medium text-gray-900">Section ${boqSectionCounter}</h5>
                        <button onclick="removeBOQSection('${sectionId}')" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Section Name *</label>
                            <input type="text" name="sections[${boqSectionCounter}][name]" required 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" 
                                   placeholder="e.g., Electrical Installation">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Section Code</label>
                            <input type="text" name="sections[${boqSectionCounter}][code]" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" 
                                   placeholder="e.g., ELC-001">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Section Description</label>
                        <textarea name="sections[${boqSectionCounter}][description]" rows="2" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" 
                                  placeholder="Detailed description of this section..."></textarea>
                    </div>
                    
                    <div class="border-t pt-4">
                        <div class="flex items-center justify-between mb-4">
                            <h6 class="font-medium text-gray-900">Items</h6>
                            <div class="flex space-x-2">
                                <button type="button" onclick="addItemFromLibrary('${sectionId}')" class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                    <i class="fas fa-book mr-1"></i>From Library
                                </button>
                                <button type="button" onclick="addBOQItem('${sectionId}')" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                    <i class="fas fa-plus mr-1"></i>Add Item
                                </button>
                            </div>
                        </div>
                        
                        <div id="${sectionId}-items" class="space-y-3">
                            <!-- Items will be added here -->
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('boq-sections-container').insertAdjacentHTML('beforeend', sectionHTML);
            updateBOQSummary();
        }

        function removeBOQSection(sectionId) {
            if (confirm('Are you sure you want to remove this section and all its items?')) {
                document.getElementById(sectionId).remove();
                updateBOQSummary();
            }
        }

        function addBOQItem(sectionId) {
            const itemCounter = document.querySelectorAll(`#${sectionId}-items .boq-item`).length + 1;
            const itemId = `${sectionId}-item-${itemCounter}`;
            
            const itemHTML = `
                <div id="${itemId}" class="boq-item bg-gray-50 p-4 rounded border">
                    <div class="flex items-center justify-between mb-3">
                        <h6 class="font-medium text-gray-900">Item ${itemCounter}</h6>
                        <button type="button" onclick="removeBOQItem('${itemId}')" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Item Code *</label>
                            <input type="text" name="${sectionId}[items][${itemCounter}][code]" required 
                                   class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500" 
                                   placeholder="ITM-001">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Description *</label>
                            <input type="text" name="${sectionId}[items][${itemCounter}][description]" required 
                                   class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500" 
                                   placeholder="Item description...">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                            <select name="${sectionId}[items][${itemCounter}][category]" 
                                    class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500">
                                <option value="Materials">Materials</option>
                                <option value="Labor">Labor</option>
                                <option value="Equipment">Equipment</option>
                                <option value="Subcontractor">Subcontractor</option>
                                <option value="Overhead">Overhead</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mt-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Unit *</label>
                            <input type="text" name="${sectionId}[items][${itemCounter}][unit]" required 
                                   class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500" 
                                   placeholder="Each, m², hrs">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Quantity *</label>
                            <input type="number" name="${sectionId}[items][${itemCounter}][quantity]" required min="0" step="0.01" 
                                   class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 item-quantity" 
                                   placeholder="0.00" onchange="calculateItemTotal('${itemId}')">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Unit Rate *</label>
                            <input type="number" name="${sectionId}[items][${itemCounter}][rate]" required min="0" step="0.01" 
                                   class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500 item-rate" 
                                   placeholder="0.00" onchange="calculateItemTotal('${itemId}')">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Total Amount</label>
                            <input type="number" name="${sectionId}[items][${itemCounter}][total]" readonly 
                                   class="w-full text-sm border border-gray-300 rounded px-2 py-1 bg-gray-100 item-total" 
                                   placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                            <select name="${sectionId}[items][${itemCounter}][status]" 
                                    class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500">
                                <option value="Draft">Draft</option>
                                <option value="Approved">Approved</option>
                                <option value="Revised">Revised</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="${sectionId}[items][${itemCounter}][notes]" rows="2" 
                                  class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-green-500" 
                                  placeholder="Additional notes, specifications, or requirements..."></textarea>
                    </div>
                </div>
            `;
            
            document.getElementById(`${sectionId}-items`).insertAdjacentHTML('beforeend', itemHTML);
            updateBOQSummary();
        }

        function removeBOQItem(itemId) {
            document.getElementById(itemId).remove();
            updateBOQSummary();
        }

        function calculateItemTotal(itemId) {
            const itemElement = document.getElementById(itemId);
            const quantity = parseFloat(itemElement.querySelector('.item-quantity').value) || 0;
            const rate = parseFloat(itemElement.querySelector('.item-rate').value) || 0;
            const total = quantity * rate;
            
            itemElement.querySelector('.item-total').value = total.toFixed(2);
            updateBOQSummary();
        }

        function updateBOQSummary() {
            const sections = document.querySelectorAll('#boq-sections-container > div').length;
            const items = document.querySelectorAll('.boq-item').length;
            let totalCost = 0;
            
            document.querySelectorAll('.item-total').forEach(input => {
                totalCost += parseFloat(input.value) || 0;
            });
            
            document.getElementById('section-count').textContent = sections;
            document.getElementById('item-count').textContent = items;
            document.getElementById('total-cost').textContent = '$' + totalCost.toLocaleString('en-US', {minimumFractionDigits: 2});
            document.getElementById('avg-cost').textContent = items > 0 ? '$' + (totalCost / items).toLocaleString('en-US', {minimumFractionDigits: 2}) : '$0.00';
        }

        function addItemFromLibrary(sectionId) {
            // Open library modal to select items
            showNotification('Opening BOQ Library to select items...', 'info');
            // This would open a modal with library items
        }

        function collectBOQData() {
            const data = {
                // Header information
                boq_reference: document.getElementById('boq_reference').value,
                boq_version: document.getElementById('boq_version').value,
                preparation_date: document.getElementById('preparation_date').value,
                prepared_by: document.getElementById('prepared_by').value,
                currency: document.getElementById('currency').value,
                valid_until: document.getElementById('valid_until').value,
                
                // Terms and conditions
                payment_terms: document.getElementById('payment_terms').value,
                delivery_timeline: document.getElementById('delivery_timeline').value,
                warranty_period: document.getElementById('warranty_period').value,
                special_conditions: document.getElementById('special_conditions').value,
                include_taxes: document.getElementById('include_taxes').checked,
                subject_to_approval: document.getElementById('subject_to_approval').checked,
                
                // Sections and items
                sections: [],
                
                // Project information
                project_id: '{{ $project->id }}',
                project_code: '{{ $project->project_code }}'
            };
            
            // Collect sections data
            const sectionElements = document.querySelectorAll('#boq-sections-container > div');
            console.log('Found section elements:', sectionElements.length); // Debug log
            
            sectionElements.forEach((sectionElement, sectionIndex) => {
                const sectionId = sectionElement.id;
                console.log('Processing section:', sectionId); // Debug log
                
                const section = {
                    name: sectionElement.querySelector(`input[name*="[name]"]`)?.value || '',
                    code: sectionElement.querySelector(`input[name*="[code]"]`)?.value || '',
                    description: sectionElement.querySelector(`textarea[name*="[description]"]`)?.value || '',
                    items: []
                };
                
                // Collect items for this section
                const itemElements = sectionElement.querySelectorAll('.boq-item');
                console.log(`Section ${sectionIndex + 1} has ${itemElements.length} items`); // Debug log
                
                itemElements.forEach((itemElement, itemIndex) => {
                    console.log(`Processing item ${itemIndex + 1} in section ${sectionId}`); // Debug log
                    
                    const item = {
                        code: itemElement.querySelector(`input[name*="[code]"]`)?.value || '',
                        description: itemElement.querySelector(`input[name*="[description]"]`)?.value || '',
                        category: itemElement.querySelector(`select[name*="[category]"]`)?.value || '',
                        unit: itemElement.querySelector(`input[name*="[unit]"]`)?.value || '',
                        quantity: parseFloat(itemElement.querySelector(`input[name*="[quantity]"]`)?.value) || 0,
                        rate: parseFloat(itemElement.querySelector(`input[name*="[rate]"]`)?.value) || 0,
                        total: parseFloat(itemElement.querySelector(`input[name*="[total]"]`)?.value) || 0,
                        status: itemElement.querySelector(`select[name*="[status]"]`)?.value || 'Draft',
                        notes: itemElement.querySelector(`textarea[name*="[notes]"]`)?.value || ''
                    };
                    
                    section.items.push(item);
                });
                
                data.sections.push(section);
            });
            
            return data;
        }

        function validateBOQData(data) {
            const errors = [];
            
            if (!data.boq_reference) {
                errors.push('BOQ reference number is required');
            }
            
            if (!data.prepared_by) {
                errors.push('Please select who prepared this BOQ');
            }
            
            if (data.sections.length === 0) {
                errors.push('Please add at least one section to the BOQ');
            }
            
            // Validate sections
            data.sections.forEach((section, sectionIndex) => {
                if (!section.name) {
                    errors.push(`Section ${sectionIndex + 1}: Section name is required`);
                }
                
                if (section.items.length === 0) {
                    errors.push(`Section ${sectionIndex + 1}: Please add at least one item`);
                }
                
                // Validate items
                section.items.forEach((item, itemIndex) => {
                    if (!item.code) {
                        errors.push(`Section ${sectionIndex + 1}, Item ${itemIndex + 1}: Item code is required`);
                    }
                    if (!item.description) {
                        errors.push(`Section ${sectionIndex + 1}, Item ${itemIndex + 1}: Description is required`);
                    }
                    if (!item.unit) {
                        errors.push(`Section ${sectionIndex + 1}, Item ${itemIndex + 1}: Unit is required`);
                    }
                    if (item.quantity <= 0) {
                        errors.push(`Section ${sectionIndex + 1}, Item ${itemIndex + 1}: Quantity must be greater than 0`);
                    }
                    if (item.rate <= 0) {
                        errors.push(`Section ${sectionIndex + 1}, Item ${itemIndex + 1}: Rate must be greater than 0`);
                    }
                });
            });
            
            return errors;
        }

        function saveBOQDraft() {
            const data = collectBOQData();
            const errors = validateBOQData(data);
            
            // For draft, we allow incomplete data but show warnings
            if (errors.length > 0) {
                const proceed = confirm(`This BOQ has ${errors.length} incomplete fields. Save as draft anyway?\n\nIssues:\n${errors.slice(0, 5).join('\n')}${errors.length > 5 ? '\n...and more' : ''}`);
                if (!proceed) return;
            }
            
            data.status = 'Draft';
            
            const saveButton = document.querySelector('button[onclick="saveBOQDraft()"]');
            const originalText = saveButton.innerHTML;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving Draft...';
            saveButton.disabled = true;
            
            fetch('/api/boq/save-draft', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showNotification('BOQ saved as draft successfully!', 'success');
                    // Optionally redirect to BOQ list or stay on form
                } else {
                    showNotification(result.message || 'Failed to save BOQ draft', 'error');
                }
            })
            .catch(error => {
                console.error('Error saving BOQ draft:', error);
                showNotification('Error saving BOQ draft. Please try again.', 'error');
            })
            .finally(() => {
                saveButton.innerHTML = originalText;
                saveButton.disabled = false;
            });
        }

        function previewBOQ() {
            const data = collectBOQData();
            const errors = validateBOQData(data);
            
            if (errors.length > 0) {
                showNotification(`Cannot preview BOQ with ${errors.length} errors. Please fix the issues first.`, 'error');
                return;
            }
            
            const previewButton = document.querySelector('button[onclick="previewBOQ()"]');
            const originalText = previewButton.innerHTML;
            previewButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating Preview...';
            previewButton.disabled = true;
            
            showNotification('Generating BOQ preview...', 'info');
            
            fetch('/api/boq/preview', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (response.ok) {
                    return response.blob();
                } else {
                    throw new Error('Preview generation failed');
                }
            })
            .then(blob => {
                // Create a URL for the blob and open in new window
                const url = window.URL.createObjectURL(blob);
                const newWindow = window.open(url, '_blank');
                
                if (!newWindow) {
                    // Fallback: download the file
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `BOQ_Preview_${data.boq_reference || 'Draft'}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    showNotification('BOQ preview downloaded successfully!', 'success');
                } else {
                    showNotification('BOQ preview opened in new window', 'success');
                }
                
                // Clean up the URL
                setTimeout(() => window.URL.revokeObjectURL(url), 1000);
            })
            .catch(error => {
                console.error('Error generating BOQ preview:', error);
                showNotification('Error generating BOQ preview. Please try again.', 'error');
            })
            .finally(() => {
                previewButton.innerHTML = originalText;
                previewButton.disabled = false;
            });
        }

        function submitBOQ() {
            const data = collectBOQData();
            console.log('BOQ Data collected:', data); // Debug log
            
            const errors = validateBOQData(data);
            console.log('Validation errors:', errors); // Debug log
            
            if (errors.length > 0) {
                showNotification(`Cannot create BOQ with ${errors.length} errors:`, 'error');
                errors.slice(0, 3).forEach(error => {
                    setTimeout(() => showNotification(error, 'error'), 100);
                });
                return;
            }
            
            data.status = 'Active';
            
            const submitButton = document.querySelector('button[onclick="submitBOQ()"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating BOQ...';
            submitButton.disabled = true;
            
            console.log('Sending data to API:', JSON.stringify(data, null, 2)); // Debug log
            
            fetch('/api/boq/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('API Response status:', response.status); // Debug log
                return response.json();
            })
            .then(result => {
                console.log('API Response result:', result); // Debug log
                if (result.success) {
                    showNotification('BOQ created successfully!', 'success');
                    
                    // Redirect to BOQ view or project page after short delay
                    setTimeout(() => {
                        if (result.boq_id) {
                            window.location.href = `/projects/${data.project_id}/boq/${result.boq_id}`;
                        } else {
                            // Switch to BOQ hub to see the created BOQ
                            switchBOQTab('hub');
                            // Refresh the page to load new data
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    }, 2000);
                } else {
                    showNotification(result.message || 'Failed to create BOQ', 'error');
                }
            })
            .catch(error => {
                console.error('Error creating BOQ:', error);
                showNotification('Error creating BOQ. Please try again.', 'error');
            })
            .finally(() => {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        }

        // BOQ Number Generation Functions
        function generateBOQNumber() {
            const format = document.getElementById('numbering_format_select')?.value || 'datetime';
            generateBOQNumberByFormat(format);
        }

        function toggleAutoGenerate() {
            const checkbox = document.getElementById('auto_generate_boq');
            const input = document.getElementById('boq_reference');
            const formatDiv = document.getElementById('numbering-format');
            
            if (checkbox.checked) {
                generateBOQNumber();
                input.setAttribute('readonly', true);
                input.classList.add('bg-gray-100');
                formatDiv.classList.remove('hidden');
                showNotification('Auto-generation enabled. BOQ numbers will be generated automatically.', 'info');
            } else {
                input.removeAttribute('readonly');
                input.classList.remove('bg-gray-100');
                input.value = '';
                formatDiv.classList.add('hidden');
                showNotification('Auto-generation disabled. You can enter custom BOQ numbers.', 'info');
            }
        }

        function updateNumberingFormat() {
            const format = document.getElementById('numbering_format_select').value;
            generateBOQNumberByFormat(format);
        }

        function generateBOQNumberByFormat(format = null) {
            if (!format) {
                format = document.getElementById('numbering_format_select').value || 'datetime';
            }
            
            const projectCode = '{{ $project->project_code }}';
            const currentDate = new Date();
            const year = currentDate.getFullYear();
            const month = String(currentDate.getMonth() + 1).padStart(2, '0');
            const day = String(currentDate.getDate()).padStart(2, '0');
            
            let boqNumber;
            
            switch(format) {
                case 'sequential':
                    generateSequentialBOQNumber();
                    return;
                    
                case 'yearseq':
                    const yearSeqId = Math.floor(Math.random() * 999) + 1;
                    const paddedYearSeq = String(yearSeqId).padStart(3, '0');
                    boqNumber = `BOQ-${projectCode}-${year}-${paddedYearSeq}`;
                    break;
                    
                case 'datetime':
                default:
                    const timestamp = currentDate.getTime().toString().slice(-4);
                    boqNumber = `BOQ-${projectCode}-${year}${month}${day}-${timestamp}`;
                    break;
            }
            
            document.getElementById('boq_reference').value = boqNumber;
            showNotification(`Generated BOQ number: ${boqNumber}`, 'success');
        }

        function getNextBOQSequence(projectId) {
            // This would typically make an API call to get the next sequence number
            // For now, we'll simulate with a simple counter
            return fetch(`/api/projects/${projectId}/next-boq-sequence`)
                .then(response => response.json())
                .then(data => data.next_sequence || 1)
                .catch(() => 1); // Fallback to 1 if API fails
        }

        function generateSequentialBOQNumber() {
            const projectCode = '{{ $project->project_code }}';
            const projectId = '{{ $project->id }}';
            
            getNextBOQSequence(projectId).then(sequence => {
                const paddedSequence = String(sequence).padStart(3, '0');
                const boqNumber = `BOQ-${projectCode}-${paddedSequence}`;
                
                document.getElementById('boq_reference').value = boqNumber;
                showNotification(`Generated sequential BOQ number: ${boqNumber}`, 'success');
            });
        }

        // Initialize with one section
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-add first section when switching to create tab
            const createTab = document.querySelector('[data-boq-tab="create"]');
            if (createTab) {
                createTab.addEventListener('click', function() {
                    setTimeout(() => {
                        if (document.querySelectorAll('#boq-sections-container > div').length === 0) {
                            addBOQSection();
                        }
                        
                        // Auto-generate BOQ number if auto-generate is enabled
                        const autoGenerate = document.getElementById('auto_generate_boq');
                        if (autoGenerate && autoGenerate.checked) {
                            generateBOQNumber();
                        }
                    }, 100);
                });
            }
            
            // Check for auto-generate preference from localStorage
            const autoGeneratePreference = localStorage.getItem('boq_auto_generate_{{ $project->id }}');
            if (autoGeneratePreference === 'true') {
                document.getElementById('auto_generate_boq').checked = true;
                toggleAutoGenerate();
            }
        });

        // Save auto-generate preference
        function saveAutoGeneratePreference() {
            const checkbox = document.getElementById('auto_generate_boq');
            localStorage.setItem('boq_auto_generate_{{ $project->id }}', checkbox.checked);
        }

        // Add event listener to save preference when checkbox changes
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('auto_generate_boq');
            if (checkbox) {
                checkbox.addEventListener('change', saveAutoGeneratePreference);
            }
        });

        // New Engineer Management Functions
        function handlePreparedByChange() {
            const select = document.getElementById('prepared_by');
            const newEngineerFields = document.getElementById('new_engineer_fields');
            
            if (select.value === 'add_new') {
                newEngineerFields.classList.remove('hidden');
                // Clear and focus on first name field
                clearNewEngineerFields();
                setTimeout(() => {
                    document.getElementById('new_engineer_first_name').focus();
                }, 100);
            } else {
                newEngineerFields.classList.add('hidden');
            }
        }

        function cancelAddNewEngineer() {
            const select = document.getElementById('prepared_by');
            const newEngineerFields = document.getElementById('new_engineer_fields');
            
            // Reset select to empty
            select.value = '';
            // Hide the form
            newEngineerFields.classList.add('hidden');
            // Clear the fields
            clearNewEngineerFields();
        }

        function clearNewEngineerFields() {
            document.getElementById('new_engineer_first_name').value = '';
            document.getElementById('new_engineer_last_name').value = '';
            document.getElementById('new_engineer_email').value = '';
            document.getElementById('new_engineer_position').value = '';
            document.getElementById('new_engineer_phone').value = '';
            document.getElementById('new_engineer_department').value = '';
        }

        function validateNewEngineerFields() {
            const firstName = document.getElementById('new_engineer_first_name').value.trim();
            const lastName = document.getElementById('new_engineer_last_name').value.trim();
            const email = document.getElementById('new_engineer_email').value.trim();
            const position = document.getElementById('new_engineer_position').value;

            if (!firstName) {
                showNotification('Please enter first name', 'error');
                document.getElementById('new_engineer_first_name').focus();
                return false;
            }

            if (!lastName) {
                showNotification('Please enter last name', 'error');
                document.getElementById('new_engineer_last_name').focus();
                return false;
            }

            if (!email) {
                showNotification('Please enter email address', 'error');
                document.getElementById('new_engineer_email').focus();
                return false;
            }

            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showNotification('Please enter a valid email address', 'error');
                document.getElementById('new_engineer_email').focus();
                return false;
            }

            if (!position) {
                showNotification('Please select a position', 'error');
                document.getElementById('new_engineer_position').focus();
                return false;
            }

            return true;
        }

        function saveNewEngineer() {
            if (!validateNewEngineerFields()) {
                return;
            }

            const engineerData = {
                first_name: document.getElementById('new_engineer_first_name').value.trim(),
                last_name: document.getElementById('new_engineer_last_name').value.trim(),
                email: document.getElementById('new_engineer_email').value.trim(),
                position: document.getElementById('new_engineer_position').value,
                phone: document.getElementById('new_engineer_phone').value.trim(),
                department: document.getElementById('new_engineer_department').value,
                status: 'active'
            };

            // Generate employee code
            const firstInitial = engineerData.first_name.charAt(0).toUpperCase();
            const lastInitial = engineerData.last_name.charAt(0).toUpperCase();
            const randomNumber = Math.floor(Math.random() * 999) + 1;
            engineerData.employee_code = `ENG${firstInitial}${lastInitial}${randomNumber.toString().padStart(3, '0')}`;

            // Show loading state
            const saveButton = document.querySelector('#new_engineer_fields button[onclick="saveNewEngineer()"]');
            const originalText = saveButton.innerHTML;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Adding...';
            saveButton.disabled = true;

            // Make API call to create engineer
            fetch('/api/employees', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(engineerData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Add new option to select
                    const select = document.getElementById('prepared_by');
                    const newOption = document.createElement('option');
                    newOption.value = data.employee.id;
                    newOption.textContent = `${data.employee.first_name} ${data.employee.last_name} (${data.employee.position})`;
                    
                    // Insert before "Add New Engineer" option
                    const addNewOption = select.querySelector('option[value="add_new"]');
                    select.insertBefore(newOption, addNewOption);
                    
                    // Select the new engineer
                    select.value = data.employee.id;
                    
                    // Hide the form
                    document.getElementById('new_engineer_fields').classList.add('hidden');
                    clearNewEngineerFields();
                    
                    showNotification(`Engineer ${data.employee.first_name} ${data.employee.last_name} added successfully!`, 'success');
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        const errors = Object.values(data.errors).flat();
                        errors.forEach(error => showNotification(error, 'error'));
                    } else {
                        showNotification(data.message || 'Failed to add engineer', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error adding engineer:', error);
                showNotification('Error adding engineer. Please try again.', 'error');
            })
            .finally(() => {
                // Restore button state
                saveButton.innerHTML = originalText;
                saveButton.disabled = false;
            });
        }
    </script>
</body>
</html>
