<template>
    <AppLayout>
        <div class="w-full  min-h-screen bg-gray-100 p-10">
            <!-- Sección de Widgets Globales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white shadow-md p-4 rounded-lg">
                    <p class="text-blue-300 text-sm">Total de Presupuestos</p>
                    <p class="text-xl font-bold text-blue-500">{{ totalBudgets }}</p>
                </div>
                <div class="bg-white shadow-md p-4 rounded-lg">
                    <p class="text-blue-300 text-sm">Total de Facturas</p>
                    <p class="text-xl font-bold text-blue-500">{{ totalInvoices }}</p>
                </div>
                <div class="bg-white shadow-md p-4 rounded-lg">
                    <p class="text-blue-300 text-sm">Total de Clientes</p>
                    <p class="text-xl font-bold text-blue-500">{{ totalClients }}</p>
                </div>
                <div class="bg-white shadow-md p-4 rounded-lg">
                    <p class="text-blue-300 text-sm">Ingresos Totales (€)</p>
                    <p class="text-xl font-bold text-blue-500">{{ totalIncome }}</p>
                </div>
            </div>

            <!-- Sección de Gráficos -->
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Análisis de Datos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Distribución de Presupuestos por Clientes</h3>
                    <PieChart :data="budgetClientData" class="h-48" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Facturas Mensuales (€)</h3>
                    <BarChart :data="invoiceMonthlyData" class="h-48" />
                </div>
            </div>

            <!-- Listas de Presupuestos y Facturas Recientes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="bg-white p-4 rounded-lg shadow-md flex flex-col">                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Presupuestos Recientes</h3>
                    <ul class="divide-y divide-gray-200">
                        <li v-for="budget in recentBudgets" :key="budget.id" class="py-2 flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-700">Cliente: {{ getClientName(budget.client_id) }}</p>
                                <p class="text-xs text-gray-500">Fecha: {{ new Date(budget.date).toLocaleDateString() }}</p>
                            </div>
                            <p class="text-sm font-semibold text-blue-500">€{{ budget.total.toFixed(2) }}</p>
                        </li>
                    </ul>
                    <div class="mt-4  text-right">
                        <a href="/budgets" class="text-blue-500 text-sm font-semibold">Ver todos</a>
                    </div>
                </div>

<div class="bg-white p-4 rounded-lg shadow-md flex flex-col">                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Facturas Recientes</h3>
                    <ul class="divide-y divide-gray-200">
                        <li v-for="invoice in recentInvoices" :key="invoice.id" class="py-2 flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-700">Cliente: {{ getClientName(invoice.client_id) }}</p>
                                <p class="text-xs text-gray-500">Fecha: {{ new Date(invoice.date).toLocaleDateString() }}</p>
                            </div>
                            <p class="text-sm font-semibold text-blue-500">€{{ invoice.total.toFixed(2) }}</p>
                        </li>
                    </ul>
                    <div class="mt-4 text-right">
                        <a href="/invoices" class="text-blue-500 text-sm font-semibold">Ver todos</a>
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
    budgets: Array,
    invoices: Array,
    clients: Array,
});

// Cálculos globales
const totalBudgets = computed(() => props.budgets.length);
const totalInvoices = computed(() => props.invoices.length);
const totalClients = computed(() => props.clients.length);
const totalIncome = computed(() => props.invoices.reduce((acc, invoice) => acc + invoice.total, 0).toFixed(2));

// Datos recientes
const recentBudgets = computed(() => props.budgets.slice(-5).reverse());
const recentInvoices = computed(() => props.invoices.slice(-5).reverse());

// Métodos auxiliares
const getClientName = (clientId) => {
    const client = props.clients.find(c => c.id === clientId);
    return client ? client.name : 'Cliente Desconocido';
};

// Datos de gráficos
const budgetClientData = computed(() => {
    const clientTotals = props.budgets.reduce((acc, budget) => {
        if (!acc[budget.client_id]) acc[budget.client_id] = 0;
        acc[budget.client_id] += budget.total;
        return acc;
    }, {});

    return {
        labels: Object.keys(clientTotals).map(clientId => getClientName(parseInt(clientId))),
        datasets: [
            {
                label: 'Presupuestos por Cliente',
                backgroundColor: ['#3B82F6', '#60A5FA', '#93C5FD'],
                data: Object.values(clientTotals),
            },
        ],
    };
});

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
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>
