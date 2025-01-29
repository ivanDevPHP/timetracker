<?php

namespace App\Services;

use App\Http\Resources\TimeEntriesResource;
use App\Models\TimeEntries;

class TimeEntriesService
{
    public function getTimeEntries(array $data){
        $task = TimeEntries::all();

        return $task;
    }

    /**
     * @param array $data
     * @return TimeEntriesResource
     */
    public function storeTimeEntries(array $data): TimeEntriesResource
    {
        $task = TimeEntries::create($data);

        return new TimeEntriesResource($task);
    }
}
