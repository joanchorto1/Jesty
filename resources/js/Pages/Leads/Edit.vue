<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen shadow-md">
            <h1 class="text-3xl font-bold mb-8 text-blue-500">Editar Lead</h1>

            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700">Nombre</label>
                        <input v-model="form.name" id="name" type="text" placeholder="Nombre del Lead"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required />
                    </div>

                    <div class="mb-4">
                        <label for="company_name" class="block text-gray-700">Compañía</label>
                        <input v-model="form.company_name" id="company_name" type="text" placeholder="Nombre de la Compañía"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required />
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-gray-700">Email</label>
                        <input v-model="form.email" id="email" type="email" placeholder="Correo Electrónico"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required />
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="block text-gray-700">Teléfono</label>
                        <input v-model="form.phone" id="phone" type="text" placeholder="Teléfono"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                    </div>

                    <div class="mb-4">
                        <label for="position" class="block text-gray-700">Posición</label>
                        <input v-model="form.position" id="position" type="text" placeholder="Posición"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                    </div>

                    <div class="mb-4">
                        <label for="source" class="block text-gray-700">Fuente</label>
                        <input v-model="form.source" id="source" type="text" placeholder="Fuente"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-gray-700">Estado</label>
                        <select v-model="form.status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="Nuevo">Nuevo</option>
                            <option value="En Proceso">En Proceso</option>
                            <option value="Convertido">Convertido</option>
                            <option value="Perdido">Perdido</option>
                        </select>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <button type="submit" class="bg-blue-900 p-2 rounded-full" title="Guardar Cambios">
                        <SaveIcon class="w-6 h-6 stroke-gray-50" />
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from "@/Components/Icons/SaveIcon.vue";

// Obtén los datos del Lead desde el servidor (se pasa desde el controlador a la vista)
const props = defineProps({
    lead: Object,
});

const form = ref({
    name: props.lead.name || '',
    company_name: props.lead.company_name || '',
    email: props.lead.email || '',
    phone: props.lead.phone || '',
    position: props.lead.position || '',
    source: props.lead.source || '',
    status: props.lead.status || 'Nuevo',
});

const submitForm = () => {
    Inertia.put(route('leads.update', props.lead.id), form.value);
};
</script>
