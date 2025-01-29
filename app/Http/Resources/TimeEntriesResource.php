<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeEntriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user' => $this->user,
            'client' => new ClientsResource($this->client),
            'start_date' => $this->start_date,
            'project' => new ProjectsResource($this->project),
            'task' => $this->task,
            'planned' => $this->planned == true ? 'Planned' : 'Unplanned',
            'task_summary' => $this->task_summary,
            'duration' => $this->duration,
        ];
    }
}
