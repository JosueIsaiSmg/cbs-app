<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    vacantes: Array,
});
</script>

<template>
    <AppLayout>
        <div class="flex justify-end p-4">
            <Link
                :href="route('vacantes.create')"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
                Crear Vacante
            </Link>
        </div>

        <div class="overflow-x-auto p-4">
            <table class="min-w-full border border-gray-300 divide-y divide-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left px-4 py-2">Area</th>
                        <th class="text-left px-4 py-2">Sueldo</th>
                        <th class="text-left px-4 py-2">Activo</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="vacante in vacantes"
                        :key="vacante.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="px-4 py-2">{{ vacante.area }}</td>
                        <td class="px-4 py-2">{{ vacante.sueldo }}</td>
                        <td class="text-left px-4 py-2 border-b">
                            <span 
                                :class="vacante.activo 
                                    ? 'bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs' 
                                    : 'bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs'"
                            >
                                {{ vacante.activo ? 'Sí' : 'No' }}
                            </span>
                        </td>                        
                        <td class="px-4 py-2 space-x-2">
                            <Link
                                :href="route('vacantes.edit', vacante.id)"
                                class="text-blue-600 hover:underline"
                            >
                                Editar
                            </Link>
                            <Link
                                :href="route('vacantes.destroy', vacante.id)"
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