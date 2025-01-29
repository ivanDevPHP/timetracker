<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientsResource;
use App\Models\Clients;
use Illuminate\Console\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        // Retrieve all clients from the database
        $clients = Clients::where('active', 1)->get();

        // Return the response as JSON
        return ClientsResource::collection($clients);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return ClientsResource|JsonResponse
     */
    public function store(Request $request): ClientsResource|JsonResponse
    {
        try {
            $this->ensureAdmin();

            // Validate the request data
            $data = $request->validate([
                'name' => 'required|string|max:1000|unique:clients,name',
                'email' => 'required|string|email|unique:clients,email',
            ]);

            // Create the client record
            $client = Clients::create($data);

            // Return the client resource
            return new ClientsResource($client);

        } catch (ValidationException $e) {
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
     * @return ClientsResource|JsonResponse
     */
    public function show(string $id): ClientsResource|JsonResponse
    {
        try {
            $client = Clients::where('id', $id)->where('active', 1)->firstOrFail();

            return new ClientsResource($client);
        }catch (Throwable $e){
            \Log::error('Client retrieval failed:', ['id' => $id, 'message' => $e->getMessage()]);
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
     * @return ClientsResource|JsonResponse
     */
    public function update(Request $request, string $id): ClientsResource|JsonResponse
    {
        try {
            $this->ensureAdmin();
            // Validate the request data
            $data = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:1000',
                    Rule::unique('clients')->ignore($id),  // Ignore the current client's name
                ],
                'email' => [
                    'required',
                    'string',
                    'email',
                    Rule::unique('clients')->ignore($id),  // Ignore the current client's email
                ]
            ]);

            $client = Clients::where('id', $id)->where('active', 1)->firstOrFail();

            $client->update($data);

            return new ClientsResource($client);
        }catch (Throwable $e){
            \Log::error('Client retrieval failed:', ['id' => $id, 'message' => $e->getMessage()]);
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
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function destroy(string $id): Application|ResponseFactory|JsonResponse|Response
    {
        try{
            $this->ensureAdmin();

            $client = Clients::where('id', $id)->where('active', 1)->firstOrFail();

            // Set active to false instead of deleting
            $client->update(['active' => false]);

            return response('', 204);
        }catch (Throwable $e){
            \Log::error('Client retrieval failed:', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
