<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md max-w-3xl mx-auto">
                <h1 class="text-2xl font-bold text-blue-500 mb-4">Crear Nueva Nota</h1>

                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label for="lead" class="block text-gray-700 font-medium mb-2">Lead</label>
                        <input
                            type="text"
                            id="lead"
                            class="w-full border border-gray-300 rounded-md p-2"
                            :value="lead.name"
                            disabled
                        />
                    </div>

                    <div class="mb-4">
                        <label for="opportunity" class="block text-gray-700 font-medium mb-2">Oportunidad</label>
                        <input
                            type="text"
                            id="opportunity"
                            class="w-full border border-gray-300 rounded-md p-2"
                            :value="opportunity.description"
                            disabled
                        />
                    </div>

                    <div class="mb-4">
                        <label for="content" class="block text-gray-700 font-medium mb-2">Contenido</label>
                        <textarea
                            id="content"
                            v-model="form.content"
                            class="w-full border border-gray-300 rounded-md p-2"
                            placeholder="Escribe el contenido de la nota"
                            rows="5"
                            required
                        ></textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button
                            type="button"
                            class="flex items-center bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400"
                            @click="cancel"
                        >
                            <DeleteIcon class="w-5 h-5 mr-2" />
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="flex items-center bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                        >
                            <SaveIcon class="w-5 h-5 mr-2" />
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from '@/Components/Icons/SaveIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';

// Props recibidos
const props = defineProps({
    lead: Object,
    opportunity: Object,
});

// Datos del formulario
const form = ref({
    content: '',
});

// Referencias a los props
const lead = ref(props.lead);
const opportunity = ref(props.opportunity);

// Función para manejar el envío del formulario
const submit = () => {
    Inertia.post(route('notes.store'), {
        lead_id: lead.value.id,
        opportunity_id: opportunity.value.id,
        content: form.value.content,
    });
};

// Función para cancelar y redirigir
const cancel = () => {
    Inertia.visit(route('opportunities.show', { id: opportunity.value.id }));
};
</script>
