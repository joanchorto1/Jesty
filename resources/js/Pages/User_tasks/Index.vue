<template>
    <AppLayout>
    <div class="mt-6 m-5">
        <div class="flex w-full justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-600 mb-3">Tareas ({{ tasks.length }})</h3>
            <NavLink :href="route('user_tasks.adminCreate')" class="text-white bg-blue-500 m-2 rounded-lg hover:bg-blue-600 px-4 py-2">
                Nueva Tarea
            </NavLink>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2 text-gray-700">Tarea</th>
                    <th class="px-4 py-2 text-gray-700">Descripción</th>
                    <th class="px-4 py-2 text-gray-700">Fecha de Finalización</th>
                    <th class="px-4 py-2 text-gray-700">Estado</th>
                    <th class="px-4 py-2 text-gray-700">Proyecto</th>
                    <th class="px-4 py-2 text-gray-700">Usuario Asignado</th>
                    <th class="px-4 py-2 text-gray-700">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="task in tasks" :key="task.id" class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-600">{{ task.title }}</td>
                    <td class="px-4 py-3 text-gray-500 truncate w-40">{{ task.description }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ task.due_date }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ task.status }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ task.project ? task.project.name : '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ task.user ? task.user.name : assignedUser(task.user_id) }}</td>
                    <td class="px-4 py-3 flex space-x-2">
                        <NavLink :href="route('user_tasks.adminEdit', task.id)" class="text-yellow-500 hover:text-yellow-700"><EditIcon class="w-5 h-5 fill-gray-950"/></NavLink>
                        <button @click="deleteTask(task.id)" class="text-red-500 hover:text-red-700"><DeleteIcon class="w-5 h-5 fill-gray-950"/></button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    </AppLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import NavLink from "../../../../vendor/laravel/jetstream/stubs/inertia/resources/js/Components/NavLink.vue";

const props = defineProps({
    tasks: Array,
    users: Array,
});

const deleteTask = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta tarea?')) {
        router.delete(route('user_tasks.destroy', id));
    }
};

const assignedUser = (userId) => {
    const user = props.users.find((candidate) => candidate.id === userId);
    return user ? user.name : '—';
};
</script>
