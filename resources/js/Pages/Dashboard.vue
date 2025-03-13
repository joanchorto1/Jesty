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
                    <Calendar id="calendar" class="w-full" />

                    <!-- Tareas de Hoy -->
                    <div class="mt-6">
                            <h3 class="text-lg font-semibold justify-start text-gray-600 mb-3">Tareas de hoy (nº de tareas)</h3>
                            <NavLink :href="route('user_tasks.create')" class="text-gray-50 bg-blue-500 rounded-lg hover:text-blue-700"><p class="text-gray-50  px-2 py-2 ">Nueva tarea</p></NavLink>

                        <ul class="space-y-2">
                            <li v-for="task in tasks" :key="task.id" class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" class="form-checkbox" />
                                    <span class="text-gray-600">{{ task.name }}</span>
                                </div>
                                <span class="text-sm text-gray-400">{{ task.time }}</span>
                            </li>
                        </ul>
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

const props = defineProps({
    user: Object,
    notifications: Array,
    tasks: Array,
    teamChat: Array,
});
</script>

<style scoped>
#calendar {
    max-height: 400px;
    border-radius: 8px;
    overflow: hidden;
}
</style>
