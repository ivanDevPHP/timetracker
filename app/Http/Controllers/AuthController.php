<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request): JsonResponse
    {
        try {
            return $this->authService->register($request);
        }catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            return $this->authService->login($request);
        }catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
