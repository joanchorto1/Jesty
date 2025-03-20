<template>
    <AppLayout>
    <div class="max-w-lg mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-700 mb-6">Crear Nueva Tarea</h2>

        <form @submit.prevent="createTask">
            <div class="mb-4">
                <label class="block text-gray-600 mb-2">Título</label>
                <input v-model="form.title" type="text" class="w-full p-2 border rounded-lg" required />
            </div>

            <div class="mb-4">
                <label class="block text-gray-600 mb-2">Descripción</label>
                <textarea v-model="form.description" class="w-full p-2 border rounded-lg" required></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-600 mb-2">Fecha de Finalización</label>
                <input v-model="form.due_date" type="date" class="w-full p-2 border rounded-lg" required />
            </div>

            <div class="mb-4">
                <label class="block text-gray-600 mb-2">Asignar a Usuario</label>
                <select v-model="form.user_id" class="w-full p-2 border rounded-lg" required>
                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">Crear Tarea</button>
        </form>
    </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    users: Array,
});

const form = ref({
    title: '',
    description: '',
    due_date: '',
    user_id: '',
});

function createTask() {
    router.post(route('user_tasks.adminStore'), form.value);
}
</script>
