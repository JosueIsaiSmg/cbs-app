<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    vacante: Object,
    errors: Object,
});

const form = useForm({
    area: props.vacante.area,
    sueldo: props.vacante.sueldo,
    activo: props.vacante.activo,
});

const submit = () => {
    form.put(route('vacantes.update', props.vacante.id));
};

</script>

<template>
    <AppLayout>
        <div class="max-w-md mx-auto mt-10 bg-white shadow rounded p-6">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label for="area" class="block text-gray-700 font-semibold mb-1">Area</label>
                    <input
                        id="area"
                        v-model="form.area"
                        type="text"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                    />
                    <div v-if="errors.area" class="text-red-600 text-sm mt-1">
                        {{ errors.area }}
                    </div>
                </div>

                <div>
                    <label for="sueldo" class="block text-gray-700 font-semibold mb-1">Sueldo</label>
                    <input
                        id="sueldo"
                        v-model="form.sueldo"
                        type="number"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                    />
                    <div v-if="errors.sueldo" class="text-red-600 text-sm mt-1">
                        {{ errors.sueldo }}
                    </div>
                </div>

                <div>
                    <label for="activo" class="block text-gray-700 font-semibold mb-1">Activo</label>
                    <input
                        id="activo"
                        v-model="form.activo"
                        type="checkbox"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                    />
                    <div v-if="errors.activo" class="text-red-600 text-sm mt-1">
                        {{ errors.activo }}
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
                        :disabled="form.processing"
                    >
                        Edit
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
  </template>