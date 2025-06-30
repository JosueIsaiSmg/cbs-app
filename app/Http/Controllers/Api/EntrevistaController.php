<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EntrevistaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EntrevistaController extends Controller
{
    protected $entrevistaService;

    public function __construct(EntrevistaService $entrevistaService)
    {
        $this->entrevistaService = $entrevistaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $result = $this->entrevistaService->getAllEntrevistas();
        
        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $result = $this->entrevistaService->createEntrevista($request->all());
        
        if ($result['success']) {
            return response()->json($result, 201);
        } else {
            $statusCode = isset($result['errors']) ? 422 : 409;
            return response()->json($result, $statusCode);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($vacante, $prospecto): JsonResponse
    {
        $result = $this->entrevistaService->getEntrevista($vacante, $prospecto);
        
        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $vacante, $prospecto): JsonResponse
    {
        $result = $this->entrevistaService->updateEntrevista($vacante, $prospecto, $request->all());
        
        if ($result['success']) {
            return response()->json($result, 200);
        } else {
            $statusCode = isset($result['errors']) ? 422 : 409;
            return response()->json($result, $statusCode);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($vacante, $prospecto): JsonResponse
    {
        $result = $this->entrevistaService->deleteEntrevista($vacante, $prospecto);
        
        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }

    /**
     * Get entrevistas by vacante
     */
    public function byVacante($vacanteId): JsonResponse
    {
        $result = $this->entrevistaService->getEntrevistasByVacante($vacanteId);
        
        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Get entrevistas by prospecto
     */
    public function byProspecto($prospectoId): JsonResponse
    {
        $result = $this->entrevistaService->getEntrevistasByProspecto($prospectoId);
        
        return response()->json($result, $result['success'] ? 200 : 500);
    }
}
