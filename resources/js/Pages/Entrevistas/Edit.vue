<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    entrevista: Object,
    errors: Object,
    vacantes: Array,
    prospectos: Array,
    error: String,
});

const form = useForm({
    vacante: props.entrevista.vacante,
    prospecto: props.entrevista.prospecto,
    fecha_entrevista: props.entrevista.fecha_entrevista,
    notas: props.entrevista.notas,
    reclutado: props.entrevista.reclutado,
});

const submit = () => {
    form.put(route('entrevistas.update', [props.entrevista.vacante, props.entrevista.prospecto]));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto mt-10 bg-white shadow-lg rounded-lg p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Editar Entrevista</h1>
                <p class="text-gray-600 mt-1">Modifica los datos de la entrevista</p>
            </div>

            <!-- Mensaje de error general -->
            <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ error }}
            </div>

            <!-- Mensaje de error general del formulario -->
            <div v-if="errors.general" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ errors.general }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Vacante -->
                <div>
                    <label for="vacante" class="block text-sm font-medium text-gray-700 mb-2">
                        Vacante
                    </label>
                    <select
                        id="vacante"
                        v-model="form.vacante"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        :class="{ 'border-red-500': errors.vacante }"
                    >
                        <option value="">Selecciona una vacante</option>
                        <option
                            v-for="vacante in vacantes"
                            :key="vacante.id"
                            :value="vacante.id"
                        >
                            {{ vacante.area }}
                        </option>
                    </select>
                    <div v-if="errors.vacante" class="text-red-600 text-sm mt-1">
                        {{ errors.vacante }}
                    </div>
                </div>

                <!-- Prospecto -->
                <div>
                    <label for="prospecto" class="block text-sm font-medium text-gray-700 mb-2">
                        Prospecto
                    </label>
                    <select
                        id="prospecto"
                        v-model="form.prospecto"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        :class="{ 'border-red-500': errors.prospecto }"
                    >
                        <option value="">Selecciona un prospecto</option>
                        <option
                            v-for="prospecto in prospectos"
                            :key="prospecto.id"
                            :value="prospecto.id"
                        >
                            {{ prospecto.nombre }}
                        </option>
                    </select>
                    <div v-if="errors.prospecto" class="text-red-600 text-sm mt-1">
                        {{ errors.prospecto }}
                    </div>
                </div>

                <!-- Fecha entrevista -->
                <div>
                    <label for="fecha_entrevista" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Entrevista
                    </label>
                    <input
                        id="fecha_entrevista"
                        v-model="form.fecha_entrevista"
                        type="date"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        :class="{ 'border-red-500': errors.fecha_entrevista }"
                    />
                    <div v-if="errors.fecha_entrevista" class="text-red-600 text-sm mt-1">
                        {{ errors.fecha_entrevista }}
                    </div>
                </div>

                <!-- Notas -->
                <div>
                    <label for="notas" class="block text-sm font-medium text-gray-700 mb-2">
                        Notas
                    </label>
                    <textarea
                        id="notas"
                        v-model="form.notas"
                        rows="4"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"
                        :class="{ 'border-red-500': errors.notas }"
                        placeholder="Ingresa las notas de la entrevista..."
                    ></textarea>
                    <div v-if="errors.notas" class="text-red-600 text-sm mt-1">
                        {{ errors.notas }}
                    </div>
                </div>

                <!-- Reclutado -->
                <div class="flex items-center space-x-3">
                    <input
                        id="reclutado"
                        v-model="form.reclutado"
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="reclutado" class="text-sm font-medium text-gray-700">
                        ¿Reclutado?
                    </label>
                </div>
                <div v-if="errors.reclutado" class="text-red-600 text-sm">
                    {{ errors.reclutado }}
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a
                        :href="route('entrevistas.index')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                    >
                        Cancelar
                    </a>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Actualizando...</span>
                        <span v-else>Actualizar Entrevista</span>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>