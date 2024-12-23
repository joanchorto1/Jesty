<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Departamentos</h2>
                        <p class="text-blue-300 text-2xl">{{ totalDepartments }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabla de Departamentos -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('departments.create')" class="bg-blue-500 hover:text-gray-50 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Agregar nuevo Departamento
                    <AddIcon class="w-6 h-6 fill-gray-200 ml-3"/>
                </NavLink>
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Gerente</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="department in filteredDepartments" :key="department.id" class="border-t">
                        <td class="px-4 py-2">{{ department.name }}</td>
                        <td class="px-4 py-2">{{ getEmployeeName(department.manager_id) }}
                        </td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('departments.edit', department.id)" class="text-yellow-500">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteDepartment(department.id)" class="text-red-500 ml-2">
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
    departments: Array,
    employees: Array
});

const filteredDepartments = reactive(props.departments);
const totalDepartments = computed(() => filteredDepartments.length);

// Función para obtener el nombre del empleado
const getEmployeeName = (employeeId) => {
    const employee = props.employees.find(emp => emp.id === employeeId);
    return employee ? employee.name : 'Empleado no encontrado';
};

const deleteDepartment = (departmentId) => {
    if (confirm("¿Estás seguro de que deseas eliminar este departamento?")) {
        Inertia.delete(route('departments.destroy', departmentId));
    }
};
</script>
