<template>
    <AppLayout>
        <div class="flex flex-col bg-gray-100 min-h-screen p-6">
            <!-- Encabezado con Bienvenida y Usuario -->
            <div class="flex items-center bg-white rounded-lg p-5 shadow-md justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-700">Bienvenido, {{ user.name }}!</h1>
            </div>

            <!-- Contenido Principal -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Calendario -->
                <div class="bg-white shadow-md rounded-lg p-6 col-span-2">
                    <div class=" border-b pb-10">
                        <Calendar id="calendar" class="w-full " />

                    </div>

                    <!-- Tareas de Hoy -->
                    <div class="mt-6">
                        <div class="flex mt-10 w-full justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-600 mb-3">Tareas de hoy ({{ tasks.length }})</h3>
                            <NavLink :href="route('user_tasks.create')" class="text-white bg-blue-500 rounded-lg hover:bg-blue-600 px-4 py-2">
                                Nueva Tarea
                            </NavLink>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full mt-10  table-auto bg-white shadow-md rounded-lg overflow-hidden">
                                <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-gray-700">Tarea</th>
                                    <th class="px-4 py-2 text-gray-700">Fecha de Finalización</th>
                                    <th class="px-4 py-2 text-gray-700">Estado</th>
                                    <th class="px-4 py-2 text-gray-700">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="task in tasks" :key="task.id" class="border-b  items-center justify-center hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-600">{{ task.title }}</td>
                                    <td class="px-4 py-3 text-gray-400">{{ task.due_date }}</td>
                                    <td class="px-4 py-3 text-gray-400">{{ task.status }}</td>
                                    <td class="px-4 py-3 items-center justify-center  flex space-x-2">
                                        <NavLink :href="route('user_tasks.show', task.id)" class="text-blue-500 hover:text-blue-700"><InfoIcon class="fill-gray-950 w-5 h-5 " title="Ver Mas"/></NavLink>
                                        <NavLink :href="route('user_tasks.edit', task.id)" class="text-yellow-500 hover:text-yellow-700"><EditIcon class="fill-gray-950 w-5 h-5" title="Editar"/> </NavLink>
                                        <NavLink :href="route('user_tasks.mark_as_completed', task.id)" v-if="task.status !== 'completed'" class="text-green-500 " title="Marcar como Finalizado"><MenuCategoryIcon class="fill-gray-950 w-5 h-5 "/> </NavLink>
                                        <NavLink :href="route('user_tasks.mark_as_in_progress', task.id)" v-if="task.status !== 'in_progress'" class="text-gray-500 " title="Marcar como Progreso"><MenuProductIcon  title="Marcar como Progreso" class="fill-gray-950 w-5 h-5"/>
                                        </NavLink>
                                        <button @click="deleteTask(task.id)" class="text-red-500"><DeleteIcon class="fill-gray-950 h-5 w-5 hover:border-b-2" title="eliminar"/> </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>




                <!-- Notificaciones -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Notificaciones</h2>
                    <ul class="space-y-4">
                        <li v-for="notification in notifications" :key="notification.id" class="p-4 bg-blue-100 rounded-lg">
                            <p class="text-gray-700 font-medium">{{ notification.title }}</p>
                            <p class="text-sm text-gray-500">{{ notification.message }}</p>
                            <NavLink :href="route('user-notifications.markAsRead',notification.id)" class="mt-2 text-blue-500 hover:text-blue-700">Marcar comol eido</NavLink>
                        </li>
                    </ul>
                </div>
            </div>



        </div>
    </AppLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import Calendar from '@/Components/Calendar.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import MenuClientsIcon from "@/Components/Icons/MenuClientsIcon.vue";
import NavLink from "../../../vendor/laravel/jetstream/stubs/inertia/resources/js/Components/NavLink.vue";
import {Inertia} from "@inertiajs/inertia";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import MenuProductIcon from "@/Components/Icons/MenuProductIcon.vue";
import MenuCategoryIcon from "@/Components/Icons/MenuCategoryIcon.vue";


const props = defineProps({
    user: Object,
    notifications: Array,
    tasks: Array,
    teamChat: Array,
});

function deleteTask(taskId) {
    if (confirm('¿Estás seguro de que deseas eliminar esta tarea?')) {
    Inertia.delete(route('user_tasks.destroy', taskId));
    }
}
</script>

<style scoped>
#calendar {
    max-height: 400px;
    border-radius: 8px;
    overflow: hidden;
}
</style>
