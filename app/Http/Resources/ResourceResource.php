<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'resource_type' => $this->resource_type,
            'resource_name' => $this->resource_name,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'cost_per_unit' => $this->cost_per_unit ? number_format($this->cost_per_unit, 2) : null,
            'total_cost' => $this->total_cost ? number_format($this->total_cost, 2) : null,
            'allocation_date' => $this->allocation_date?->format('Y-m-d'),
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relationships
            'project' => $this->whenLoaded('project', function () {
                return [
                    'id' => $this->project->id,
                    'project_name' => $this->project->project_name,
                    'project_code' => $this->project->project_code,
                ];
            }),
            
            'task' => $this->whenLoaded('task', function () {
                return [
                    'id' => $this->task->id,
                    'task_name' => $this->task->task_name,
                ];
            }),
            
            'employee' => $this->whenLoaded('employee', function () {
                return [
                    'id' => $this->employee->id,
                    'full_name' => $this->employee->full_name,
                    'email' => $this->employee->email,
                    'position' => $this->employee->position,
                ];
            }),
            
            'equipment' => $this->whenLoaded('equipment', function () {
                return [
                    'id' => $this->equipment->id,
                    'equipment_name' => $this->equipment->equipment_name,
                    'equipment_type' => $this->equipment->equipment_type,
                    'serial_number' => $this->equipment->serial_number,
                ];
            }),
        ];
    }
}
