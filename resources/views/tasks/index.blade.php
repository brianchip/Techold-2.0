<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - Donezo Project Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .gradient-dark { background: linear-gradient(135deg, #1f2937 0%, #111827 100%); }
        .sidebar-item:hover { background-color: rgba(255, 255, 255, 0.1); }
        .task-priority-high { border-left: 4px solid #ef4444; }
        .task-priority-medium { border-left: 4px solid #f59e0b; }
        .task-priority-low { border-left: 4px solid #10b981; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar', ['active' => 'tasks'])

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tasks & Work Breakdown Structure</h1>
                        <p class="text-gray-600 text-sm">Manage project tasks, dependencies, and progress tracking</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button id="addTaskBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>New Task</span>
                        </button>
                        <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chart-gantt mr-2"></i>
                            Gantt View
                        </button>
                        @include('partials.user-menu')
                    </div>
                </div>
            </header>

            <!-- Tasks Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Filters and Views -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <input type="text" placeholder="Search tasks..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 w-64">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                            <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option>All Projects</option>
                                @if(isset($projects))
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option>All Status</option>
                                <option>Not Started</option>
                                <option>In Progress</option>
                                <option>Completed</option>
                                <option>On Hold</option>
                                <option>Cancelled</option>
                            </select>
                            <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option>All Priorities</option>
                                <option>High</option>
                                <option>Medium</option>
                                <option>Low</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="bg-green-100 text-green-800 px-3 py-2 rounded-lg text-sm font-medium">
                                <i class="fas fa-list mr-2"></i>
                                List View
                            </button>
                            <button class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                <i class="fas fa-th-large mr-2"></i>
                                Board View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Task Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-lg mr-4">
                                <i class="fas fa-tasks text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Tasks</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $taskStats['total'] ?? 156 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-lg mr-4">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Completed</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $taskStats['completed'] ?? 89 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-100 rounded-lg mr-4">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">In Progress</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $taskStats['in_progress'] ?? 45 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-100 rounded-lg mr-4">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Overdue</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $taskStats['overdue'] ?? 12 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tasks List -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Task List</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if(isset($tasks) && $tasks->count() > 0)
                                    @foreach($tasks as $task)
                                    <tr class="hover:bg-gray-50 task-priority-{{ strtolower($task->priority ?? 'medium') }}">
                                        <td class="px-6 py-4">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">{{ $task->task_name }}</h4>
                                                <p class="text-sm text-gray-500">{{ $task->task_code }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-gray-900">{{ $task->project->project_name ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <img src="https://via.placeholder.com/32x32/10b981/ffffff?text={{ substr($task->assignee_name ?? 'A', 0, 1) }}" class="w-8 h-8 rounded-full mr-3">
                                                <span class="text-sm text-gray-900">{{ $task->assignee_name ?? 'Unassigned' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if(($task->priority ?? 'Medium') === 'High') bg-red-100 text-red-800
                                                @elseif(($task->priority ?? 'Medium') === 'Medium') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ $task->priority ?? 'Medium' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($task->status === 'Completed') bg-green-100 text-green-800
                                                @elseif($task->status === 'In Progress') bg-blue-100 text-blue-800
                                                @elseif($task->status === 'On Hold') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $task->status ?? 'Not Started' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-gray-900">
                                                {{ $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('M d, Y') : 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $task->progress_percent ?? 0 }}%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">{{ $task->progress_percent ?? 0 }}%</span>
                                            </div>
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
                                    @endforeach
                                @else
                                    @for($i = 0; $i < 10; $i++)
                                    <tr class="hover:bg-gray-50 task-priority-{{ ['high', 'medium', 'low'][rand(0, 2)] }}">
                                        <td class="px-6 py-4">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">{{ ['Database Design', 'API Development', 'Frontend Integration', 'Testing Phase', 'Documentation', 'Code Review', 'Deployment Setup', 'Security Audit'][rand(0, 7)] }}</h4>
                                                <p class="text-sm text-gray-500">TASK-{{ str_pad($i + 1, 3, '0', STR_PAD_LEFT) }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-gray-900">{{ ['ERP System', 'Website Redesign', 'Mobile App', 'Data Migration'][rand(0, 3)] }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <img src="https://via.placeholder.com/32x32/{{ ['10b981', '3b82f6', '8b5cf6', 'f59e0b'][rand(0, 3)] }}/ffffff?text={{ chr(65 + rand(0, 25)) }}" class="w-8 h-8 rounded-full mr-3">
                                                <span class="text-sm text-gray-900">{{ ['John Doe', 'Jane Smith', 'Mike Wilson', 'Sarah Davis'][rand(0, 3)] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php $priority = ['High', 'Medium', 'Low'][rand(0, 2)]; @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($priority === 'High') bg-red-100 text-red-800
                                                @elseif($priority === 'Medium') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ $priority }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php $status = ['Not Started', 'In Progress', 'Completed', 'On Hold'][rand(0, 3)]; @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($status === 'Completed') bg-green-100 text-green-800
                                                @elseif($status === 'In Progress') bg-blue-100 text-blue-800
                                                @elseif($status === 'On Hold') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-gray-900">{{ \Carbon\Carbon::now()->addDays(rand(1, 30))->format('M d, Y') }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php $progress = rand(0, 100); @endphp
                                            <div class="flex items-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">{{ $progress }}%</span>
                                            </div>
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
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div id="addTaskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-screen overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Create New Task</h2>
                </div>
                <form id="taskForm" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Task Name *</label>
                            <input type="text" name="task_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project *</label>
                            <select name="project_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select Project</option>
                                @if(isset($projects))
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <select name="priority" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="On Hold">On Hold</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" name="end_date" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assignee</label>
                            <input type="text" name="assignee_name" placeholder="Enter assignee name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Hours</label>
                            <input type="number" name="estimated_hours" step="0.5" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" id="cancelTaskBtn" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Create Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const addTaskBtn = document.getElementById('addTaskBtn');
        const addTaskModal = document.getElementById('addTaskModal');
        const cancelTaskBtn = document.getElementById('cancelTaskBtn');
        const taskForm = document.getElementById('taskForm');

        addTaskBtn.addEventListener('click', () => {
            addTaskModal.classList.remove('hidden');
        });

        cancelTaskBtn.addEventListener('click', () => {
            addTaskModal.classList.add('hidden');
            taskForm.reset();
        });

        addTaskModal.addEventListener('click', (e) => {
            if (e.target === addTaskModal) {
                addTaskModal.classList.add('hidden');
                taskForm.reset();
            }
        });

        // Form submission
        taskForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(taskForm);
            
            try {
                const response = await fetch('/api/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });

                if (response.ok) {
                    addTaskModal.classList.add('hidden');
                    taskForm.reset();
                    location.reload();
                } else {
                    alert('Error creating task. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error creating task. Please try again.');
            }
        });
    </script>
</body>
</html>

