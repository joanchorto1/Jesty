<template>
    <AppLayout>
        <div class="w-full bg-gray-100 p-10">
            <!-- Sección de Widgets Globales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalIncomes }}</p>
                    <p class="text-blue-700 font-semibold">Ingresos Totales</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalExpenses }}</p>
                    <p class="text-blue-700 font-semibold">Total de Gastos</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ profit }}</p>
                    <p class="text-blue-700 font-semibold">Ganancias Netas</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalTransactions }}</p>
                    <p class="text-blue-700 font-semibold">Transacciones Totales</p>
                </div>
            </div>

            <!-- Selección del Año -->
            <div class="bg-white p-6 rounded-lg shadow mb-8">
                <h2 class="text-xl font-semibold text-blue-700 mb-4">Ingresos Acumulados</h2>
                <div class="mb-4">
                    <label for="year" class="text-blue-500 mr-2">Seleccionar Año:</label>
                    <select id="year" v-model="selectedYear" class="py-2 px-5 pr-5 border rounded">
                        <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                    </select>
                </div>
                <BarChart :data="cumulativeIncomeData" />
            </div>

            <!-- Sección de Gráficos adicionales -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Comparación de Métricas</h3>
                    <LineChart :data="metricsComparisonData" />
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Distribución de Gastos Mensuales</h3>
                    <BarChart :data="monthlyExpensesData" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BarChart from '@/Components/BarChart.vue';
import LineChart from '@/Components/LineChart.vue';

const props = defineProps({
    incomes: Array,
    expenses: Array,
    clients: Array,
});

// Estado para el año seleccionado
const selectedYear = ref(new Date().getFullYear());

// Función para obtener todos los años disponibles en ingresos y gastos
const availableYears = computed(() => {
    const yearsFromIncomes = props.incomes.map(income => new Date(income.date).getFullYear());
    const yearsFromExpenses = props.expenses.map(expense => new Date(expense.date).getFullYear());
    return [...new Set([...yearsFromIncomes, ...yearsFromExpenses])].sort((a, b) => b - a);
});

// Cálculos de widgets
const totalIncomes = computed(() => props.incomes.reduce((acc, income) => acc + income.total_amount, 0));
const totalExpenses = computed(() => props.expenses.reduce((acc, expense) => acc + expense.amount, 0));
const profit = computed(() => totalIncomes.value - totalExpenses.value);
const totalTransactions = computed(() => props.incomes.length + props.expenses.length);

// Datos acumulados de ingresos para el gráfico de barras
const cumulativeIncomeData = computed(() => {
    const dataByMonth = Array(12).fill(0);

    props.incomes
        .filter(income => new Date(income.date).getFullYear() === selectedYear.value)
        .forEach(income => {
            const monthIndex = new Date(income.date).getMonth();
            dataByMonth[monthIndex] += income.total_amount;
        });

    return {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [
            {
                label: 'Ingresos Acumulados',
                data: dataByMonth,
                backgroundColor: 'rgba(34, 197, 94, 0.3)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 1,
            },
        ],
    };
});

// Comparación de métricas para el gráfico de línea
const metricsComparisonData = computed(() => {
    return {
        labels: ['Ingresos', 'Gastos', 'Ganancias', 'Transacciones'],
        datasets: [
            {
                label: 'Métricas',
                data: [totalIncomes.value, totalExpenses.value, profit.value, totalTransactions.value],
                fill: false,
                borderColor: 'rgba(59, 130, 246, 1)',
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderWidth: 2,
            },
        ],
    };
});

// Datos de gastos mensuales
const monthlyExpensesData = computed(() => {
    const monthlyTotals = props.expenses
        .filter(expense => new Date(expense.date).getFullYear() === selectedYear.value)
        .reduce((acc, expense) => {
            const monthIndex = new Date(expense.date).getMonth();
            if (!acc[monthIndex]) acc[monthIndex] = 0;
            acc[monthIndex] += expense.amount;
            return acc;
        }, Array(12).fill(0));

    return {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [
            {
                label: 'Gastos Mensuales',
                backgroundColor: 'rgba(239, 68, 68, 0.5)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1,
                data: monthlyTotals,
            },
        ],
    };
});
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>
