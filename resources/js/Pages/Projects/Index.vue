<template>
    <AppLayout>
        <div class="p-6 bg-gray-100 min-h-screen">
            <div class="max-w-7xl mx-auto bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Proyectos</h1>
                    <Link :href="route('projects.create')" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                        Nuevo proyecto
                    </Link>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Cliente</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Departamento</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Fechas</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Fases</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Responsables</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="project in projects" :key="project.id">
                            <td class="px-4 py-3">
                                <Link :href="route('projects.show', project.id)" class="text-blue-600 hover:underline">
                                    {{ project.name }}
                                </Link>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ project.client ? project.client.name : '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ project.department ? project.department.name : '—' }}</td>
                            <td class="px-4 py-3">
                                <span :class="['px-2 py-1 rounded-full text-xs font-semibold', statusClasses(project.status)]">
                                    {{ statusLabel(project.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ formatDate(project.start_date) }} - {{ formatDate(project.end_date) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ project.phases?.length ?? 0 }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ project.responsibles?.length ?? 0 }}</td>
                            <td class="px-4 py-3 text-right text-sm">
                                <Link :href="route('projects.edit', project.id)" class="text-blue-500 hover:underline mr-3">Editar</Link>
                                <button @click="destroy(project.id)" class="text-red-600 hover:underline">Eliminar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="!projects.length" class="text-center text-gray-500 py-12">
                    No hay proyectos registrados.
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    projects: { type: Array, default: () => [] },
});

const destroy = (id) => {
    if (confirm('¿Deseas eliminar este proyecto?')) {
        router.delete(route('projects.destroy', id));
    }
};

const formatDate = (value) => {
    if (!value) {
        return '—';
    }
    return new Date(value).toLocaleDateString();
};

const statusLabel = (status) => {
    if (!status) {
        return 'Sin estado';
    }

    const normalized = status.toLowerCase();
    switch (normalized) {
        case 'in_progress':
            return 'En progreso';
        case 'completed':
            return 'Completado';
        case 'invoiced':
            return 'Facturado';
        case 'planning':
            return 'Planificación';
        default:
            return status;
    }
};

const statusClasses = (status) => {
    const normalized = (status || '').toLowerCase();
    switch (normalized) {
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'invoiced':
            return 'bg-indigo-100 text-indigo-700';
        case 'in_progress':
            return 'bg-blue-100 text-blue-700';
        case 'planning':
            return 'bg-yellow-100 text-yellow-700';
        case 'archived':
            return 'bg-gray-200 text-gray-700';
        default:
            return 'bg-gray-100 text-gray-700';
    }
};
</script>
