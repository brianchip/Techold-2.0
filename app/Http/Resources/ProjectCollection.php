<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
            'summary' => [
                'total_projects' => $this->total(),
                'active_projects' => $this->collection->where('status', 'In Progress')->count(),
                'completed_projects' => $this->collection->where('status', 'Completed')->count(),
                'overdue_projects' => $this->collection->where('is_overdue', true)->count(),
                'total_budget' => $this->collection->sum('total_budget'),
                'total_actual_cost' => $this->collection->sum('actual_cost'),
                'average_progress' => $this->collection->avg('progress_percent'),
            ]
        ];
    }
}
