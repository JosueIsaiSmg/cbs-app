<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    errors: Object,
});

const form = useForm({
    vacante: '',
    prospecto: '',
    fecha_entrevista: '',
    notas: '',
    reclutado: '',
});

const submit = () => {
    form.post(route('entrevista.store'));
};

</script>
<template>
    <AppLayout>
          <form @submit.prevent="submit">
            <div>
              <label for="vacante">Vacante:</label >
              <input id="vacante" v-model="form.vacante" />
              <div v-if="errors.vacante">El Vacante es Requerido</div>
            </div>
            <div>
              <label for="prospecto">Prospecto:</label>
              <input id="prospecto" v-model="form.prospecto"/>
              <div v-if="errors.prospecto">El Prospecto es Requerido</div>
            </div>
            <div>
              <label for="fecha_entrevista">Fecha Entrevista:</label>
              <input id="fecha_entrevista" v-model="form.fecha_entrevista"/>
              <div v-if="errors.fecha_entrevista">La Fecha Entrevista es Requerida</div>
            </div>
            
            <div>
              <label for="notas">Notas:</label>
              <input id="notas" v-model="form.notas"/>
              <div v-if="errors.notas">Las Notas son Requeridas</div>
            </div>
            
            <button type="submit"> Añadir </button>
          </form>
    </AppLayout>
  </template>