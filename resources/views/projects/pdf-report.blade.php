<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Report - {{ $project->project_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
            margin-bottom: 10px;
        }
        h1 {
            color: #10b981;
            margin-bottom: 5px;
        }
        h2 {
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-top: 30px;
        }
        .project-info {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #374151;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status.in-progress { background-color: #dcfce7; color: #166534; }
        .status.planned { background-color: #dbeafe; color: #1e40af; }
        .status.completed { background-color: #f3f4f6; color: #374151; }
        .status.on-hold { background-color: #fef3c7; color: #92400e; }
        .status.cancelled { background-color: #fee2e2; color: #991b1b; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f9fafb;
            font-weight: bold;
        }
        .financial-summary {
            background-color: #f0f9ff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">TECHOLD Engineering</div>
        <h1>Project Report</h1>
        <p>Generated on {{ now()->format('F j, Y') }}</p>
    </div>

    <!-- Project Overview -->
    <h2>Project Overview</h2>
    <div class="project-info">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Project Name:</div>
                {{ $project->project_name }}
            </div>
            <div class="info-item">
                <div class="info-label">Project Code:</div>
                {{ $project->project_code }}
            </div>
            <div class="info-item">
                <div class="info-label">Project Type:</div>
                {{ $project->project_type }}
            </div>
            <div class="info-item">
                <div class="info-label">Costing Type:</div>
                {{ $project->costing_type }}
            </div>
            <div class="info-item">
                <div class="info-label">Client:</div>
                {{ $project->client->company_name ?? 'N/A' }}
            </div>
            <div class="info-item">
                <div class="info-label">Status:</div>
                <span class="status {{ strtolower(str_replace(' ', '-', $project->status)) }}">
                    {{ $project->status }}
                </span>
            </div>
            <div class="info-item">
                <div class="info-label">Project Manager:</div>
                {{ $project->projectManager->full_name ?? 'N/A' }}
            </div>
            <div class="info-item">
                <div class="info-label">Prime Mover:</div>
                {{ $project->primeMover->full_name ?? 'N/A' }}
            </div>
            <div class="info-item">
                <div class="info-label">Start Date:</div>
                {{ $project->start_date ? $project->start_date->format('M j, Y') : 'N/A' }}
            </div>
            <div class="info-item">
                <div class="info-label">End Date:</div>
                {{ $project->end_date ? $project->end_date->format('M j, Y') : 'N/A' }}
            </div>
            <div class="info-item">
                <div class="info-label">Location:</div>
                {{ $project->location ?? 'N/A' }}
            </div>
            <div class="info-item">
                <div class="info-label">Progress:</div>
                {{ $project->progress_percent }}%
            </div>
        </div>
        @if($project->description)
        <div style="margin-top: 15px;">
            <div class="info-label">Description:</div>
            {{ $project->description }}
        </div>
        @endif
    </div>

    <!-- Financial Summary -->
    <h2>Financial Summary</h2>
    <div class="financial-summary">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Total Budget:</div>
                ${{ number_format($project->total_budget, 2) }}
            </div>
            <div class="info-item">
                <div class="info-label">Actual Cost:</div>
                ${{ number_format($project->actual_cost, 2) }}
            </div>
            <div class="info-item">
                <div class="info-label">Procurement Budget:</div>
                ${{ number_format($project->procurement_budget, 2) }}
            </div>
            <div class="info-item">
                <div class="info-label">Procurement Actual:</div>
                ${{ number_format($project->actual_procurement_cost, 2) }}
            </div>
            <div class="info-item">
                <div class="info-label">Budget Variance:</div>
                <span style="color: {{ $project->budget_variance >= 0 ? '#dc2626' : '#16a34a' }}">
                    ${{ number_format(abs($project->budget_variance), 2) }} 
                    ({{ $project->budget_variance >= 0 ? 'Over' : 'Under' }} Budget)
                </span>
            </div>
            <div class="info-item">
                <div class="info-label">Variance Percentage:</div>
                <span style="color: {{ $project->budget_variance >= 0 ? '#dc2626' : '#16a34a' }}">
                    {{ number_format(abs($project->budget_variance_percent), 1) }}%
                </span>
            </div>
        </div>
    </div>

    <!-- Approval Status -->
    <h2>Approval Status</h2>
    <table>
        <thead>
            <tr>
                <th>Approver Role</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Engineering Manager</td>
                <td>{{ $project->engineering_manager_approved ? 'Approved' : 'Pending' }}</td>
                <td>{{ $project->engineering_manager_approved_at ? $project->engineering_manager_approved_at->format('M j, Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Finance Manager</td>
                <td>{{ $project->finance_manager_approved ? 'Approved' : 'Pending' }}</td>
                <td>{{ $project->finance_manager_approved_at ? $project->finance_manager_approved_at->format('M j, Y') : 'N/A' }}</td>
            </tr>
            @if($project->requiresMDApproval())
            <tr>
                <td>Managing Director</td>
                <td>{{ $project->md_approved ? 'Approved' : 'Pending' }}</td>
                <td>{{ $project->md_approved_at ? $project->md_approved_at->format('M j, Y') : 'N/A' }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Tasks Summary -->
    @if($project->tasks->count() > 0)
    <div class="page-break"></div>
    <h2>Tasks Summary</h2>
    <table>
        <thead>
            <tr>
                <th>Task Name</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Progress</th>
                <th>Assignee</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->tasks as $task)
            <tr>
                <td>{{ $task->task_name }}</td>
                <td>{{ $task->status }}</td>
                <td>{{ $task->priority }}</td>
                <td>{{ $task->progress_percent }}%</td>
                <td>{{ $task->assignee_name ?? 'Unassigned' }}</td>
                <td>{{ $task->end_date ? $task->end_date->format('M j, Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Budget Lines -->
    @if($project->budgetLines->count() > 0)
    <h2>Budget Breakdown</h2>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Description</th>
                <th>Planned Amount</th>
                <th>Actual Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->budgetLines as $budgetLine)
            <tr>
                <td>{{ $budgetLine->category }}</td>
                <td>{{ $budgetLine->description ?? 'N/A' }}</td>
                <td>${{ number_format($budgetLine->planned_amount, 2) }}</td>
                <td>${{ number_format($budgetLine->actual_amount ?? 0, 2) }}</td>
                <td>{{ $budgetLine->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Risks Summary -->
    @if($project->risks->count() > 0)
    <h2>Risk Management</h2>
    <table>
        <thead>
            <tr>
                <th>Risk Title</th>
                <th>Severity</th>
                <th>Status</th>
                <th>Mitigation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->risks as $risk)
            <tr>
                <td>{{ $risk->risk_title }}</td>
                <td>{{ $risk->severity }}</td>
                <td>{{ $risk->status }}</td>
                <td>{{ $risk->mitigation_plan ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated by TECHOLD Engineering Project Management System</p>
        <p>Report generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>Project Code: {{ $project->project_code }} | Page 1 of 1</p>
    </div>
</body>
</html>
