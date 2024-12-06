<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen shadow-md">
            <h1 class="text-3xl font-bold mb-8">Editar Categoría de Gasto</h1>
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700">Nombre de la Categoría</label>
                        <input v-model="category.name" id="name" type="text" placeholder="Nombre de la Categoría"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700">Descripción</label>
                        <textarea v-model="category.description" id="description" rows="4"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                  placeholder="Descripción de la categoría"></textarea>
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

// Obtener la categoría existente desde los props
const props = defineProps({
    category: Object,
});
const category = ref({
    name: props.category.name,
    description: props.category.description,
});

// Cargar los datos de la categoría al montar el componente
onMounted(() => {
    category.value = props.category;
});

const submitForm = () => {
    Inertia.put(route('expenseCategories.update', props.category.id), category.value);
};
</script>
