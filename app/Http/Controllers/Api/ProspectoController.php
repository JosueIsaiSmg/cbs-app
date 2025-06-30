<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProspectoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProspectoController extends Controller
{
    protected $prospectoService;

    public function __construct(ProspectoService $prospectoService)
    {
        $this->prospectoService = $prospectoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $result = $this->prospectoService->getAllProspectos();
        
        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $result = $this->prospectoService->createProspecto($request->all());
        
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
        $result = $this->prospectoService->getProspecto($id);
        
        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $result = $this->prospectoService->updateProspecto($id, $request->all());
        
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
        $result = $this->prospectoService->deleteProspecto($id);
        
        $statusCode = $result['success'] ? 200 : 404;
        return response()->json($result, $statusCode);
    }

    /**
     * Get active prospectos
     */
    public function activos(): JsonResponse
    {
        $result = $this->prospectoService->getProspectosActivos();
        
        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Search prospectos
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Query parameter is required'
            ], 400);
        }

        $result = $this->prospectoService->searchProspectos($query);
        
        return response()->json($result, $result['success'] ? 200 : 500);
    }
}
