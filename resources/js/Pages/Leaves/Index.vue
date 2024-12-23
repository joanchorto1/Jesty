<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Permisos</h2>
                        <p class="text-blue-300 text-2xl">{{ totalLeaves }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabla de permisos -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('leaves.create')" class="bg-blue-500 hover:text-gray-50 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Agregar nuevo Permiso
                    <AddIcon class="w-6 h-6 fill-gray-200 ml-3"/>
                </NavLink>
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Empleado</th>
                        <th class="px-4 py-2 text-left">Razón</th>
                        <th class="px-4 py-2 text-left">Tipo</th>
                        <th class="px-4 py-2 text-left">Fecha de Inicio</th>
                        <th class="px-4 py-2 text-left">Fecha de Fin</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="leave in filteredLeaves" :key="leave.id" class="border-t">
                        <td class="px-4 py-2">{{ findName(leave.employee_id) }}</td>
                        <td class="px-4 py-2">{{ leave.reason }}</td>
                        <td class="px-4 py-2">{{ leave.type }}</td>
                        <td class="px-4 py-2">{{ leave.start_date }}</td>
                        <td class="px-4 py-2">{{ leave.end_date }}</td>
                        <td class="px-4 py-2">{{ leave.status }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('leaves.goToEdit', leave.id)" class="text-yellow-500">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteLeave(leave.id)" class="text-red-500 ml-2">
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
    leaves: Array,
    employees: Array,
});

const filteredLeaves = reactive(props.leaves);
const totalLeaves = computed(() => filteredLeaves.length);
const findName = (id) => {
    return props.employees.find(employee => employee.id === id).name;
};

const deleteLeave = (leaveId) => {
    if (confirm("¿Estás seguro de que deseas eliminar este permiso?")) {
        Inertia.delete(route('leaves.destroy', leaveId));
    }
};
</script>
