<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from '@inertiajs/vue3';

defineProps({
    errors: Object,
    vacantes: Array,
    prospectos: Array,
    error: String,
});

const form = useForm({
    vacante: '',
    prospecto: '',
    fecha_entrevista: '',
    notas: '',
    reclutado: false,
});

const submit = () => {
    form.post(route('entrevistas.store'));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-md mx-auto mt-10 bg-white shadow rounded p-6">
            <!-- Mensaje de error general -->
            <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ error }}
            </div>

            <!-- Mensaje de error general del formulario -->
            <div v-if="errors.general" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ errors.general }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">

                <!-- Vacante -->
                <div>
                    <label for="vacante" class="block text-gray-700 font-semibold mb-1">Vacante</label>
                    <select
                        id="vacante"
                        v-model="form.vacante"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
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
                    <label for="prospecto" class="block text-gray-700 font-semibold mb-1">Prospecto</label>
                    <select
                        id="prospecto"
                        v-model="form.prospecto"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
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
                    <label for="fecha_entrevista" class="block text-gray-700 font-semibold mb-1">Fecha de Entrevista</label>
                    <input
                        id="fecha_entrevista"
                        v-model="form.fecha_entrevista"
                        type="date"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                    />
                    <div v-if="errors.fecha_entrevista" class="text-red-600 text-sm mt-1">
                        {{ errors.fecha_entrevista }}
                    </div>
                </div>

                <!-- Notas -->
                <div>
                    <label for="notas" class="block text-gray-700 font-semibold mb-1">Notas</label>
                    <textarea
                        id="notas"
                        v-model="form.notas"
                        rows="4"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                    ></textarea>
                    <div v-if="errors.notas" class="text-red-600 text-sm mt-1">
                        {{ errors.notas }}
                    </div>
                </div>

                <!-- Reclutado -->
                <div class="flex items-center space-x-2">
                    <input
                        id="reclutado"
                        v-model="form.reclutado"
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring focus:ring-blue-200 border-gray-300 rounded"
                    />
                    <label for="reclutado" class="text-gray-700">¿Reclutado?</label>
                </div>
                <div v-if="errors.reclutado" class="text-red-600 text-sm">
                    {{ errors.reclutado }}
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
                        :disabled="form.processing"
                    >
                        Añadir
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
