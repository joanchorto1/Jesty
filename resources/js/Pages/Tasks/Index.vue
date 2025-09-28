<template>
    <AppLayout>
    <div>
        <h1>Tareas</h1>
        <inertia-link :href="route('tasks.create')" class="btn btn-primary">Crear Tarea</inertia-link>
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-left">
            <tr>
                <th class="px-4 py-3 text-sm font-semibold text-gray-600">Título</th>
                <th class="px-4 py-3 text-sm font-semibold text-gray-600">Relacionado con</th>
                <th class="px-4 py-3 text-sm font-semibold text-gray-600">Fecha de vencimiento</th>
                <th class="px-4 py-3 text-sm font-semibold text-gray-600">Estado</th>
                <th class="px-4 py-3 text-sm font-semibold text-gray-600 text-right">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="task in tasks.data" :key="task.id" class="border-b last:border-b-0 hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-800">
                    <p class="font-medium">{{ task.title }}</p>
                    <p v-if="task.description" class="text-sm text-gray-500">{{ task.description }}</p>
                </td>
                <td class="px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">{{ task.taskable_type_label || 'Sin relación' }}</p>
                    <p class="text-gray-700">{{ task.taskable_label || '—' }}</p>
                </td>
                <td class="px-4 py-3 text-gray-700">{{ task.due_date }}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold"
                          :class="statusClass(task.status)">
                        {{ formatStatus(task.status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <inertia-link :href="route('tasks.show', task.id)" class="text-blue-500 hover:text-blue-700 text-sm">Ver</inertia-link>
                    <inertia-link :href="route('tasks.edit', task.id)" class="text-yellow-500 hover:text-yellow-600 text-sm">Editar</inertia-link>
                    <button @click="deleteTask(task.id)" class="text-red-500 hover:text-red-700 text-sm">Eliminar</button>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="mt-4 flex justify-between items-center" v-if="tasks.links">
            <button
                v-if="tasks.prev_page_url"
                class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                @click="changePage(tasks.current_page - 1)"
            >Anterior</button>
            <span class="text-sm text-gray-600">Página {{ tasks.current_page }} de {{ tasks.last_page }}</span>
            <button
                v-if="tasks.next_page_url"
                class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                @click="changePage(tasks.current_page + 1)"
            >Siguiente</button>
        </div>
    </div>

    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    tasks: { type: Object, required: true },
});

const deleteTask = (id) => {
    if (confirm('¿Estás seguro de eliminar esta tarea?')) {
        Inertia.delete(route('tasks.destroy', id));
    }
};

const changePage = (page) => {
    Inertia.get(route('tasks.index'), { page }, { preserveScroll: true });
};

const formatStatus = (status) => {
    if (!status) {
        return '';
    }

    const lower = status.toLowerCase();

    switch (lower) {
        case 'pendiente':
            return 'Pendiente';
        case 'en progreso':
            return 'En progreso';
        case 'completada':
            return 'Completada';
        default:
            return status;
    }
};

const statusClass = (status) => {
    const base = 'bg-gray-200 text-gray-700';
    if (!status) {
        return base;
    }

    const lower = status.toLowerCase();

    if (lower === 'pendiente') {
        return 'bg-yellow-100 text-yellow-700';
    }

    if (lower === 'en progreso') {
        return 'bg-blue-100 text-blue-700';
    }

    if (lower === 'completada') {
        return 'bg-green-100 text-green-700';
    }

    return base;

};
</script>
