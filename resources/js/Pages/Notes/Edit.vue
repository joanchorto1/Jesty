<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md max-w-3xl mx-auto">
                <h1 class="text-2xl font-bold text-blue-500 mb-4">Editar Nota</h1>

                <form @submit.prevent="submit">
                    <!-- Campo Lead -->

                    <!-- Campo Contenido de Nota -->
                    <div class="mb-4">
                        <label for="content" class="block text-gray-700 font-medium mb-2">Nota</label>
                        <textarea
                            id="content"
                            v-model="form.content"
                            class="w-full border border-gray-300 rounded-md p-2"
                            rows="5"
                            required
                        ></textarea>
                    </div>

                    <!-- Botones -->
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
    note: Object,
});

// Datos iniciales
const lead = ref(props.lead);
const opportunity = ref(props.opportunity);
const form = ref({
    content: props.note.content,
    opportunity_id: props.note.opportunity_id,
    lead_id: props.note.lead_id,
});

// Función para manejar el envío del formulario
const submit = () => {
    Inertia.put(route('notes.update', props.note.id), {
        content: form.value.content,
    });
};

// Función para cancelar y redirigir
const cancel = () => {
    Inertia.visit(route('opportunities.show', { id: opportunity.value.id }));
};
</script>
