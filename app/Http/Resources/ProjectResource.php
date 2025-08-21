<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_code' => $this->project_code,
            'project_name' => $this->project_name,
            'project_type' => $this->project_type,
            'description' => $this->description,
            'status' => $this->status,
            'location' => $this->location,
            
            // Dates
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Financial data
            'total_budget' => $this->total_budget ? number_format($this->total_budget, 2) : null,
            'actual_cost' => $this->actual_cost ? number_format($this->actual_cost, 2) : null,
            'variance' => $this->variance ? number_format($this->variance, 2) : null,
            'variance_percent' => $this->variance_percent ? round($this->variance_percent, 2) : null,
            
            // Progress and timeline
            'progress_percent' => $this->progress_percent ?? 0,
            'duration' => $this->duration,
            'days_remaining' => $this->days_remaining,
            'is_overdue' => $this->is_overdue,
            
            // Relationships
            'client' => $this->whenLoaded('client', function () {
                return [
                    'id' => $this->client->id,
                    'company_name' => $this->client->company_name,
                    'contact_person' => $this->client->contact_person,
                    'email' => $this->client->email,
                    'phone' => $this->client->phone,
                ];
            }),
            
            'project_manager' => $this->whenLoaded('projectManager', function () {
                return [
                    'id' => $this->projectManager->id,
                    'full_name' => $this->projectManager->full_name,
                    'email' => $this->projectManager->email,
                    'position' => $this->projectManager->position,
                ];
            }),
            
            // Related entities with counts
            'tasks_count' => $this->when(isset($this->tasks_count), $this->tasks_count),
            'risks_count' => $this->when(isset($this->risks_count), $this->risks_count),
            'documents_count' => $this->when(isset($this->documents_count), $this->documents_count),
            'resources_count' => $this->when(isset($this->resources_count), $this->resources_count),
            
            // Detailed relationships (only when explicitly loaded)
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
            'budget_lines' => BudgetLineResource::collection($this->whenLoaded('budgetLines')),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'risks' => RiskResource::collection($this->whenLoaded('risks')),
            'resources' => ResourceResource::collection($this->whenLoaded('resources')),
            
            // Metadata
            'metadata' => $this->metadata,
            
            // Status indicators
            'status_color' => $this->getStatusColor(),
            'priority_level' => $this->getPriorityLevel(),
        ];
    }
    
    /**
     * Get status color for UI display
     */
    private function getStatusColor(): string
    {
        return match($this->status) {
            'Planning' => 'blue',
            'In Progress' => 'green',
            'On Hold' => 'yellow',
            'Completed' => 'gray',
            'Cancelled' => 'red',
            default => 'gray'
        };
    }
    
    /**
     * Get priority level based on various factors
     */
    private function getPriorityLevel(): string
    {
        if ($this->is_overdue) {
            return 'high';
        }
        
        if ($this->status === 'In Progress' && $this->days_remaining <= 7) {
            return 'medium';
        }
        
        return 'normal';
    }
}
