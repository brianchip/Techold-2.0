<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Project - {{ $project->project_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen bg-gray-100">
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
                            <a href="{{ route('projects.show', $project) }}" class="hover:text-gray-700">{{ $project->project_name }}</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-chevron-right mx-2 text-xs"></i>
                            <span class="text-gray-900 font-medium">Edit</span>
                        </li>
                    </ol>
                </nav>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('projects.show', $project) }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Edit Project</h1>
                            <p class="text-gray-600 text-sm">{{ $project->project_code }} • {{ $project->project_name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        @include('partials.user-menu')
                    </div>
                </div>
            </header>

            <!-- Edit Form Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-4xl mx-auto">
                    <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Basic Information</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Name *</label>
                                    <input type="text" name="project_name" value="{{ old('project_name', $project->project_name) }}" required 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('project_name') border-red-500 @enderror">
                                    @error('project_name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Code</label>
                                    <input type="text" value="{{ $project->project_code }}" disabled 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 text-gray-500">
                                    <p class="text-xs text-gray-500 mt-1">Project code cannot be changed</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Type *</label>
                                    <select name="project_type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="Engineering" {{ old('project_type', $project->project_type) === 'Engineering' ? 'selected' : '' }}>Engineering</option>
                                        <option value="Procurement" {{ old('project_type', $project->project_type) === 'Procurement' ? 'selected' : '' }}>Procurement</option>
                                        <option value="Installation" {{ old('project_type', $project->project_type) === 'Installation' ? 'selected' : '' }}>Installation</option>
                                        <option value="EPC" {{ old('project_type', $project->project_type) === 'EPC' ? 'selected' : '' }}>EPC</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Costing Type *</label>
                                    <select name="costing_type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="Tender/Proposal" {{ old('costing_type', $project->costing_type) === 'Tender/Proposal' ? 'selected' : '' }}>Tender/Proposal</option>
                                        <option value="Merchandise" {{ old('costing_type', $project->costing_type) === 'Merchandise' ? 'selected' : '' }}>Merchandise</option>
                                        <option value="Service Sales" {{ old('costing_type', $project->costing_type) === 'Service Sales' ? 'selected' : '' }}>Service Sales</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Client *</label>
                                    <select name="client_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                    <select name="status" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="Planned" {{ old('status', $project->status) === 'Planned' ? 'selected' : '' }}>Planned</option>
                                        <option value="In Progress" {{ old('status', $project->status) === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="On Hold" {{ old('status', $project->status) === 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                        <option value="Completed" {{ old('status', $project->status) === 'Completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="Cancelled" {{ old('status', $project->status) === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="description" rows="4" 
                                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                          placeholder="Project description...">{{ old('description', $project->description) }}</textarea>
                            </div>
                        </div>

                        <!-- Project Dates and Budget -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Dates and Budget</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                                    <input type="date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" required 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                                    <input type="date" name="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}" required 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Budget</label>
                                    <input type="number" name="total_budget" value="{{ old('total_budget', $project->total_budget) }}" step="0.01" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Procurement Budget</label>
                                    <input type="number" name="procurement_budget" value="{{ old('procurement_budget', $project->procurement_budget) }}" step="0.01" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                            </div>
                        </div>

                        <!-- Team Assignment -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Team Assignment</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Manager *</label>
                                    <select name="project_manager_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('project_manager_id', $project->project_manager_id) == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }} - {{ $employee->position }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Prime Mover (Engineer)</label>
                                    <select name="prime_mover_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="">Select Prime Mover</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('prime_mover_id', $project->prime_mover_id) == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }} - {{ $employee->position }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Project Location -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Location & Additional Info</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                    <input type="text" name="location" value="{{ old('location', $project->location) }}" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                           placeholder="Project location">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Progress (%)</label>
                                    <input type="number" name="progress_percent" value="{{ old('progress_percent', $project->progress_percent) }}" 
                                           min="0" max="100" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                            </div>

                            <!-- Emergency Procurement -->
                            <div class="mt-6">
                                <label class="flex items-center">
                                    <input type="checkbox" name="emergency_procurement" value="1" 
                                           {{ old('emergency_procurement', $project->emergency_procurement) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Emergency Procurement (≤4 hours)</span>
                                </label>
                                
                                <div class="mt-3" id="emergencyJustification" style="{{ old('emergency_procurement', $project->emergency_procurement) ? 'display: block;' : 'display: none;' }}">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Justification</label>
                                    <textarea name="emergency_justification" rows="2" 
                                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                              placeholder="Explain why emergency procurement is needed...">{{ old('emergency_justification', $project->emergency_justification) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('projects.show', $project) }}" 
                                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                    Cancel
                                </a>
                                <div class="flex items-center space-x-4">
                                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-save mr-2"></i>
                                        Update Project
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Toggle emergency justification field
        const emergencyCheckbox = document.querySelector('input[name="emergency_procurement"]');
        const justificationDiv = document.getElementById('emergencyJustification');
        
        emergencyCheckbox.addEventListener('change', function() {
            justificationDiv.style.display = this.checked ? 'block' : 'none';
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const startDate = new Date(document.querySelector('input[name="start_date"]').value);
            const endDate = new Date(document.querySelector('input[name="end_date"]').value);
            
            if (endDate < startDate) {
                e.preventDefault();
                alert('End date must be after start date');
                return false;
            }
        });
    </script>
</body>
</html>
