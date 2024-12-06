<template>
    <AppLayout>
        <div class="w-full bg-gray-100 p-10">
            <!-- Sección de Widgets Globales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalBudgets }}</p>
                    <p class="text-blue-700 font-semibold">Total de Presupuestos</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalInvoices }}</p>
                    <p class="text-blue-700 font-semibold">Total de Facturas</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalClients }}</p>
                    <p class="text-blue-700 font-semibold">Total de Clientes</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalIncome }}</p>
                    <p class="text-blue-700 font-semibold">Ingresos Totales</p>
                </div>
            </div>

            <!-- Sección de Gráficos de Presupuestos -->
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Gráficos de Presupuestos</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Distribución de Presupuestos por Clientes</h3>
                    <PieChart :data="budgetClientData" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Presupuestos Mensuales</h3>
                    <BarChart :data="budgetData" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Comparación Anual de Presupuestos</h3>
                    <!-- Aquí podrías agregar un nuevo gráfico si tienes los datos anuales -->
                    <BarChart :data="budgetYearlyComparisonData" />
                </div>
            </div>

            <!-- Sección de Gráficos de Facturas -->
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Gráficos de Facturas</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Distribución de Facturas por Clientes</h3>
                    <PieChart :data="invoiceClientData" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Facturas Mensuales</h3>
                    <BarChart :data="invoiceMonthlyData" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Comparación Anual de Facturas</h3>
                    <!-- Aquí podrías agregar un nuevo gráfico si tienes los datos anuales -->
                    <BarChart :data="invoiceYearlyComparisonData" />
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
    budgets: Array,
    invoices: Array,
    clients: Array,
});

// Cálculos globales
const totalBudgets = computed(() => props.budgets.length);
const totalInvoices = computed(() => props.invoices.length);
const totalClients = computed(() => props.clients.length);
const totalIncome = computed(() => props.invoices.reduce((acc, invoice) => acc + invoice.total, 0));

// Datos de Presupuestos
const budgetData = computed(() => {
    const monthlyTotals = props.budgets.reduce((acc, budget) => {
        const month = new Date(budget.date).toLocaleString('default', { month: 'short' });
        if (!acc[month]) acc[month] = 0;
        acc[month] += budget.total;
        return acc;
    }, {});

    return {
        labels: Object.keys(monthlyTotals),
        datasets: [
            {
                label: 'Presupuestos',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: Object.values(monthlyTotals),
            },
        ],
    };
});

// Distribución de Presupuestos por Cliente
const budgetClientData = computed(() => {
    const clientTotals = props.budgets.reduce((acc, budget) => {
        if (!acc[budget.client_id]) acc[budget.client_id] = 0;
        acc[budget.client_id] += budget.total;
        return acc;
    }, {});

    return {
        labels: Object.keys(clientTotals).map(clientId => {
            const client = props.clients.find(c => c.id === parseInt(clientId));
            return client ? client.name : 'Cliente Desconocido';
        }),
        datasets: [
            {
                label: 'Presupuestos por Cliente',
                backgroundColor: ['#3B82F6', '#60A5FA', '#93C5FD'],
                data: Object.values(clientTotals),
            },
        ],
    };
});

// Datos de Facturas
const invoiceMonthlyData = computed(() => {
    const monthlyTotals = props.invoices.reduce((acc, invoice) => {
        const month = new Date(invoice.date).toLocaleString('default', { month: 'short' });
        if (!acc[month]) acc[month] = 0;
        acc[month] += invoice.total;
        return acc;
    }, {});

    return {
        labels: Object.keys(monthlyTotals),
        datasets: [
            {
                label: 'Facturas',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: Object.values(monthlyTotals),
            },
        ],
    };
});

// Distribución de Facturas por Cliente
const invoiceClientData = computed(() => {
    const clientTotals = props.invoices.reduce((acc, invoice) => {
        if (!acc[invoice.client_id]) acc[invoice.client_id] = 0;
        acc[invoice.client_id] += invoice.total;
        return acc;
    }, {});

    return {
        labels: Object.keys(clientTotals).map(clientId => {
            const client = props.clients.find(c => c.id === parseInt(clientId));
            return client ? client.name : 'Cliente Desconocido';
        }),
        datasets: [
            {
                label: 'Facturas por Cliente',
                backgroundColor: ['#3B82F6', '#60A5FA', '#93C5FD'],
                data: Object.values(clientTotals),
            },
        ],
    };
});
const invoiceYearlyComparisonData = computed(() => {
    const yearlyTotals = props.invoices.reduce((acc, invoice) => {
        const year = new Date(invoice.date).getFullYear();
        if (!acc[year]) acc[year] = 0;
        acc[year] += invoice.total;
        return acc;
    }, {});

    return {
        labels: Object.keys(yearlyTotals),
        datasets: [
            {
                label: 'Facturas',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: Object.values(yearlyTotals),
            },
        ],
    };
});

// Comparación Anual de Presupuestos

const budgetYearlyComparisonData = computed(() => {
    const yearlyTotals = props.budgets.reduce((acc, budget) => {
        const year = new Date(budget.date).getFullYear();
        if (!acc[year]) acc[year] = 0;
        acc[year] += budget.total;
        return acc;
    }, {});

    return {
        labels: Object.keys(yearlyTotals),
        datasets: [
            {
                label: 'Presupuestos',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: Object.values(yearlyTotals),
            },
        ],
    };
});
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>
