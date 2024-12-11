<template>
    <AppLayout>
    <div class="flex flex-col bg-gray-100 min-h-screen">
        <div class="flex flex-col  md:flex-row md:space-x-4 p-4">
            <!-- Panel de Información del Usuario -->
            <div class="bg-white shadow-md rounded-lg p-4 w-full md:w-1/4">
                <h2 class="text-xl pb-2 border-b-2 font-semibold mb-4">Información del Usuario</h2>
                <div>
                    <img :src="props.user.img">
                </div>
                <div class="mb-2">
                    <strong>Nombre:</strong> {{ $page.props.auth.user.name }}
                </div>
                <div class="mb-2">
                    <strong>Email:</strong> {{    $page.props.auth.user.email }}
                </div>
                <div class="mb-2">
                    <strong>Rol:</strong> {{role.name}}
                </div>

            </div>

            <!-- Calendario -->
            <div class="bg-white shadow-md rounded-lg text-center p-4 w-full md:w-2/4">
                <h2 class="text-xl pb-2 border-b-2 font-semibold mb-4">Calendario</h2>
                <Calendar class="p-10"/>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4 w-full md:w-1/4">
                <h2 class="text-xl pb-2 border-b-2 font-semibold mb-4">Tareas</h2>
                <ul>
                    <li v-for="tearea in tasks">{{tarea.name}}</li>
                </ul>
            </div>
        </div>

        <!-- Lista de Notificaciones -->
        <div class="bg-white shadow-md mx-5 rounded-lg p-4 mt-4">
            <h2 class="text-lg font-semibold mb-4">Notificaciones</h2>
            <ul>
                <li v-for="notification in notifications" :key="notification.id" class="border-b last:border-b-0 py-2">
                    <span class="font-semibold">{{ notification.title }}</span>
                    <p class="text-sm text-gray-600">{{ notification.message }}</p>
                </li>
            </ul>
        </div>
    </div>
    </AppLayout>
</template>

<script setup>
import { defineProps, onMounted } from 'vue';
import Calendar from "@/Components/Calendar.vue";
import AppLayout from "@/Layouts/AppLayout.vue";


const props = defineProps({
    role: Object,
    notifications: Array,
    user: Object,
    tasks: Array,
    company: Object,
});
</script>

<style scoped>
#calendar {
    max-height: 400px; /* Ajusta la altura del calendario según tus necesidades */
}
</style>

