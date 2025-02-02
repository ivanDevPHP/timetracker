<?php

use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\TimeEntriesController;
use App\Http\Requests\StoreClientsRequest;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['jwt.auth'])->group(function () {
    Route::prefix('/clients')->group(function () {
        Route::get('/', [ClientsController::class, 'index']);
        Route::post('/', [ClientsController::class, 'store']);
        Route::get('/{client}', [ClientsController::class, 'show']);
        Route::put('/{client}', [ClientsController::class, 'update']);
        Route::delete('/{client}', [ClientsController::class, 'destroy']);
    });
    Route::prefix('/projects')->group(function () {
        Route::get('/', [ProjectsController::class, 'index']);
        Route::post('/', [ProjectsController::class, 'store']);
        Route::get('/{project}', [ProjectsController::class, 'show']);
        Route::put('/{project}', [ProjectsController::class, 'update']);
        Route::delete('/{project}', [ProjectsController::class, 'destroy']);
    });
    Route::prefix('/task')->group(function () {
        Route::get('/', [TimeEntriesController::class, 'index']);
        Route::post('/', [TimeEntriesController::class, 'store']);
        Route::get('/{task}', [TimeEntriesController::class, 'show']);
        Route::put('/{task}', [TimeEntriesController::class, 'update']);
        Route::delete('/{task}', [TimeEntriesController::class, 'destroy']);
    });
});




