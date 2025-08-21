<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'probability' => $this->probability,
            'impact' => $this->impact,
            'risk_score' => $this->risk_score,
            'status' => $this->status,
            'mitigation_plan' => $this->mitigation_plan,
            'owner' => $this->owner,
            'due_date' => $this->due_date?->format('Y-m-d'),
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
