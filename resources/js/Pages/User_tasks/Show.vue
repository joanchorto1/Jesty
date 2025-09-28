<template>
    <AppLayout>
        <div class="flex flex-col bg-gray-100 min-h-screen p-6">
            <!-- Encabezado -->
            <div class="flex items-center bg-white rounded-lg p-5 shadow-md justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-700">Detalles de la Tarea</h1>
                <a class="bg-blue-500 text-gray-50 p-2 rounded-lg" href="/dashboard">Volver</a>
            </div>

            <!-- Detalles de la Tarea -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <p class="text-xl font-semibold text-gray-800 mb-4">{{ task.title }}</p>
                <p class="text-gray-600 mb-4"><strong>Descripción:</strong> {{ task.description }}</p>
                <p class="text-gray-600 mb-4"><strong>Fecha de Finalización:</strong> {{ task.due_date }}</p>
                <p class="text-gray-600 mb-4"><strong>Estado:</strong> <span>{{ task.status }}</span></p>
                <p class="text-gray-600 mb-4"><strong>Proyecto:</strong> {{ task.project ? task.project.name : 'Sin proyecto' }}</p>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-6 flex space-x-4">
                <NavLink :href="route('user_tasks.edit', task.id)" class="text-yellow-500 hover:text-yellow-700">Editar</NavLink>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import { router } from '@inertiajs/vue3';

defineProps({
    task: Object
});




</script>

<style scoped>
label {
    font-weight: 500;
}
</style>
