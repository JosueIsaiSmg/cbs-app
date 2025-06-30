<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    errors: Object,
});

const form = useForm({
    nombre: '',
    email: '',
    telefono: '',
});

const submit = () => {
    form.post(route('prospecto.store'));
};

</script>
<template>
    <AppLayout>
          <form @submit.prevent="submit">
            <div>
              <label for="nombre">Nombre:</label >
              <input id="nombre" v-model="form.nombre" />
              <div v-if="errors.nombre">El Nombre es Requerido</div>
            </div>
            <div>
              <label for="email">Email:</label>
              <input id="email" v-model="form.email"/>
              <div v-if="errors.email">El Email es Requerido</div>
            </div>
            <div>
              <label for="telefono">Telefono:</label>
              <input id="telefono" v-model="form.telefono"/>
              <div v-if="errors.telefono">El Telefono es Requerido</div>
            </div>
            <button type="submit"> Añadir </button>
          </form>
    </AppLayout>
  </template>