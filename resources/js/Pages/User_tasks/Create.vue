<template>
    <AppLayout>
        <div class="flex flex-col bg-gray-100 min-h-screen p-6">
            <!-- Encabezado -->
            <div class="flex items-center bg-white rounded-lg p-5 shadow-md justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-700">Crear Nueva Tarea</h1>
            </div>

            <!-- Formulario de Creación de Tarea -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="title">Título</label>
                        <input type="text" v-model="form.title" id="title" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Descripción</label>
                        <textarea v-model="form.description" id="description" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="due_date">Fecha de Finalización</label>
                        <input type="date" v-model="form.due_date" id="due_date" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div class="flex justify-end">
                        <NavLink :href="route('user_tasks.index')" class="text-gray-700 bg-gray-300 hover:bg-gray-400 rounded-lg px-4 py-2 mr-2">Cancelar</NavLink>
                        <button type="submit" class="bg-blue-500 text-white hover:bg-blue-600 rounded-lg px-4 py-2">Crear Tarea</button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from '@/Components/NavLink.vue';

const form = reactive({
    title: '',
    description: '',
    due_date: ''
});

function submit() {
    router.post(route('user_tasks.store'), form);
}
</script>

<style scoped>
label {
    font-weight: 500;
}
</style>
