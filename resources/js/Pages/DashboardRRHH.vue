<template>
    <AppLayout>
        <div class="w-full bg-gray-100 p-10">
            <!-- Sección de Widgets Globales -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalEmployees }}</p>
                    <p class="text-blue-700 font-semibold">Total de Empleados</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalDepartments }}</p>
                    <p class="text-blue-700 font-semibold">Total de Departamentos</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ averageSalary }}</p>
                    <p class="text-blue-700 font-semibold">Salario Promedio</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalEmployeesActive }}</p>
                    <p class="text-blue-700 font-semibold">Empleados Activos</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalProjects }}</p>
                    <p class="text-blue-700 font-semibold">Proyectos Totales</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ activeProjects }}</p>
                    <p class="text-blue-700 font-semibold">Proyectos Activos</p>
                </div>
            </div>

            <!-- Sección de Gráficos de Empleados -->
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Gráficos de Empleados</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Distribución de Empleados por Departamento</h3>
                    <PieChart :data="employeeDepartmentData" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Empleados por Año de Contratación</h3>
                    <BarChart :data="employeeHiringYearData" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Comparación de Salarios por Departamento</h3>
                    <BarChart :data="departmentSalaryComparisonData" />
                </div>
            </div>

            <!-- Sección de Gráficos de Departamentos -->
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Gráficos de Departamentos</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Distribución de Departamentos por Empleados</h3>
                    <PieChart :data="departmentEmployeeData" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Departamentos por Ubicación</h3>
                    <BarChart :data="departmentLocationData" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Comparación de Departamentos por Proyectos</h3>
                    <BarChart :data="departmentProjectComparisonData" />
                </div>
            </div>

            <h2 class="text-xl font-semibold text-gray-700 my-4">Estado de los Proyectos</h2>
            <div class="bg-white p-4 rounded-lg shadow">
                <BarChart :data="projectStatusData" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BarChart from '@/Components/BarChart.vue';
import PieChart from '@/Components/PieChart.vue';

const props = defineProps({
    employees: Array,
    departments: Array,
    projects: Array,
});

// Cálculos globales
const totalEmployees = computed(() => props.employees.length);
const totalDepartments = computed(() => props.departments.length);
const totalEmployeesActive = computed(() => props.employees.filter(employee => employee.status === 'active').length);
const averageSalary = computed(() => {
    if (!props.employees.length) {
        return 0;
    }
    const totalSalary = props.employees.reduce((acc, employee) => acc + (employee.salary || 0), 0);
    return (totalSalary / props.employees.length).toFixed(2);
});
const totalProjects = computed(() => props.projects.length);
const activeProjects = computed(() => props.projects.filter(project => project.status !== 'completed' && project.status !== 'archived').length);

// Datos de Empleados por Departamento
const employeeDepartmentData = computed(() => {
    const departmentTotals = props.employees.reduce((acc, employee) => {
        if (!acc[employee.department_id]) acc[employee.department_id] = 0;
        acc[employee.department_id] += 1;
        return acc;
    }, {});

    return {
        labels: Object.keys(departmentTotals).map(departmentId => {
            const department = props.departments.find(d => d.id === parseInt(departmentId));
            return department ? department.name : 'Departamento Desconocido';
        }),
        datasets: [
            {
                label: 'Empleados por Departamento',
                backgroundColor: ['#3B82F6', '#60A5FA', '#93C5FD'],
                data: Object.values(departmentTotals),
            },
        ],
    };
});

// Datos de Empleados por Año de Contratación
const employeeHiringYearData = computed(() => {
    const hiringYearTotals = props.employees.reduce((acc, employee) => {
        const year = new Date(employee.hire_date).getFullYear();
        if (!acc[year]) acc[year] = 0;
        acc[year] += 1;
        return acc;
    }, {});

    return {
        labels: Object.keys(hiringYearTotals),
        datasets: [
            {
                label: 'Empleados por Año de Contratación',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: Object.values(hiringYearTotals),
            },
        ],
    };
});

// Comparación de Salarios por Departamento
const departmentSalaryComparisonData = computed(() => {
    const departmentSalaryTotals = props.departments.reduce((acc, department) => {
        const departmentEmployees = props.employees.filter(employee => employee.department_id === department.id);
        const totalSalary = departmentEmployees.reduce((acc, employee) => acc + employee.salary, 0);
        const avgSalary = departmentEmployees.length > 0 ? totalSalary / departmentEmployees.length : 0;
        acc[department.name] = avgSalary;
        return acc;
    }, {});

    return {
        labels: Object.keys(departmentSalaryTotals),
        datasets: [
            {
                label: 'Salarios Promedio por Departamento',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: Object.values(departmentSalaryTotals),
            },
        ],
    };
});

// Datos de Departamentos por Empleados
const departmentEmployeeData = computed(() => {
    const departmentEmployeeTotals = props.departments.reduce((acc, department) => {
        const departmentEmployees = props.employees.filter(employee => employee.department_id === department.id);
        acc[department.name] = departmentEmployees.length;
        return acc;
    }, {});

    return {
        labels: Object.keys(departmentEmployeeTotals),
        datasets: [
            {
                label: 'Empleados por Departamento',
                backgroundColor: ['#3B82F6', '#60A5FA', '#93C5FD'],
                data: Object.values(departmentEmployeeTotals),
            },
        ],
    };
});

// Datos de Departamentos por Ubicación
const departmentLocationData = computed(() => {
    const locationTotals = props.departments.reduce((acc, department) => {
        const location = department.location || 'Desconocida';
        if (!acc[location]) acc[location] = 0;
        acc[location] += 1;
        return acc;
    }, {});

    return {
        labels: Object.keys(locationTotals),
        datasets: [
            {
                label: 'Departamentos por Ubicación',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: Object.values(locationTotals),
            },
        ],
    };
});

// Datos de Comparación de Departamentos por Proyectos
const departmentProjectComparisonData = computed(() => {
    const projectTotals = props.departments.reduce((acc, department) => {
        const projectCount = department.projects ? department.projects.length : 0;
        acc[department.name] = projectCount;
        return acc;
    }, {});

    return {
        labels: Object.keys(projectTotals),
        datasets: [
            {
                label: 'Proyectos por Departamento',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: Object.values(projectTotals),
            },
        ],
    };
});

const projectStatusData = computed(() => {
    const statusTotals = props.projects.reduce((acc, project) => {
        const status = project.status || 'Sin estado';
        acc[status] = (acc[status] || 0) + 1;
        return acc;
    }, {});

    return {
        labels: Object.keys(statusTotals),
        datasets: [
            {
                label: 'Proyectos por Estado',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: Object.values(statusTotals),
            },
        ],
    };
});
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>
