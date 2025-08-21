<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Projects - Donezo Project Management</title>
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
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Projects</h1>
                        <p class="text-gray-600 text-sm">Manage all your engineering and construction projects</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button id="addProjectBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>New Project</span>
                        </button>
                        <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                        @include('partials.user-menu')
                    </div>
                </div>
            </header>

            <!-- Projects Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Filters and Search -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <form method="GET" action="{{ route('projects.index') }}" id="filterForm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 w-64">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                                <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">All Status</option>
                                    <option value="Planned" {{ request('status') === 'Planned' ? 'selected' : '' }}>Planned</option>
                                    <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="On Hold" {{ request('status') === 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                    <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                                <select name="client_id" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">All Clients</option>
                                @if(isset($clients))
                                    @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-search mr-1"></i> Filter
                                </button>
                                @if(request()->hasAny(['search', 'status', 'client_id']))
                                    <a href="{{ route('projects.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-times mr-1"></i> Clear
                                    </a>
                                @endif
                        </div>
                        <div class="flex items-center space-x-2">
                                <select name="sort_by" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                                    <option value="project_name" {{ request('sort_by') === 'project_name' ? 'selected' : '' }}>Sort by Name</option>
                                    <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Sort by Status</option>
                                    <option value="progress_percent" {{ request('sort_by') === 'progress_percent' ? 'selected' : '' }}>Sort by Progress</option>
                                    <option value="end_date" {{ request('sort_by') === 'end_date' ? 'selected' : '' }}>Sort by Due Date</option>
                                </select>
                                <button type="button" onclick="toggleSortOrder()" class="bg-gray-100 p-2 rounded-lg hover:bg-gray-200 transition-colors" title="Toggle Sort Order">
                                    <i class="fas fa-sort-amount-{{ request('sort_order', 'desc') === 'desc' ? 'down' : 'up' }}"></i>
                            </button>
                                <input type="hidden" name="sort_order" id="sortOrder" value="{{ request('sort_order', 'desc') }}">
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Projects Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @if(isset($projects) && $projects->count() > 0)
                        @foreach($projects as $project)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow cursor-pointer" onclick="window.location.href='{{ route('projects.show', $project) }}'">
                            <div class="p-6">
                                <!-- Project Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $project->project_name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $project->project_code }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($project->status === 'In Progress') bg-green-100 text-green-800
                                        @elseif($project->status === 'Planning') bg-blue-100 text-blue-800
                                        @elseif($project->status === 'Completed') bg-gray-100 text-gray-800
                                        @elseif($project->status === 'On Hold') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $project->status }}
                                    </span>
                                </div>

                                <!-- Project Description -->
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $project->description ?? 'No description available' }}</p>

                                <!-- Progress Bar -->
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Progress</span>
                                        <span>{{ $project->progress_percent ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ $project->progress_percent ?? 0 }}%"></div>
                                    </div>
                                </div>

                                <!-- Project Details -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500">Client:</span>
                                        <span class="font-medium">{{ $project->client->company_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500">Budget:</span>
                                        <span class="font-medium">${{ number_format($project->total_budget ?? 0) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500">Due Date:</span>
                                        <span class="font-medium">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d, Y') : 'N/A' }}</span>
                                    </div>
                                </div>

                                <!-- Project Actions -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex -space-x-2">
                                            @for($i = 0; $i < min(3, rand(1, 4)); $i++)
                                            <img src="https://via.placeholder.com/24x24/{{ ['10b981', '3b82f6', '8b5cf6', 'f59e0b'][$i] }}/ffffff?text={{ chr(65 + $i) }}" 
                                                 class="w-6 h-6 rounded-full border-2 border-white">
                                            @endfor
                                        </div>
                                        <span class="text-xs text-gray-500">{{ rand(2, 6) }} members</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('projects.show', $project) }}" onclick="event.stopPropagation()" class="text-gray-400 hover:text-blue-600 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project) }}" onclick="event.stopPropagation()" class="text-gray-400 hover:text-green-600 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="event.stopPropagation(); deleteProject({{ $project->id }})" class="text-gray-400 hover:text-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <!-- Empty State -->
                        <div class="col-span-full">
                            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-project-diagram text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Projects Yet</h3>
                                <p class="text-gray-500 mb-6">Get started by creating your first project</p>
                                <button class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Create First Project
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if(isset($projects) && $projects->hasPages())
                <div class="mt-8 flex justify-center">
                    <div class="bg-white rounded-lg shadow-sm p-4">
                    {{ $projects->links() }}
                    </div>
                </div>
                @endif

                <!-- Results Info -->
                <div class="mt-4 text-center text-sm text-gray-500">
                    @if($projects->total() > 0)
                        Showing {{ $projects->firstItem() }} to {{ $projects->lastItem() }} of {{ $projects->total() }} projects
                        @if(request()->hasAny(['search', 'status', 'client_id']))
                            (filtered results)
                        @endif
                    @else
                        No projects found
                        @if(request()->hasAny(['search', 'status', 'client_id']))
                            matching your filters
                        @endif
                    @endif
                </div>
            </main>
        </div>
    </div>

    <!-- Add Project Modal -->
    <div id="addProjectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Create New Project</h2>
                </div>
                <form id="projectForm" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Name *</label>
                            <input type="text" name="project_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Type *</label>
                            <select name="project_type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select Type</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Procurement">Procurement</option>
                                <option value="Installation">Installation</option>
                                <option value="EPC">EPC</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Costing Type *</label>
                            <select name="costing_type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="Tender/Proposal">Tender/Proposal</option>
                                <option value="Merchandise">Merchandise</option>
                                <option value="Service Sales">Service Sales</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Client *</label>
                            <select name="client_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select Client</option>
                                @if(isset($clients))
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                            <input type="date" name="start_date" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                            <input type="date" name="end_date" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Manager *</label>
                            <select name="project_manager_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Select Manager</option>
                                @if(isset($employees))
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->full_name }} - {{ $employee->position }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="Planned">Planned</option>
                                <option value="In Progress">In Progress</option>
                                <option value="On Hold">On Hold</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Budget</label>
                            <input type="number" name="total_budget" step="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" id="cancelBtn" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Create Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const addProjectBtn = document.getElementById('addProjectBtn');
        const addProjectModal = document.getElementById('addProjectModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const projectForm = document.getElementById('projectForm');

        addProjectBtn.addEventListener('click', () => {
            addProjectModal.classList.remove('hidden');
        });

        cancelBtn.addEventListener('click', () => {
            addProjectModal.classList.add('hidden');
            projectForm.reset();
        });

        addProjectModal.addEventListener('click', (e) => {
            if (e.target === addProjectModal) {
                addProjectModal.classList.add('hidden');
                projectForm.reset();
            }
        });

        // Form submission
        projectForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(projectForm);
            
            try {
                const response = await fetch('/api/projects', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });

                if (response.ok) {
                    addProjectModal.classList.add('hidden');
                    projectForm.reset();
                    location.reload(); // Refresh the page to show new project
                } else {
                    alert('Error creating project. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error creating project. Please try again.');
            }
        });

        // Delete project function
        function deleteProject(projectId) {
            if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
                fetch(`/api/projects/${projectId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting project. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting project. Please try again.');
                });
            }
        }

        // Toggle sort order
        function toggleSortOrder() {
            const sortOrderInput = document.getElementById('sortOrder');
            const currentOrder = sortOrderInput.value;
            sortOrderInput.value = currentOrder === 'desc' ? 'asc' : 'desc';
            document.getElementById('filterForm').submit();
        }

        // Auto-submit form when sort option changes
        document.querySelector('select[name="sort_by"]').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Auto-submit form when filters change
        document.querySelector('select[name="status"]').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        document.querySelector('select[name="client_id"]').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Submit form on Enter key in search input
        document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });
    </script>
</body>
</html>

