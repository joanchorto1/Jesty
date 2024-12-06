<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen shadow-md">
            <h1 class="text-3xl font-bold mb-8">Crear Oportunidad</h1>

            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Título -->


                    <!-- Descripción -->
                    <div class="mb-4 md:col-span-2">
                        <label for="description" class="block text-gray-700">Descripción</label>
                        <textarea v-model="form.description" id="description" rows="4" placeholder="Descripción"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <!-- Valor -->
                    <div class="mb-4">
                        <label for="value" class="block text-gray-700">Valor</label>
                        <input v-model="form.value" id="value" type="number" placeholder="Valor"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required />
                    </div>

                    <!-- Probabilidad -->
                    <div class="mb-4">
                        <label for="probability" class="block text-gray-700">Probabilidad (%)</label>
                        <input v-model="form.probability" id="probability" type="number" min="0" max="100"
                               placeholder="Probabilidad de éxito"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required />
                    </div>

                    <!-- Estado -->
                    <div class="mb-4">
                        <label for="status" class="block text-gray-700">Estado</label>
                        <select v-model="form.status" id="status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="En proceso">En proceso</option>
                            <option value="Ganada">Ganada</option>
                            <option value="Perdida">Perdida</option>
                        </select>
                    </div>

                    <!-- Lead -->
                    <div class="mb-4 md:col-span-2">
                        <label for="lead_id" class="block text-gray-700">Lead</label>
                        <p class="rounded-md w-full bg-white pl-2 py-3 border-gray-300 border">
                            {{ props.lead.name }}
                        </p>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <button type="submit" class="bg-blue-900 p-2 rounded-full" title="Guardar Oportunidad">
                        <SaveIcon class="w-6 h-6 stroke-gray-50" />
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from "@/Components/Icons/SaveIcon.vue";

// Props recibidos
const props = defineProps({
    lead: Object,
});

// Formulario inicializado
const form = ref({
    description: '',
    value: '',
    probability: '', // Nuevo campo para la probabilidad
    status: 'Abierta',
    lead_id: props.lead.id,
});

// Manejar el envío del formulario
const submitForm = () => {
    Inertia.post(route('opportunities.store'), form.value);
};
</script>
