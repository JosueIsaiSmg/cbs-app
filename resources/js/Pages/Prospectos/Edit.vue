<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    prospecto: Object,
    errors: Object,
});

const form = useForm({
    nombre: props.prospecto.nombre,
    correo: props.prospecto.correo,
    fecha_registro: props.prospecto.fecha_registro,
});

const submit = () => {
    form.put(route('prospectos.update', props.prospecto.id));
};

</script>

<template>
    <AppLayout>
        <div class="max-w-md mx-auto mt-10 bg-white shadow rounded p-6">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label for="nombre" class="block text-gray-700 font-semibold mb-1">Nombre</label>
                    <input
                        id="nombre"
                        v-model="form.nombre"
                        type="text"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                    />
                    <div v-if="errors.nombre" class="text-red-600 text-sm mt-1">
                        {{ errors.nombre }}
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-gray-700 font-semibold mb-1">Email</label>
                    <input
                        id="email"
                        v-model="form.correo"
                        type="email"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                    />
                    <div v-if="errors.correo" class="text-red-600 text-sm mt-1">
                        {{ errors.correo }}
                    </div>
                </div>

                <div>
                    <label for="fecha_registro" class="block text-gray-700 font-semibold mb-1">Fecha de Registro</label>
                    <input
                        id="fecha_registro"
                        v-model="form.fecha_registro"
                        type="date"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                    />
                    <div v-if="errors.fecha_registro" class="text-red-600 text-sm mt-1">
                        {{ errors.fecha_registro }}
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