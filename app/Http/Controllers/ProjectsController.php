<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectsResource;
use App\Models\Projects;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Validate the request data, client_id is optional but must exist if provided
        $data = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
        ]);

        // Build query
        $query = Projects::where('active', 1);

        // Apply client_id filter only if it's provided
        if (isset($data['client_id'])) {
            $query->where('client_id', $data['client_id']);
        }

        // Get projects
        $projects = $query->get();

        return ProjectsResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try{
            $this->ensureAdmin();

            // Validate the request data
            $data = $request->validate([
                'name' => 'required|string|max:1000|unique:projects,name',
                'client_id' => 'required|exists:clients,id'
            ]);

            $project = Projects::create($data);

            // Return the project resource
            return new ProjectsResource($project);
        }catch (ValidationException $e) {
            // Log the exception for debugging purposes (optional)
            \Log::error('Validation failed:', $e->errors());

            // Return the validation errors in a structured JSON format
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return ProjectsResource|JsonResponse
     */
    public function show(string $id): ProjectsResource|JsonResponse
    {
        try {
            $client = Projects::where('id', $id)->where('active', 1)->firstOrFail();

            return new ProjectsResource($client);
        }catch (Throwable $e){
            \Log::error('Project retrieval failed:', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $id
     * @return ProjectsResource|JsonResponse
     */
    public function update(Request $request, string $id): ProjectsResource|JsonResponse
    {
        try{
            $this->ensureAdmin();

            $data = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:1000',
                    Rule::unique('clients')->ignore($id),
                ],
                'client_id' => ['prohibited'], // ❌ Prevents updating client_id
            ]);

            $project = Projects::where('id', $id)->where('active', 1)->firstOrFail();

            $project->update($data);

            return new ProjectsResource($project);
        }catch (Throwable $e){
            \Log::error('Project retrieval failed:', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }catch (ValidationException $e) {
            // Log the exception for debugging purposes (optional)
            \Log::error('Validation failed:', $e->errors());

            // Return the validation errors in a structured JSON format
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try{
            $this->ensureAdmin();

            $project = Projects::where('id', $id)->where('active', 1)->firstOrFail();

            $project->update(['active' => false]);

            return response('', 204);
        }catch (Throwable $e){
            \Log::error('Project retrieval failed:', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
