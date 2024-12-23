<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Nóminas</h2>
                        <p class="text-blue-300 text-2xl">{{ totalPayrolls }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabla de Nóminas -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('payrolls.create')" class="bg-blue-500 hover:text-gray-50 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Agregar nueva Nómina
                    <AddIcon class="w-6 h-6 fill-gray-200 ml-3"/>
                </NavLink>
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Empleado</th>
                        <th class="px-4 py-2 text-left">Fecha de Pago</th>
                        <th class="px-4 py-2 text-left">Salario Base</th>
                        <th class="px-4 py-2 text-left">Salario Neto</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="payroll in payrolls" :key="payroll.id" class="border-t">
                        <!-- Mostrar nombre del empleado -->
                        <td class="px-4 py-2">
                            {{ getEmployeeName(payroll.employee_id) }}
                        </td>
                        <!-- Fecha de pago -->
                        <td class="px-4 py-2">{{ payroll.payment_date }}</td>
                        <!-- Salario base -->
                        <td class="px-4 py-2">{{ payroll.base_salary | currency }}</td>
                        <!-- Estado -->
                        <td class="px-4 py-2">{{ payroll.net_pay }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('payrolls.show', payroll)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('payrolls.edit', payroll)" class="text-yellow-500">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deletePayroll(payroll.id)" class="text-red-500 ml-2">
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
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import { computed } from 'vue';

const props = defineProps({
    payrolls: Array,
    employees: Array
});

// Contar el número de nóminas
const totalPayrolls = computed(() => props.payrolls.length);

// Función para obtener el nombre del empleado
const getEmployeeName = (employeeId) => {
    const employee = props.employees.find(emp => emp.id === employeeId);
    return employee ? employee.name : 'Empleado no encontrado';
};

// Eliminar nómina
const deletePayroll = (payrollId) => {
    if (confirm("¿Estás seguro de que deseas eliminar esta nómina?")) {
        Inertia.delete(route('payrolls.destroy', payrollId));
    }
};
</script>

<style scoped>
/* Estilo adicional */
.text-lg {
    line-height: 1.75rem;
}
</style>
