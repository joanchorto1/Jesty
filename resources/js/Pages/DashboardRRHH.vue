<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-cyan-700 via-blue-700 to-indigo-700 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2">
                        <p class="text-cyan-200 text-sm uppercase tracking-widest">Talento y cultura</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Radiografía del equipo humano</h1>
                        <p class="text-sm text-cyan-200">Analiza la composición, crecimiento y distribución de tu organización.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-cyan-200">Empleados</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalEmployees }}</p>
                            <p class="text-sm text-cyan-200 mt-3">{{ headcountGrowth }}% crecimiento anual</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-cyan-200">Departamentos</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalDepartments }}</p>
                            <p class="text-sm text-cyan-200 mt-3">{{ averageTeamSize }} personas promedio</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-cyan-200">Salario medio</p>
                            <p class="text-3xl font-semibold mt-2">€{{ averageSalary.toFixed(2) }}</p>
                            <p class="text-sm text-cyan-200 mt-3">Brecha interdepartamental {{ salaryVariance }}%</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-cyan-200">Activos</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalEmployeesActive }}</p>
                            <p class="text-sm text-cyan-200 mt-3">{{ inactiveEmployees }} en pausa</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Empleados por departamento</h2>
                        <p class="text-sm text-slate-500">Balancea cargas de trabajo y dimensiona cada área.</p>
                        <div class="mt-6">
                            <PieChart :data="employeeDepartmentData" />
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Contrataciones por año</h2>
                        <p class="text-sm text-slate-500">Evalúa el ritmo de incorporación de talento.</p>
                        <div class="mt-6">
                            <BarChart :data="employeeHiringYearData" />
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Salario medio por departamento</h2>
                        <p class="text-sm text-slate-500">Identifica áreas con mayor inversión salarial.</p>
                        <div class="mt-6">
                            <BarChart :data="departmentSalaryComparisonData" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Distribución de talento</h2>
                        <p class="text-sm text-slate-500">Relación de headcount por áreas funcionales.</p>
                        <div class="mt-6">
                            <PieChart :data="departmentEmployeeData" />
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Departamentos por ubicación</h2>
                        <p class="text-sm text-slate-500">Comprende la huella geográfica del equipo.</p>
                        <div class="mt-6">
                            <BarChart :data="departmentLocationData" />
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Proyectos activos por departamento</h2>
                        <p class="text-sm text-slate-500">Detecta áreas con mayor carga operativa.</p>
                        <div class="mt-6">
                            <BarChart :data="departmentProjectComparisonData" />
                        </div>
                    </div>
                </div>
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
});

const totalEmployees = computed(() => props.employees.length);
const totalDepartments = computed(() => props.departments.length);
const totalEmployeesActive = computed(() => props.employees.filter(employee => employee.status === 'active').length);
const inactiveEmployees = computed(() => props.employees.filter(employee => employee.status !== 'active').length);
const averageSalary = computed(() => {
    if (!props.employees.length) {
        return 0;
    }
    const totalSalary = props.employees.reduce((acc, employee) => acc + (employee.salary ?? 0), 0);
    return totalSalary / props.employees.length;
});

const headcountGrowth = computed(() => {
    const currentYear = new Date().getFullYear();
    const previousYearCount = props.employees.filter(employee => new Date(employee.hire_date).getFullYear() === currentYear - 1).length;
    if (!previousYearCount) {
        return 100;
    }
    const currentYearCount = props.employees.filter(employee => new Date(employee.hire_date).getFullYear() === currentYear).length;
    return Math.round(((currentYearCount - previousYearCount) / previousYearCount) * 100);
});

const averageTeamSize = computed(() => {
    if (!props.departments.length) {
        return 0;
    }
    return Math.round(totalEmployees.value / props.departments.length);
});

const salaryVariance = computed(() => {
    if (!props.departments.length) {
        return 0;
    }
    const salaries = props.departments.map(department => {
        const departmentEmployees = props.employees.filter(employee => employee.department_id === department.id);
        if (!departmentEmployees.length) return 0;
        const total = departmentEmployees.reduce((acc, employee) => acc + (employee.salary ?? 0), 0);
        return total / departmentEmployees.length;
    }).filter(Boolean);

    if (!salaries.length) {
        return 0;
    }
    const maxSalary = Math.max(...salaries);
    const minSalary = Math.min(...salaries);
    return Math.round(((maxSalary - minSalary) / maxSalary) * 100);
});

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
                label: 'Empleados por departamento',
                backgroundColor: ['#06B6D4', '#2563EB', '#7C3AED', '#14B8A6', '#EC4899'],
                data: Object.values(departmentTotals),
            },
        ],
    };
});

const employeeHiringYearData = computed(() => {
    const hiringYearTotals = props.employees.reduce((acc, employee) => {
        if (!employee.hire_date) return acc;
        const year = new Date(employee.hire_date).getFullYear();
        if (!acc[year]) acc[year] = 0;
        acc[year] += 1;
        return acc;
    }, {});

    return {
        labels: Object.keys(hiringYearTotals),
        datasets: [
            {
                label: 'Altas',
                backgroundColor: 'rgba(14, 165, 233, 0.5)',
                borderColor: 'rgba(14, 165, 233, 1)',
                borderWidth: 1,
                data: Object.values(hiringYearTotals),
            },
        ],
    };
});

const departmentSalaryComparisonData = computed(() => {
    const departmentSalaryTotals = props.departments.reduce((acc, department) => {
        const departmentEmployees = props.employees.filter(employee => employee.department_id === department.id);
        const totalSalary = departmentEmployees.reduce((total, employee) => total + (employee.salary ?? 0), 0);
        const avgSalary = departmentEmployees.length > 0 ? totalSalary / departmentEmployees.length : 0;
        acc[department.name] = avgSalary;
        return acc;
    }, {});

    return {
        labels: Object.keys(departmentSalaryTotals),
        datasets: [
            {
                label: 'Salario medio',
                backgroundColor: 'rgba(37, 99, 235, 0.45)',
                borderColor: 'rgba(37, 99, 235, 1)',
                borderWidth: 1,
                data: Object.values(departmentSalaryTotals),
            },
        ],
    };
});

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
                label: 'Empleados',
                backgroundColor: ['#06B6D4', '#2563EB', '#7C3AED', '#14B8A6', '#EC4899'],
                data: Object.values(departmentEmployeeTotals),
            },
        ],
    };
});

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
                label: 'Departamentos',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: Object.values(locationTotals),
            },
        ],
    };
});

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
                label: 'Proyectos activos',
                backgroundColor: 'rgba(129, 140, 248, 0.45)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 1,
                data: Object.values(projectTotals),
            },
        ],
    };
});
</script>
