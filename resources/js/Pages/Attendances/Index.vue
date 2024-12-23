<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Asistencias</h2>
                        <p class="text-blue-300 text-2xl">{{ totalAttendances }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabla de Asistencias -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('attendances.create')" class="bg-blue-500 hover:text-gray-50 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Agregar nueva Asistencia
                    <AddIcon class="w-6 h-6 fill-gray-200 ml-3"/>
                </NavLink>
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Empleado</th>
                        <th class="px-4 py-2 text-left">Fecha de Asistencia</th>
                        <th class="px-4 py-2 text-left">Total de horas</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="attendance in filteredAttendances" :key="attendance.id" class="border-t">
                        <td class="px-4 py-2">{{getEmployeeName(attendance.employee_id)}}</td>
                        <td class="px-4 py-2">{{ attendance.date }}</td>
                        <td class="px-4 py-2">{{ attendance.total_hours }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('attendances.show', attendance.id)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('attendances.edit', attendance.id)" class="text-yellow-500">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteAttendance(attendance.id)" class="text-red-500 ml-2">
                                <DeleteIcon class="w-5 h-5"/>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import NavLink from "@/Components/NavLink.vue";
import { computed, reactive } from 'vue';
import InfoIcon from "@/Components/Icons/InfoIcon.vue";

const props = defineProps({
    attendances: Array,
    employees: Array
});

const filteredAttendances = reactive(props.attendances);
const totalAttendances = computed(() => filteredAttendances.length);

const getEmployeeName = (employeeId) => {
    const employee = props.employees.find(employee => employee.id === employeeId);
    return employee ? employee.name : '';
};

const deleteAttendance = (attendanceId) => {
    if (confirm("¿Estás seguro de que deseas eliminar esta asistencia?")) {
        Inertia.delete(route('attendances.destroy', attendanceId));
    }
};
</script>
