<?php

namespace App\Services;

use App\Models\Vacante;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class VacanteService
{
    /**
     * Obtener todas las vacantes
     */
    public function getAllVacantes()
    {
        try {
            $vacantes = Vacante::all();
            
            Log::info('Vacantes obtenidas exitosamente', [
                'count' => $vacantes->count(),
                'user_id' => auth()->id()
            ]);
            
            return [
                'success' => true,
                'data' => $vacantes,
                'message' => 'Vacantes obtenidas exitosamente'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error al obtener vacantes: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al obtener las vacantes',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Crear una nueva vacante
     */
    public function createVacante($data)
    {
        try {
            // Validar datos
            $validator = Validator::make($data, [
                'area' => 'required|string|max:255',
                'sueldo' => 'required|numeric|min:0',
                'activo' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ];
            }

            // Crear vacante
            $vacante = Vacante::create($data);

            Log::info('Vacante creada exitosamente', [
                'vacante_id' => $vacante->id,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'data' => $vacante,
                'message' => 'Vacante creada exitosamente'
            ];

        } catch (\Exception $e) {
            Log::error('Error al crear vacante: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al crear la vacante',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener una vacante específica
     */
    public function getVacante($id)
    {
        try {
            $vacante = Vacante::findOrFail($id);

            return [
                'success' => true,
                'data' => $vacante,
                'message' => 'Vacante obtenida exitosamente'
            ];

        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Vacante no encontrada'
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener vacante: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al obtener la vacante',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar una vacante
     */
    public function updateVacante($id, $data)
    {
        try {
            $vacante = Vacante::findOrFail($id);

            // Validar datos
            $validator = Validator::make($data, [
                'area' => 'required|string|max:255',
                'sueldo' => 'required|numeric|min:0',
                'activo' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ];
            }

            // Actualizar vacante
            $vacante->update($data);

            Log::info('Vacante actualizada exitosamente', [
                'vacante_id' => $id,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'data' => $vacante,
                'message' => 'Vacante actualizada exitosamente'
            ];

        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Vacante no encontrada'
            ];

        } catch (\Exception $e) {
            Log::error('Error al actualizar vacante: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al actualizar la vacante',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar una vacante
     */
    public function deleteVacante($id)
    {
        try {
            $vacante = Vacante::findOrFail($id);

            // Verificar si tiene entrevistas asociadas
            if ($vacante->entrevistas()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar la vacante porque tiene entrevistas asociadas'
                ];
            }

            $vacante->delete();

            Log::info('Vacante eliminada exitosamente', [
                'vacante_id' => $id,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => 'Vacante eliminada exitosamente'
            ];

        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Vacante no encontrada'
            ];

        } catch (\Exception $e) {
            Log::error('Error al eliminar vacante: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al eliminar la vacante',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener vacantes activas
     */
    public function getVacantesActivas()
    {
        try {
            $vacantes = Vacante::where('activo', true)->get();

            return [
                'success' => true,
                'data' => $vacantes,
                'message' => 'Vacantes activas obtenidas exitosamente'
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener vacantes activas: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al obtener las vacantes activas',
                'error' => $e->getMessage()
            ];
        }
    }
} 