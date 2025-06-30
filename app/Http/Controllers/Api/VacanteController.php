<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VacanteService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VacanteController extends Controller
{
    protected $vacanteService;

    public function __construct(VacanteService $vacanteService)
    {
        $this->vacanteService = $vacanteService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $result = $this->vacanteService->getAllVacantes();
        
        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $result = $this->vacanteService->createVacante($request->all());
        
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
    public function show($id): JsonResponse
    {
        $result = $this->vacanteService->getVacante($id);
        
        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $result = $this->vacanteService->updateVacante($id, $request->all());
        
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
    public function destroy($id): JsonResponse
    {
        $result = $this->vacanteService->deleteVacante($id);
        
        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }

    /**
     * Get active vacantes
     */
    public function activas(): JsonResponse
    {
        $result = $this->vacanteService->getVacantesActivas();
        
        return response()->json($result, $result['success'] ? 200 : 500);
    }
}
