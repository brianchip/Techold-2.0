<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Implement proper authorization
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'project_name' => 'required|string|max:255',
            'project_type' => 'required|string|in:Engineering,Procurement,Installation,EPC',
            'costing_type' => 'required|string|in:Tender/Proposal,Merchandise,Service Sales',
            'client_id' => 'required|exists:clients,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:Planned,In Progress,On Hold,Completed,Cancelled',
            'project_manager_id' => 'required|exists:employees,id',
            'prime_mover_id' => 'nullable|exists:employees,id',
            'description' => 'nullable|string',
            'total_budget' => 'nullable|numeric|min:0',
            'procurement_budget' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
            'emergency_procurement' => 'nullable|boolean',
            'emergency_justification' => 'nullable|string|required_if:emergency_procurement,true',
            
            // Optional nested data
            'tasks' => 'nullable|array',
            'tasks.*.task_name' => 'required_with:tasks|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.start_date' => 'nullable|date',
            'tasks.*.end_date' => 'nullable|date|after_or_equal:tasks.*.start_date',
            'tasks.*.assigned_to' => 'nullable|exists:employees,id',
            'tasks.*.status' => 'nullable|string|in:Not Started,In Progress,Completed,On Hold,Cancelled',
            'tasks.*.priority' => 'nullable|string|in:Low,Medium,High,Critical',
            
            'budget_lines' => 'nullable|array',
            'budget_lines.*.category' => 'required_with:budget_lines|string|max:255',
            'budget_lines.*.description' => 'nullable|string',
            'budget_lines.*.budgeted_amount' => 'required_with:budget_lines|numeric|min:0',
            'budget_lines.*.currency' => 'nullable|string|size:3',
        ];

        // For updates, project_code should not be editable
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['project_code'] = 'nullable|string'; // Allow but don't validate uniqueness on update
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'project_name' => 'project name',
            'project_type' => 'project type',
            'client_id' => 'client',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'project_manager_id' => 'project manager',
            'total_budget' => 'total budget',
            'tasks.*.task_name' => 'task name',
            'tasks.*.assigned_to' => 'assigned employee',
            'budget_lines.*.category' => 'budget category',
            'budget_lines.*.budgeted_amount' => 'budgeted amount',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'end_date.after_or_equal' => 'The end date must be equal to or after the start date.',
            'tasks.*.end_date.after_or_equal' => 'Each task end date must be equal to or after its start date.',
            'client_id.exists' => 'The selected client does not exist.',
            'project_manager_id.exists' => 'The selected project manager does not exist.',
            'tasks.*.assigned_to.exists' => 'The selected employee for task assignment does not exist.',
        ];
    }
}
