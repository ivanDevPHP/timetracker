<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimeEntriesResource;
use App\Services\TimeEntriesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TimeEntriesController extends Controller
{
    protected TimeEntriesService $timeEntriesService;

    public function __construct(TimeEntriesService $timeEntriesService)
    {
        $this->timeEntriesService = $timeEntriesService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $this->ensureAdmin();

            $data = $request->validate([
                'user_id' => 'nullable|exists:user,id',
                'client_id' => 'nullable|exists:clients,id',
                'start_date' => 'nullable|date',
                'project_id'  => 'nullable|exists:projects,id',
                'task' => 'nullable|string|max:1000',
                'planned' => 'nullable|boolean'
            ]);

            $this->timeEntriesService->getTimeEntries($data);
        }catch (\Throwable $e){
            Log::error('Time Entries retrieval failed:', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return TimeEntriesResource|JsonResponse
     */
    public function store(Request $request): TimeEntriesResource|JsonResponse
    {
        try{
            $this->ensureAdmin();

            $data = $request->validate([
                'user_id' => [
                    'required',
                    'exists:users,id'
                ],
                'client_id' => [
                    'required',
                    'exists:clients,id',
                    function ($attribute, $value, $fail) {
                        $client = \App\Models\Clients::find($value);
                        if (!$client || !$client->active) {
                            $fail('The selected client is not active.');
                        }
                    },
                ],
                'start_date' => 'required|date',
                'project_id' => [
                    'required',
                    'exists:projects,id',
                    function ($attribute, $value, $fail) {
                        $project = \App\Models\Projects::find($value);
                        if (!$project || !$project->active) {
                            $fail('The selected project is not active.');
                        }
                    },
                ],
                'task' => 'required|string|max:1000',
                'planned' => 'required|boolean',
                'task_summary' => 'required|string',
                'duration' => 'required|integer',
            ]);

            return $this->timeEntriesService->storeTimeEntries($data);
        }catch (\Throwable $e){
            Log::error('Time Entries retrieval failed:', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
