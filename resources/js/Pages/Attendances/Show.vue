<template>
    <AppLayout>
    <div class=" flex flex-col p-6 w-full bg-white shadow rounded-lg">
        <h1 class="text-2xl font-bold mb-4">Detalles de Asistencia</h1>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-100 p-4 rounded">
                <span class="font-semibold text-gray-700">Empleado:</span>
                <p>{{ attendance.employee.name }}</p>
            </div>
            <div class="bg-gray-100 p-4 rounded">
                <span class="font-semibold text-gray-700">Fecha:</span>
                <p>{{ attendance.date }}</p>
            </div>
            <div class="bg-gray-100 p-4 rounded">
                <span class="font-semibold text-gray-700">Hora de Entrada:</span>
                <p>{{ attendance.check_in }}</p>
            </div>
            <div class="bg-gray-100 p-4 rounded">
                <span class="font-semibold text-gray-700">Hora de Salida:</span>
                <p>{{ attendance.check_out }}</p>
            </div>
            <div class="bg-gray-100 p-4 rounded">
                <span class="font-semibold text-gray-700">Horas Totales:</span>
                <p>{{ attendance.total_hours }}</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <button
                @click="goBack"
                class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400"
            >
                Volver
            </button>
            <button
                @click="editAttendance"
                class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600"
            >
                Editar
            </button>
        </div>
    </div>
    </AppLayout>
</template>

<script>
import { defineProps, defineEmits } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
export default {
    components: {AppLayout},
    props: {
        attendance: {
            type: Object,
            required: true,
        },
    },
    setup() {
        const goBack = () => {
            history.back();
        };

        const editAttendance = () => {
            // Navega a la página de edición
            window.location.href = `/attendances/${attendance.id}/edit`;
        };

        return {
            goBack,
            editAttendance,
        };
    },
};
</script>

<style scoped>
.container {
    max-width: 800px;
}
</style>
