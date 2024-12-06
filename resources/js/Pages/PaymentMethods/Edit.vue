<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen shadow-md">
            <h1 class="text-3xl font-bold mb-8">Editar Método de Pago</h1>
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700">Nombre del Método</label>
                        <input v-model="method.name" id="name" type="text" placeholder="Nombre del Método"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700">Descripción</label>
                        <textarea v-model="method.description" id="description" rows="4"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                  placeholder="Descripción del método de pago"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <button type="submit" class="bg-blue-900 p-2 rounded-full" title="Guardar Cambios">
                        <SaveIcon class="w-6 h-6 stroke-gray-50"/>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import {Inertia} from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from "@/Components/Icons/SaveIcon.vue";

// Obtener el método existente desde los props
const props = defineProps({
    method: Object,
});
const method = ref({
    name: props.method.name,
    description: props.method.description,
});

// Cargar los datos del método al montar el componente
onMounted(() => {
    method.value = props.method;
});

const submitForm = () => {
    Inertia.put(route('paymentMethods.update', props.method.id), method.value);
};
</script>
