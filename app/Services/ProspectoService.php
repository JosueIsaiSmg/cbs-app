<?php

namespace App\Services;

use App\Models\Prospecto;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class ProspectoService
{
    /**
     * Obtener todos los prospectos
     */
    public function getAllProspectos()
    {
        try {
            $prospectos = Prospecto::all();
            
            Log::info('Prospectos obtenidos exitosamente', [
                'count' => $prospectos->count(),
                'user_id' => auth()->id()
            ]);
            
            return [
                'success' => true,
                'data' => $prospectos,
                'message' => 'Prospectos obtenidos exitosamente'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error al obtener prospectos: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al obtener los prospectos',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Crear un nuevo prospecto
     */
    public function createProspecto($data)
    {
        try {
            // Validar datos
            $validator = Validator::make($data, [
                'nombre' => 'required|string|max:255',
                'correo' => 'required|email|unique:prospectos,correo',
                'fecha_registro' => 'required|date'
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ];
            }

            // Crear prospecto
            $prospecto = Prospecto::create($data);

            Log::info('Prospecto creado exitosamente', [
                'prospecto_id' => $prospecto->id,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'data' => $prospecto,
                'message' => 'Prospecto creado exitosamente'
            ];

        } catch (\Exception $e) {
            Log::error('Error al crear prospecto: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al crear el prospecto',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener un prospecto específico
     */
    public function getProspecto($id)
    {
        try {
            $prospecto = Prospecto::findOrFail($id);

            return [
                'success' => true,
                'data' => $prospecto,
                'message' => 'Prospecto obtenido exitosamente'
            ];

        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Prospecto no encontrado'
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener prospecto: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al obtener el prospecto',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar un prospecto
     */
    public function updateProspecto($id, $data)
    {
        try {
            $prospecto = Prospecto::findOrFail($id);

            // Validar datos
            $validator = Validator::make($data, [
                'nombre' => 'required|string|max:255',
                'correo' => 'required|email|unique:prospectos,correo,' . $id,
                'fecha_registro' => 'required|date'
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ];
            }

            // Actualizar prospecto
            $prospecto->update($data);

            Log::info('Prospecto actualizado exitosamente', [
                'prospecto_id' => $id,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'data' => $prospecto,
                'message' => 'Prospecto actualizado exitosamente'
            ];

        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Prospecto no encontrado'
            ];

        } catch (\Exception $e) {
            Log::error('Error al actualizar prospecto: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al actualizar el prospecto',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar un prospecto
     */
    public function deleteProspecto($id)
    {
        try {
            $prospecto = Prospecto::findOrFail($id);

            // Verificar si tiene entrevistas asociadas
            if ($prospecto->entrevistas()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar el prospecto porque tiene entrevistas asociadas'
                ];
            }

            $prospecto->delete();

            Log::info('Prospecto eliminado exitosamente', [
                'prospecto_id' => $id,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => 'Prospecto eliminado exitosamente'
            ];

        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Prospecto no encontrado'
            ];

        } catch (\Exception $e) {
            Log::error('Error al eliminar prospecto: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al eliminar el prospecto',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Buscar prospectos por nombre o email
     */
    public function searchProspectos($query)
    {
        try {
            $prospectos = Prospecto::where('nombre', 'like', "%{$query}%")
                                  ->orWhere('correo', 'like', "%{$query}%")
                                  ->get();

            return [
                'success' => true,
                'data' => $prospectos,
                'message' => 'Búsqueda de prospectos completada'
            ];

        } catch (\Exception $e) {
            Log::error('Error al buscar prospectos: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error al buscar prospectos',
                'error' => $e->getMessage()
            ];
        }
    }
} 