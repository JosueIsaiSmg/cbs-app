<?php

namespace App\Http\Controllers;

use App\Models\Entrevista;
use App\Models\Vacante;
use App\Models\Prospecto;
use App\Services\EntrevistaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

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
    public function index(): Response
    {
        try {
            $result = $this->entrevistaService->getAllEntrevistas();
            
            if (!$result['success']) {
                return Inertia::render('Entrevistas/Index', [
                    'entrevistas' => [],
                    'error' => $result['message']
                ]);
            }

            $formData = $this->entrevistaService->getFormData();
            
            return Inertia::render('Entrevistas/Index', [
                'entrevistas' => $result['data'],
                'vacantes' => $formData['success'] ? $formData['data']['vacantes'] : [],
                'prospectos' => $formData['success'] ? $formData['data']['prospectos'] : [],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - index: ' . $e->getMessage());
            
            return Inertia::render('Entrevistas/Index', [
                'entrevistas' => [],
                'vacantes' => [],
                'prospectos' => [],
                'error' => 'Error al cargar las entrevistas'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        try {
            $formData = $this->entrevistaService->getFormData();
            
            if (!$formData['success']) {
                return Inertia::render('Entrevistas/Create', [
                    'vacantes' => [],
                    'prospectos' => [],
                    'error' => $formData['message']
                ]);
            }

            return Inertia::render('Entrevistas/Create', [
                'vacantes' => $formData['data']['vacantes'],
                'prospectos' => $formData['data']['prospectos'],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - create: ' . $e->getMessage());
            
            return Inertia::render('Entrevistas/Create', [
                'vacantes' => [],
                'prospectos' => [],
                'error' => 'Error al cargar el formulario'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $result = $this->entrevistaService->createEntrevista($request->all());
            
            if ($result['success']) {
                return redirect()->route('entrevistas.index')
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors($result['errors'] ?? ['general' => $result['message']]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - store: ' . $e->getMessage());
            
            return back()->withErrors(['general' => 'Error al crear la entrevista']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Entrevista $entrevista): Response
    {
        try {
            $result = $this->entrevistaService->getEntrevista($entrevista->vacante, $entrevista->prospecto);
            
            if (!$result['success']) {
                return redirect()->route('entrevistas.index')
                    ->with('error', $result['message']);
            }

            return Inertia::render('Entrevistas/Show', [
                'entrevista' => $result['data']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - show: ' . $e->getMessage());
            
            return redirect()->route('entrevistas.index')
                ->with('error', 'Error al cargar la entrevista');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($vacante, $prospecto): Response
    {
        try {
            $result = $this->entrevistaService->getEntrevista($vacante, $prospecto);
            
            if (!$result['success']) {
                return redirect()->route('entrevistas.index')
                    ->with('error', $result['message']);
            }

            $formData = $this->entrevistaService->getFormData();
            
            return Inertia::render('Entrevistas/Edit', [
                'entrevista' => $result['data'],
                'vacantes' => $formData['success'] ? $formData['data']['vacantes'] : [],
                'prospectos' => $formData['success'] ? $formData['data']['prospectos'] : [],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - edit: ' . $e->getMessage());
            
            return redirect()->route('entrevistas.index')
                ->with('error', 'Error al cargar el formulario de edición');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $vacante, $prospecto): RedirectResponse
    {
        try {
            $result = $this->entrevistaService->updateEntrevista($vacante, $prospecto, $request->all());
            
            if ($result['success']) {
                return redirect()->route('entrevistas.index')
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors($result['errors'] ?? ['general' => $result['message']]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - update: ' . $e->getMessage());
            
            return back()->withErrors(['general' => 'Error al actualizar la entrevista']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($vacante, $prospecto): RedirectResponse
    {
        try {
            $result = $this->entrevistaService->deleteEntrevista($vacante, $prospecto);
            
            if ($result['success']) {
                return redirect()->route('entrevistas.index')
                    ->with('success', $result['message']);
            } else {
                return redirect()->route('entrevistas.index')
                    ->with('error', $result['message']);
            }
            
        } catch (\Exception $e) {
            Log::error('Error en controlador web - destroy: ' . $e->getMessage());
            
            return redirect()->route('entrevistas.index')
                ->with('error', 'Error al eliminar la entrevista');
        }
    }
}
