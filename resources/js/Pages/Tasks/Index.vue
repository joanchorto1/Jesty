<template>
    <AppLayout>
        <div class="p-6 bg-gray-100 min-h-screen">
            <div class="max-w-6xl mx-auto bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-semibold text-gray-800">Tareas CRM</h1>
                    <Link :href="route('tasks.create')" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                        Crear Tarea
                    </Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oportunidad</th>
                                <th class="px-4 py-2" />
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="task in tasks.data" :key="task.id">
                                <td class="px-4 py-2 text-sm text-gray-700">
                                    <Link :href="route('tasks.show', task.id)" class="text-blue-600 hover:underline">
                                        {{ task.title }}
                                    </Link>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700 capitalize">{{ statusLabel(task.status) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ formatDate(task.due_date) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ task.project?.name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ task.lead?.name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ task.opportunity?.name ?? '—' }}</td>
                                <td class="px-4 py-2 text-right text-sm">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('tasks.edit', task.id)" class="text-blue-500 hover:underline">Editar</Link>
                                        <button @click="deleteTask(task.id)" class="text-red-600 hover:underline">Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-between items-center" v-if="tasks.links?.length">
                    <div class="text-sm text-gray-600">
                        Mostrando {{ tasks.from }}-{{ tasks.to }} de {{ tasks.total }} tareas
                    </div>
                    <div class="flex gap-2">
                        <Link
                            v-for="link in tasks.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            class="px-3 py-1 rounded border"
                            :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-700 border-gray-300 hover:bg-gray-100'"
                            v-html="link.label"
                            preserve-scroll
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    tasks: { type: Object, required: true },
});

const deleteTask = (id) => {
    if (confirm('¿Estás seguro de eliminar esta tarea?')) {
        router.delete(route('tasks.destroy', id));
    }
};

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
