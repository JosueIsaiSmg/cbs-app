<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

const page = usePage();

defineProps({
    entrevistas: Array,
    vacantes: Array,
    prospectos: Array,
    error: String,
});

// Mensajes flash
const showSuccessMessage = ref(false);
const showErrorMessage = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

// Función para mostrar mensajes
const showMessage = (type, message) => {
    if (type === 'success') {
        successMessage.value = message;
        showSuccessMessage.value = true;
        setTimeout(() => {
            showSuccessMessage.value = false;
        }, 5000);
    } else if (type === 'error') {
        errorMessage.value = message;
        showErrorMessage.value = true;
        setTimeout(() => {
            showErrorMessage.value = false;
        }, 5000);
    }
};

// Verificar mensajes flash al montar
onMounted(() => {
    // Verificar si existen las propiedades flash de forma segura
    const flash = page.props?.flash;
    
    if (flash) {
        if (flash.success) {
            showMessage('success', flash.success);
        }
        if (flash.error) {
            showMessage('error', flash.error);
        }
    }
});

// Observar cambios en las propiedades flash
watch(() => page.props.flash, (newFlash) => {
    if (newFlash) {
        if (newFlash.success) {
            showMessage('success', newFlash.success);
        }
        if (newFlash.error) {
            showMessage('error', newFlash.error);
        }
    }
}, { deep: true });
</script>

<template>
    <AppLayout>
        <!-- Mensajes Flash -->
        <div v-if="showSuccessMessage" class="fixed top-4 right-4 z-50">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <span class="block sm:inline">{{ successMessage }}</span>
                <button @click="showSuccessMessage = false" class="ml-2 text-green-700 hover:text-green-900">
                    ×
                </button>
            </div>
        </div>

        <div v-if="showErrorMessage" class="fixed top-4 right-4 z-50">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <span class="block sm:inline">{{ errorMessage }}</span>
                <button @click="showErrorMessage = false" class="ml-2 text-red-700 hover:text-red-900">
                    ×
                </button>
            </div>
        </div>

        <!-- Mensaje de error general -->
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ error }}
        </div>

        <div class="flex justify-end p-4">
            <Link
                :href="route('entrevistas.create')"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
                Crear Entrevista
            </Link>
        </div>

        <div class="overflow-x-auto p-4">
            <table class="min-w-full border border-gray-300 divide-y divide-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left px-4 py-2">Vacante</th>
                        <th class="text-left px-4 py-2">Prospecto</th>
                        <th class="text-left px-4 py-2">Fecha Entrevista</th>
                        <th class="text-left px-4 py-2">Notas</th>
                        <th class="text-left px-4 py-2">Reclutado</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-300">
                    <tr v-for="entrevista in entrevistas" :key="`${entrevista.vacante}-${entrevista.prospecto}`" class="hover:bg-gray-50">
                        <td class="text-left px-4 py-2 border-b">
                            {{ entrevista.vacante?.area || 'N/A' }}
                        </td>
                        <td class="text-left px-4 py-2 border-b">
                            {{ entrevista.prospecto?.nombre || 'N/A' }}
                        </td>
                        <td class="text-left px-4 py-2 border-b">
                            {{ entrevista.fecha_entrevista }}
                        </td>
                        <td class="text-left px-4 py-2 border-b">
                            <div class="max-w-xs truncate" :title="entrevista.notas">
                                {{ entrevista.notas }}
                            </div>
                        </td>
                        <td class="text-left px-4 py-2 border-b">
                            <span 
                                :class="entrevista.reclutado 
                                    ? 'bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs' 
                                    : 'bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs'"
                            >
                                {{ entrevista.reclutado ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 border-b">
                            <div class="flex space-x-2">
                                <Link 
                                    :href="route('entrevistas.edit', [entrevista.vacante, entrevista.prospecto])" 
                                    class="text-blue-600 hover:text-blue-800 text-sm"
                                >
                                    Editar
                                </Link>
                                <Link 
                                    :href="route('entrevistas.destroy', [entrevista.vacante, entrevista.prospecto])" 
                                    method="delete" 
                                    class="text-red-600 hover:text-red-800 text-sm"
                                    @click="confirm('¿Estás seguro de que quieres eliminar esta entrevista?')"
                                >
                                    Eliminar
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>