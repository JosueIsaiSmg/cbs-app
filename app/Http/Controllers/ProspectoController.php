<?php

namespace App\Http\Controllers;

use App\Services\ProspectoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

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
    public function index(): Response
    {
        try {
            $result = $this->prospectoService->getAllProspectos();
            
            if (!$result['success']) {
                return Inertia::render('Prospectos/Index', [
                    'prospectos' => [],
                    'error' => $result['message']
                ]);
            }

            return Inertia::render('Prospectos/Index', [
                'prospectos' => $result['data']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - prospectos index: ' . $e->getMessage());
            
            return Inertia::render('Prospectos/Index', [
                'prospectos' => [],
                'error' => 'Error al cargar los prospectos'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Prospectos/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $result = $this->prospectoService->createProspecto($request->all());
            
            if ($result['success']) {
                return redirect()->route('prospectos.index')
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors($result['errors'] ?? ['general' => $result['message']]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - prospectos store: ' . $e->getMessage());
            
            return back()->withErrors(['general' => 'Error al crear el prospecto']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Response
    {
        try {
            $result = $this->prospectoService->getProspecto($id);
            
            if (!$result['success']) {
                return redirect()->route('prospectos.index')
                    ->with('error', $result['message']);
            }

            return Inertia::render('Prospectos/Show', [
                'prospecto' => $result['data']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - prospectos show: ' . $e->getMessage());
            
            return redirect()->route('prospectos.index')
                ->with('error', 'Error al cargar el prospecto');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): Response
    {
        try {
            $result = $this->prospectoService->getProspecto($id);
            
            if (!$result['success']) {
                return redirect()->route('prospectos.index')
                    ->with('error', $result['message']);
            }

            return Inertia::render('Prospectos/Edit', [
                'prospecto' => $result['data']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - prospectos edit: ' . $e->getMessage());
            
            return redirect()->route('prospectos.index')
                ->with('error', 'Error al cargar el formulario de edición');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $result = $this->prospectoService->updateProspecto($id, $request->all());
            
            if ($result['success']) {
                return redirect()->route('prospectos.index')
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors($result['errors'] ?? ['general' => $result['message']]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - prospectos update: ' . $e->getMessage());
            
            return back()->withErrors(['general' => 'Error al actualizar el prospecto']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $result = $this->prospectoService->deleteProspecto($id);
            
            if ($result['success']) {
                return redirect()->route('prospectos.index')
                    ->with('success', $result['message']);
            } else {
                return redirect()->route('prospectos.index')
                    ->with('error', $result['message']);
            }
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - prospectos destroy: ' . $e->getMessage());
            
            return redirect()->route('prospectos.index')
                ->with('error', 'Error al eliminar el prospecto');
        }
    }
}
