<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'task_name' => $this->task_name,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'progress_percent' => $this->progress_percent ?? 0,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'estimated_hours' => $this->estimated_hours,
            'actual_hours' => $this->actual_hours,
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
            
            'assigned_employee' => $this->whenLoaded('assignedEmployee', function () {
                return [
                    'id' => $this->assignedEmployee->id,
                    'full_name' => $this->assignedEmployee->full_name,
                    'email' => $this->assignedEmployee->email,
                ];
            }),
            
            'parent_task' => $this->whenLoaded('parentTask', function () {
                return [
                    'id' => $this->parentTask->id,
                    'task_name' => $this->parentTask->task_name,
                ];
            }),
            
            'subtasks' => TaskResource::collection($this->whenLoaded('subtasks')),
            'resources' => ResourceResource::collection($this->whenLoaded('resources')),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
        ];
    }
}
