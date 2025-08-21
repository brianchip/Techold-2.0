<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Project Costing Dashboard - Techold</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .gradient-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .gradient-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    </style>
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
                            <span class="text-gray-900 font-medium">{{ $project->project_name }}</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-chevron-right mx-2 text-xs"></i>
                            <span class="text-gray-900 font-medium">Costing Dashboard</span>
                        </li>
                    </ol>
                </nav>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('projects.show', $project) }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Project Costing Dashboard</h1>
                            <p class="text-gray-600 text-sm">{{ $project->project_code }} • {{ $project->costing_type }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            @if($project->isFullyApproved()) bg-green-100 text-green-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            @if($project->isFullyApproved())
                                <i class="fas fa-check mr-1"></i> Fully Approved
                            @else
                                <i class="fas fa-clock mr-1"></i> Pending Approval
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if(!$project->isFullyApproved())
                            <button onclick="submitForApproval()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Submit for Approval
                            </button>
                        @endif
                        @include('partials.user-menu')
                    </div>
                </div>
            </header>

            <!-- Costing Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Costing Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Budget Overview -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-dollar-sign text-blue-600 text-xl"></i>
                            </div>
                            <span class="text-sm text-gray-500">Total Budget</span>
                        </div>
                        <div class="text-2xl font-bold text-gray-900">${{ number_format($project->total_budget, 2) }}</div>
                        <p class="text-sm text-gray-500 mt-1">Procurement: ${{ number_format($project->procurement_budget, 2) }}</p>
                    </div>

                    <!-- Actual Cost -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-receipt text-green-600 text-xl"></i>
                            </div>
                            <span class="text-sm text-gray-500">Actual Cost</span>
                        </div>
                        <div class="text-2xl font-bold text-gray-900">${{ number_format($project->actual_cost, 2) }}</div>
                        <p class="text-sm text-gray-500 mt-1">Procurement: ${{ number_format($project->actual_procurement_cost, 2) }}</p>
                    </div>

                    <!-- Variance -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-orange-100 p-3 rounded-lg">
                                <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                            </div>
                            <span class="text-sm text-gray-500">Variance</span>
                        </div>
                        <div class="text-2xl font-bold {{ $project->budget_variance >= 0 ? 'text-red-600' : 'text-green-600' }}">
                            ${{ number_format(abs($project->budget_variance), 2) }}
                        </div>
                        <p class="text-sm {{ $project->budget_variance >= 0 ? 'text-red-500' : 'text-green-500' }} mt-1">
                            {{ $project->budget_variance >= 0 ? 'Over' : 'Under' }} Budget ({{ number_format(abs($project->budget_variance_percent), 1) }}%)
                        </p>
                    </div>

                    <!-- Quotes Status -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-file-invoice text-purple-600 text-xl"></i>
                            </div>
                            <span class="text-sm text-gray-500">Quotes</span>
                        </div>
                        <div class="text-2xl font-bold text-gray-900">{{ $project->quotes()->count() }}</div>
                        <p class="text-sm {{ $project->hasMinimumQuotes() ? 'text-green-500' : 'text-red-500' }} mt-1">
                            Min {{ $project->getMinimumQuotesRequired() }} required
                            @if($project->hasMinimumQuotes())
                                <i class="fas fa-check ml-1"></i>
                            @else
                                <i class="fas fa-times ml-1"></i>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Approval Workflow Section -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Approval Workflow</h2>
                    
                    <div class="space-y-4">
                        <!-- Prime Mover -->
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Prime Mover (Engineer)</h4>
                                <p class="text-sm text-gray-500">
                                    {{ $project->primeMover->full_name ?? 'Not assigned' }}
                                    @if($project->primeMover)
                                        - {{ $project->primeMover->position }}
                                    @endif
                                </p>
                            </div>
                            <span class="text-green-600">
                                <i class="fas fa-check"></i> Compiled
                            </span>
                        </div>

                        <!-- Engineering Manager -->
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 {{ $project->engineering_manager_approved ? 'bg-green-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                                <i class="fas fa-hard-hat {{ $project->engineering_manager_approved ? 'text-green-600' : 'text-gray-400' }} text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Engineering Manager</h4>
                                <p class="text-sm text-gray-500">
                                    {{ $project->engineeringManager->full_name ?? 'Not assigned' }}
                                    @if($project->engineering_manager_approved_at)
                                        - Approved {{ $project->engineering_manager_approved_at->format('M j, Y H:i') }}
                                    @endif
                                </p>
                            </div>
                            @if($project->engineering_manager_approved)
                                <span class="text-green-600">
                                    <i class="fas fa-check"></i> Approved
                                </span>
                            @else
                                <span class="text-yellow-600">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @endif
                        </div>

                        <!-- Finance Manager -->
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 {{ $project->finance_manager_approved ? 'bg-green-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                                <i class="fas fa-calculator {{ $project->finance_manager_approved ? 'text-green-600' : 'text-gray-400' }} text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Finance Manager</h4>
                                <p class="text-sm text-gray-500">
                                    {{ $project->financeManager->full_name ?? 'Not assigned' }}
                                    @if($project->finance_manager_approved_at)
                                        - Approved {{ $project->finance_manager_approved_at->format('M j, Y H:i') }}
                                    @endif
                                </p>
                            </div>
                            @if($project->finance_manager_approved)
                                <span class="text-green-600">
                                    <i class="fas fa-check"></i> Approved
                                </span>
                            @else
                                <span class="text-yellow-600">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @endif
                        </div>

                        <!-- Managing Director (if required) -->
                        @if($project->requiresMDApproval())
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 {{ $project->md_approved ? 'bg-green-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                                <i class="fas fa-crown {{ $project->md_approved ? 'text-green-600' : 'text-gray-400' }} text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Managing Director</h4>
                                <p class="text-sm text-gray-500">
                                    {{ $project->managingDirector->full_name ?? 'Not assigned' }}
                                    @if($project->md_approved_at)
                                        - Approved {{ $project->md_approved_at->format('M j, Y H:i') }}
                                    @endif
                                </p>
                                @if($project->requiresMDApproval())
                                    <p class="text-xs text-blue-600">Required: {{ $project->total_budget > 10000 ? 'Amount > $10,000' : 'Complex Service' }}</p>
                                @endif
                            </div>
                            @if($project->md_approved)
                                <span class="text-green-600">
                                    <i class="fas fa-check"></i> Approved
                                </span>
                            @else
                                <span class="text-yellow-600">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quotes Management Section -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Supplier Quotes</h2>
                        <button onclick="openAddQuoteModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Add Quote
                        </button>
                    </div>

                    @if($project->emergency_procurement)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                                <h4 class="font-medium text-red-800">Emergency Procurement</h4>
                            </div>
                            <p class="text-sm text-red-600 mt-1">{{ $project->emergency_justification }}</p>
                            <p class="text-xs text-red-500 mt-2">Only 1 quote required for emergency situations</p>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quote Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quote Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid Until</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($project->quotes as $quote)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $quote->supplier_name }}</div>
                                            @if($quote->is_authorized_distributor)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mt-1">
                                                    <i class="fas fa-shield-alt mr-1"></i> Authorized
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $quote->formatted_amount }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $quote->quote_date->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($quote->valid_until)
                                            {{ $quote->valid_until->format('M j, Y') }}
                                            @if($quote->isExpired())
                                                <span class="text-red-600 ml-1">(Expired)</span>
                                            @endif
                                        @else
                                            No expiry
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($quote->status === 'Selected') bg-green-100 text-green-800
                                            @elseif($quote->status === 'Pending') bg-yellow-100 text-yellow-800
                                            @elseif($quote->status === 'Rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $quote->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($quote->status === 'Pending')
                                            <button class="text-green-600 hover:text-green-900 mr-3">Select</button>
                                            <button class="text-red-600 hover:text-red-900">Reject</button>
                                        @endif
                                        <button class="text-blue-600 hover:text-blue-900 ml-3">View</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No quotes added yet. 
                                        @if(!$project->emergency_procurement)
                                            Minimum {{ $project->getMinimumQuotesRequired() }} quotes required.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Project History/Audit Trail -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Approval History</h2>
                    
                    <div class="space-y-4">
                        @forelse($project->approvals()->orderBy('created_at', 'desc')->get() as $approval)
                        <div class="flex items-start space-x-4 p-4 border border-gray-200 rounded-lg">
                            <div class="w-8 h-8 {{ $approval->isApproved() ? 'bg-green-100' : ($approval->isRejected() ? 'bg-red-100' : 'bg-yellow-100') }} rounded-full flex items-center justify-center">
                                @if($approval->isApproved())
                                    <i class="fas fa-check text-green-600 text-sm"></i>
                                @elseif($approval->isRejected())
                                    <i class="fas fa-times text-red-600 text-sm"></i>
                                @else
                                    <i class="fas fa-clock text-yellow-600 text-sm"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium text-gray-900">{{ $approval->approval_type }}</h4>
                                    <span class="text-sm text-gray-500">{{ $approval->submitted_at->format('M j, Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    {{ $approval->approver_role }}: {{ $approval->approver->full_name }}
                                </p>
                                @if($approval->comments)
                                    <p class="text-sm text-gray-500 mt-1 italic">{{ $approval->comments }}</p>
                                @endif
                                @if($approval->approved_amount)
                                    <p class="text-sm text-green-600 mt-1">Approved Amount: ${{ number_format($approval->approved_amount, 2) }}</p>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                            <p>No approval history yet</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Submit for Approval Modal -->
    <div id="submitApprovalModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Submit for Approval</h3>
                    <button onclick="closeSubmitApprovalModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="submitApprovalForm" method="POST" action="{{ route('projects.submit-approval', $project) }}">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Approval Type *</label>
                            <select name="approval_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="Tender Sign Off">Tender Sign Off</option>
                                <option value="Merchandise Costing">Merchandise Costing</option>
                                <option value="Service Sales Costing">Service Sales Costing</option>
                                <option value="Budget Variance">Budget Variance</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Submit To *</label>
                            <select name="approver_role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                @if(!$project->engineering_manager_approved)
                                    <option value="Engineering Manager">Engineering Manager</option>
                                @endif
                                @if(!$project->finance_manager_approved && $project->engineering_manager_approved)
                                    <option value="Finance Manager">Finance Manager</option>
                                @endif
                                @if(!$project->md_approved && $project->requiresMDApproval() && $project->finance_manager_approved)
                                    <option value="Managing Director">Managing Director</option>
                                @endif
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                            <textarea name="comments" rows="3" placeholder="Additional comments for the approver..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeSubmitApprovalModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-paper-plane mr-1"></i>
                            Submit for Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Quote Modal -->
    <div id="addQuoteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Add Supplier Quote</h3>
                    <button onclick="closeAddQuoteModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="addQuoteForm" method="POST" action="{{ route('quotes.store') }}">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name *</label>
                            <input type="text" name="supplier_name" required 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                   placeholder="Enter supplier name">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quote Amount *</label>
                            <input type="number" name="quote_amount" required min="0" step="0.01" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                   placeholder="0.00">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                                <select name="currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="USD" selected>USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                    <option value="ZWL">ZWL</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quote Date *</label>
                                <input type="date" name="quote_date" required value="{{ date('Y-m-d') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Valid Until</label>
                                <input type="date" name="valid_until" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quote Reference</label>
                                <input type="text" name="quote_reference" placeholder="Quote #"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Contact</label>
                            <input type="text" name="supplier_contact" placeholder="Contact person or email"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Items Description *</label>
                            <textarea name="items_description" rows="3" required placeholder="Describe the quoted items/services..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_authorized_distributor" value="1" 
                                       class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Authorized Distributor</span>
                            </label>
                        </div>
                        
                        @if($project->emergency_procurement)
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_emergency_quote" value="1" 
                                       class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Emergency Quote</span>
                            </label>
                        </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="2" placeholder="Additional notes..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeAddQuoteModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-plus mr-1"></i>
                            Add Quote
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Submit for Approval functionality
        function submitForApproval() {
            document.getElementById('submitApprovalModal').classList.remove('hidden');
        }
        
        function closeSubmitApprovalModal() {
            document.getElementById('submitApprovalModal').classList.add('hidden');
            document.getElementById('submitApprovalForm').reset();
        }
        
        // Add Quote functionality
        function openAddQuoteModal() {
            document.getElementById('addQuoteModal').classList.remove('hidden');
        }
        
        function closeAddQuoteModal() {
            document.getElementById('addQuoteModal').classList.add('hidden');
            document.getElementById('addQuoteForm').reset();
        }

        // Handle form submissions
        document.getElementById('submitApprovalForm').addEventListener('submit', function(e) {
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
                if (data.success) {
                    closeSubmitApprovalModal();
                    location.reload();
                } else {
                    alert('Error submitting for approval. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting for approval. Please try again.');
            });
        });

        document.getElementById('addQuoteForm').addEventListener('submit', function(e) {
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
                    closeAddQuoteModal();
                    location.reload();
                } else {
                    alert('Error adding quote. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding quote. Please try again.');
            });
        });
    </script>
</body>
</html>
