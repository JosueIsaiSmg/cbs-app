<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    prospectos: Array,
});
</script>

<template>
    <AppLayout>
        <div class="flex justify-end p-4">
            <Link
                :href="route('prospectos.create')"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
                Crear Prospecto
            </Link>
        </div>

        <div class="overflow-x-auto p-4">
            <table class="min-w-full border border-gray-300 divide-y divide-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left px-4 py-2">Nombre</th>
                        <th class="text-left px-4 py-2">Email</th>
                        <th class="text-left px-4 py-2">Fecha Registro</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="prospecto in prospectos"
                        :key="prospecto.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="px-4 py-2">{{ prospecto.nombre }}</td>
                        <td class="px-4 py-2">{{ prospecto.correo }}</td>
                        <td class="px-4 py-2">{{ prospecto.fecha_registro }}</td>
                        <td class="px-4 py-2 space-x-2">
                            <Link
                                :href="route('prospectos.edit', prospecto.id)"
                                class="text-blue-600 hover:underline"
                            >
                                Editar
                            </Link>
                            <Link
                                :href="route('prospectos.destroy', prospecto.id)"
                                method="delete"
                                as="button"
                                class="text-red-600 hover:underline"
                            >
                                Borrar
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
