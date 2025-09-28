<template>
    <AppLayout>
        <div class="p-6 bg-gray-100 min-h-screen">
            <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6 space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">{{ task.title }}</h1>
                        <p class="text-sm text-gray-500">Estado: <span class="capitalize">{{ statusLabel(task.status) }}</span></p>
                    </div>
                    <Link :href="route('tasks.edit', task.id)" class="px-3 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                        Editar
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded">
                        <p class="text-sm font-medium text-gray-500">Fecha límite</p>
                        <p class="text-base text-gray-800">{{ formatDate(task.due_date) }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <p class="text-sm font-medium text-gray-500">Proyecto</p>
                        <p class="text-base text-gray-800">{{ task.project?.name ?? '—' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <p class="text-sm font-medium text-gray-500">Lead</p>
                        <p class="text-base text-gray-800">{{ task.lead?.name ?? '—' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <p class="text-sm font-medium text-gray-500">Oportunidad</p>
                        <p class="text-base text-gray-800">{{ task.opportunity?.name ?? '—' }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Descripción</p>
                    <p class="text-gray-700 whitespace-pre-line">{{ task.description || 'Sin descripción' }}</p>
                </div>

                <div class="flex justify-end">
                    <Link :href="route('tasks.index')" class="px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Volver al listado
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    task: { type: Object, required: true },
});

const statusLabel = (status) => {
    switch (status) {
        case 'in_progress':
            return 'En progreso';
        case 'completed':
            return 'Completada';
        default:
            return 'Pendiente';
    }
};

const formatDate = (date) => {
    if (!date) {
        return '—';
    }
    return new Date(date).toLocaleDateString();
};
</script>
