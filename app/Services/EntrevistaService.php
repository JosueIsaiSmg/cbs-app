<?php

namespace App\Services;

use App\Models\Entrevista;
use App\Models\Vacante;
use App\Models\Prospecto;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class EntrevistaService
{
    /**
     * Obtener todas las entrevistas con relaciones
     */
    public function getAllEntrevistas()
    {
        try {
            $entrevistas = Entrevista::with(['vacante', 'prospecto'])->get();
            
            Log::info('Entrevistas obtenidas exitosamente', [
                'count' => $entrevistas->count(),
                'user_id' => auth()->id()
            ]);
            
            return [
                'success' => true,
                'data' => $entrevistas,
                'message' => 'Entrevistas obtenidas exitosamente'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error al obtener entrevistas: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al obtener las entrevistas',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Crear una nueva entrevista
     */
    public function createEntrevista($data)
    {
        try {
            // Validar datos
            $validator = Validator::make($data, [
                'vacante' => 'required|exists:vacantes,id',
                'prospecto' => 'required|exists:prospectos,id',
                'fecha_entrevista' => 'required|date',
                'notas' => 'required|string|max:1000',
                'reclutado' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ];
            }

            // Verificar duplicados
            $existingEntrevista = Entrevista::where('vacante', $data['vacante'])
                ->where('prospecto', $data['prospecto'])
                ->first();
                
            if ($existingEntrevista) {
                return [
                    'success' => false,
                    'message' => 'Ya existe una entrevista para esta vacante y prospecto'
                ];
            }

            // Crear entrevista
            $entrevista = Entrevista::create($data);
            $entrevista->load(['vacante', 'prospecto']);

            Log::info('Entrevista creada exitosamente', [
                'entrevista_id' => $entrevista->vacante . '-' . $entrevista->prospecto,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'data' => $entrevista,
                'message' => 'Entrevista creada exitosamente'
            ];

        } catch (\Exception $e) {
            Log::error('Error al crear entrevista: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al crear la entrevista',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener una entrevista específica
     */
    public function getEntrevista($vacante, $prospecto)
    {
        try {
            $entrevista = Entrevista::where('vacante', $vacante)
                                   ->where('prospecto', $prospecto)
                                   ->with(['vacante', 'prospecto'])
                                   ->firstOrFail();

            return [
                'success' => true,
                'data' => $entrevista,
                'message' => 'Entrevista obtenida exitosamente'
            ];

        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Entrevista no encontrada'
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener entrevista: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al obtener la entrevista',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar una entrevista
     */
    public function updateEntrevista($vacante, $prospecto, $data)
    {
        try {
            $entrevista = Entrevista::where('vacante', $vacante)
                                   ->where('prospecto', $prospecto)
                                   ->firstOrFail();

            // Validar datos
            $validator = Validator::make($data, [
                'vacante' => 'required|exists:vacantes,id',
                'prospecto' => 'required|exists:prospectos,id',
                'fecha_entrevista' => 'required|date',
                'notas' => 'required|string|max:1000',
                'reclutado' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ];
            }

            // Verificar duplicados solo si cambió la vacante o prospecto
            if ($data['vacante'] != $vacante || $data['prospecto'] != $prospecto) {
                $existingEntrevista = Entrevista::where('vacante', $data['vacante'])
                    ->where('prospecto', $data['prospecto'])
                    ->first();
                    
                if ($existingEntrevista) {
                    return [
                        'success' => false,
                        'message' => 'Ya existe una entrevista para esta vacante y prospecto'
                    ];
                }
            }

            // Actualizar entrevista
            $entrevista->update($data);
            $entrevista->load(['vacante', 'prospecto']);

            Log::info('Entrevista actualizada exitosamente', [
                'entrevista_id' => $vacante . '-' . $prospecto,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'data' => $entrevista,
                'message' => 'Entrevista actualizada exitosamente'
            ];

        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Entrevista no encontrada'
            ];

        } catch (\Exception $e) {
            Log::error('Error al actualizar entrevista: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al actualizar la entrevista',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar una entrevista
     */
    public function deleteEntrevista($vacante, $prospecto)
    {
        try {
            $entrevista = Entrevista::where('vacante', $vacante)
                                   ->where('prospecto', $prospecto)
                                   ->firstOrFail();

            $entrevista->delete();

            Log::info('Entrevista eliminada exitosamente', [
                'entrevista_id' => $vacante . '-' . $prospecto,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => 'Entrevista eliminada exitosamente'
            ];

        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Entrevista no encontrada'
            ];

        } catch (\Exception $e) {
            Log::error('Error al eliminar entrevista: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al eliminar la entrevista',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener entrevistas por vacante
     */
    public function getEntrevistasByVacante($vacanteId)
    {
        try {
            $entrevistas = Entrevista::where('vacante', $vacanteId)
                                    ->with(['vacante', 'prospecto'])
                                    ->get();

            return [
                'success' => true,
                'data' => $entrevistas,
                'message' => 'Entrevistas por vacante obtenidas exitosamente'
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener entrevistas por vacante: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al obtener las entrevistas',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener entrevistas por prospecto
     */
    public function getEntrevistasByProspecto($prospectoId)
    {
        try {
            $entrevistas = Entrevista::where('prospecto', $prospectoId)
                                    ->with(['vacante', 'prospecto'])
                                    ->get();

            return [
                'success' => true,
                'data' => $entrevistas,
                'message' => 'Entrevistas por prospecto obtenidas exitosamente'
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener entrevistas por prospecto: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al obtener las entrevistas',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener datos para formularios (vacantes y prospectos)
     */
    public function getFormData()
    {
        try {
            $vacantes = Vacante::all();
            $prospectos = Prospecto::all();

            return [
                'success' => true,
                'data' => [
                    'vacantes' => $vacantes,
                    'prospectos' => $prospectos
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener datos de formulario: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al obtener los datos del formulario',
                'error' => $e->getMessage()
            ];
        }
    }
} 