<?php

namespace App\Http\Controllers;

use App\Services\VacanteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

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
    public function index(): Response
    {
        try {
            $result = $this->vacanteService->getAllVacantes();
            
            if (!$result['success']) {
                return Inertia::render('Vacantes/Index', [
                    'vacantes' => [],
                    'error' => $result['message']
                ]);
            }

            return Inertia::render('Vacantes/Index', [
                'vacantes' => $result['data']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - vacantes index: ' . $e->getMessage());
            
            return Inertia::render('Vacantes/Index', [
                'vacantes' => [],
                'error' => 'Error al cargar las vacantes'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Vacantes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $result = $this->vacanteService->createVacante($request->all());
            
            if ($result['success']) {
                return redirect()->route('vacantes.index')
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors($result['errors'] ?? ['general' => $result['message']]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - vacantes store: ' . $e->getMessage());
            
            return back()->withErrors(['general' => 'Error al crear la vacante']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Response
    {
        try {
            $result = $this->vacanteService->getVacante($id);
            
            if (!$result['success']) {
                return redirect()->route('vacantes.index')
                    ->with('error', $result['message']);
            }

            return Inertia::render('Vacantes/Show', [
                'vacante' => $result['data']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - vacantes show: ' . $e->getMessage());
            
            return redirect()->route('vacantes.index')
                ->with('error', 'Error al cargar la vacante');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): Response
    {
        try {
            $result = $this->vacanteService->getVacante($id);
            
            if (!$result['success']) {
                return redirect()->route('vacantes.index')
                    ->with('error', $result['message']);
            }

            return Inertia::render('Vacantes/Edit', [
                'vacante' => $result['data']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - vacantes edit: ' . $e->getMessage());
            
            return redirect()->route('vacantes.index')
                ->with('error', 'Error al cargar el formulario de edición');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $result = $this->vacanteService->updateVacante($id, $request->all());
            
            if ($result['success']) {
                return redirect()->route('vacantes.index')
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors($result['errors'] ?? ['general' => $result['message']]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - vacantes update: ' . $e->getMessage());
            
            return back()->withErrors(['general' => 'Error al actualizar la vacante']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $result = $this->vacanteService->deleteVacante($id);
            
            if ($result['success']) {
                return redirect()->route('vacantes.index')
                    ->with('success', $result['message']);
            } else {
                return redirect()->route('vacantes.index')
                    ->with('error', $result['message']);
            }
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - vacantes destroy: ' . $e->getMessage());
            
            return redirect()->route('vacantes.index')
                ->with('error', 'Error al eliminar la vacante');
        }
    }
}
