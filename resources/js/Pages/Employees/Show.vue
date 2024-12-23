<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">

            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Asistencias</h2>
                        <p class="text-blue-300 text-2xl">{{ filteredAttendances.length }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Permisos</h2>
                        <p class="text-blue-300 text-2xl">{{ filteredLeaves.length }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Evaluaciones de Desempeño</h2>
                        <p class="text-blue-300 text-2xl">{{ filteredPerformanceReviews.length }}</p>
                    </div>
                </div>
            </div>

            <!-- Información del empleado -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-2xl text-blue-500 font-bold">Detalles del Empleado</h2>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <p><strong>Nombre:</strong> {{ employee.name }}</p>
                    <p><strong>Email:</strong> {{ employee.email }}</p>
                    <p><strong>Teléfono:</strong> {{ employee.phone }}</p>
                    <p><strong>Dirección:</strong> {{ employee.address }}</p>
                    <p><strong>Cargo:</strong> {{ employee.job_title }}</p>
                    <p><strong>Departamento:</strong> {{ department.name }}</p>
                    <p><strong>Salario:</strong> {{ employee.salary }}</p>
                    <p><strong>Fecha de Ingreso:</strong> {{ employee.hire_date }}</p>
                    <p><strong>Estado:</strong> {{ employee.status }}</p>
                    <p><strong>DNI:</strong> {{ employee.dni }}</p>
                    <p><strong>Número de Seguridad Social:</strong> {{ employee.nnss }}</p>
                    <p><strong>IBAN:</strong> {{ employee.iban }}</p>
                    <p><strong>Fecha de Nacimiento:</strong> {{ employee.birth_date }}</p>
                </div>
            </div>

            <!-- Asistencias -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="text-xl font-semibold mb-4">Asistencias</h3>
                <table class="w-full table-auto">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Entrada</th>
                        <th class="px-4 py-2 text-left">Salida</th>
                        <th class="px-4 py-2 text-left">Horas Totales</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="attendance in filteredAttendances" :key="attendance.id" class="border-t">
                        <td class="px-4 py-2">{{ attendance.date }}</td>
                        <td class="px-4 py-2">{{ attendance.check_in }}</td>
                        <td class="px-4 py-2">{{ attendance.check_out }}</td>
                        <td class="px-4 py-2">{{ attendance.total_hours }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Permisos -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="text-xl font-semibold mb-4">Permisos</h3>
                <table class="w-full table-auto">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha de Inicio</th>
                        <th class="px-4 py-2 text-left">Fecha de Fin</th>
                        <th class="px-4 py-2 text-left">Tipo</th>
                        <th class="px-4 py-2 text-left">Razón</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="leave in filteredLeaves" :key="leave.id" class="border-t">
                        <td class="px-4 py-2">{{ leave.start_date }}</td>
                        <td class="px-4 py-2">{{ leave.end_date }}</td>
                        <td class="px-4 py-2">{{ leave.type }}</td>
                        <td class="px-4 py-2">{{ leave.reason }}</td>
                        <td class="px-4 py-2">{{ leave.status }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Evaluaciones de desempeño -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="text-xl font-semibold mb-4">Evaluaciones de Desempeño</h3>
                <table class="w-full table-auto">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha de Evaluación</th>
                        <th class="px-4 py-2 text-left">Puntuación</th>
                        <th class="px-4 py-2 text-left">Revisado por</th>
                        <th class="px-4 py-2 text-left">Comentarios</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="review in filteredPerformanceReviews" :key="review.id" class="border-t">
                        <td class="px-4 py-2">{{ review.review_date }}</td>
                        <td class="px-4 py-2">{{ review.score }}</td>
                        <td class="px-4 py-2">{{ review.reviewed_by }}</td>
                        <td class="px-4 py-2">{{ review.comments }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Capacitaciones -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="text-xl font-semibold mb-4">Capacitaciones</h3>
                <table class="w-full table-auto">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-left">Duración</th>
                        <th class="px-4 py-2 text-left">Fecha</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="training in trainings" :key="training.id" class="border-t">
                        <td class="px-4 py-2">{{ training.name }}</td>
                        <td class="px-4 py-2">{{ training.description }}</td>
                        <td class="px-4 py-2">{{ training.duration }} horas</td>
                        <td class="px-4 py-2">{{ training.date }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Nómina -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="text-xl font-semibold mb-4">Nómina</h3>
                <table class="w-full table-auto">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha de Pago</th>
                        <th class="px-4 py-2 text-left">Salario Base</th>
                        <th class="px-4 py-2 text-left">Bonificaciones</th>
                        <th class="px-4 py-2 text-left">Deducciones</th>
                        <th class="px-4 py-2 text-left">Total Neto</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="payroll in filteredPayrolls" :key="payroll.id" class="border-t">
                        <td class="px-4 py-2">{{ payroll.pay_date }}</td>
                        <td class="px-4 py-2">{{ payroll.base_salary }}</td>
                        <td class="px-4 py-2">{{ payroll.bonuses }}</td>
                        <td class="px-4 py-2">{{ payroll.deductions }}</td>
                        <td class="px-4 py-2">{{ payroll.net_salary }}</td>
                        <td class="px-4 py-2">{{ payroll.status }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>


<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    employee: Object,
    leaves: Array,
    attendances: Array,
    department: Object,
    performanceReviews: Array,
    trainings: Array,
    payrolls: Array
});

console.log(props.trainings);
// Filtrar datos
const filteredAttendances = computed(() => props.attendances.filter(att => att.employee_id === props.employee.id));
const filteredLeaves = computed(() => props.leaves.filter(leave => leave.employee_id === props.employee.id));
const filteredPerformanceReviews = computed(() => props.performanceReviews.filter(review => review.employee_id === props.employee.id));
const filteredPayrolls = computed(() => props.payrolls.filter(payroll => payroll.employee_id === props.employee.id));
</script>

<style scoped>
/* Estilos personalizados */
</style>
