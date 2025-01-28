<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientsRequest;
use App\Http\Resources\ClientsResource;
use App\Models\Clients;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve all clients from the database
        $clients = Clients::where('active', 1)->get();

        // Return the response as JSON
        return ClientsResource::collection($clients);
    }

    /**
     * Store a newly created resource in storage.
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
     */
    public function show(string $id)
    {
        try {
            $client = Clients::where('id', $id)->where('active', 1)->firstOrFail();

            return new ClientsResource($client);
        }catch (Throwable $e){
            \Log::error('Client retrieval failed:', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
            ], $e->getStatusCode());
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
     */
    public function destroy(string $id)
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
            ], $e->getStatusCode());
        }
    }
}
