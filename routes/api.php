<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EntrevistaController;
use App\Http\Controllers\Api\VacanteController;
use App\Http\Controllers\Api\ProspectoController;
use App\Http\Controllers\Api\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas públicas de autenticación
Route::post('/login', [AuthController::class, 'login']);

// Todas las rutas de la API requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    
    // Rutas API para Entrevistas
    Route::prefix('entrevistas')->group(function () {
        Route::get('/', [EntrevistaController::class, 'index']);
        Route::post('/', [EntrevistaController::class, 'store']);
        Route::get('/{vacante}/{prospecto}', [EntrevistaController::class, 'show']);
        Route::put('/{vacante}/{prospecto}', [EntrevistaController::class, 'update']);
        Route::delete('/{vacante}/{prospecto}', [EntrevistaController::class, 'destroy']);
        
        // Rutas adicionales
        Route::get('/vacante/{vacanteId}', [EntrevistaController::class, 'byVacante']);
        Route::get('/prospecto/{prospectoId}', [EntrevistaController::class, 'byProspecto']);
    });

    // Rutas API para Vacantes
    Route::prefix('vacantes')->group(function () {
        Route::get('/', [VacanteController::class, 'index']);
        Route::post('/', [VacanteController::class, 'store']);
        Route::get('/activas', [VacanteController::class, 'activas']);
        Route::get('/{id}', [VacanteController::class, 'show']);
        Route::put('/{id}', [VacanteController::class, 'update']);
        Route::delete('/{id}', [VacanteController::class, 'destroy']);
    });

    // Rutas API para Prospectos
    Route::prefix('prospectos')->group(function () {
        Route::get('/', [ProspectoController::class, 'index']);
        Route::post('/', [ProspectoController::class, 'store']);
        Route::get('/activos', [ProspectoController::class, 'activos']);
        Route::get('/search', [ProspectoController::class, 'search']);
        Route::get('/{id}', [ProspectoController::class, 'show']);
        Route::put('/{id}', [ProspectoController::class, 'update']);
        Route::delete('/{id}', [ProspectoController::class, 'destroy']);
    });

    // Rutas protegidas
    Route::post('/logout', [AuthController::class, 'logout']);
});
