<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'description' => $this->description,
            'budgeted_amount' => $this->budgeted_amount ? number_format($this->budgeted_amount, 2) : null,
            'actual_amount' => $this->actual_amount ? number_format($this->actual_amount, 2) : null,
            'variance' => $this->variance ? number_format($this->variance, 2) : null,
            'variance_percent' => $this->variance_percent ? round($this->variance_percent, 2) : null,
            'currency' => $this->currency ?? 'USD',
            'status' => $this->status,
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
        ];
    }
}
