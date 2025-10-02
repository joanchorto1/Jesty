<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-indigo-700 via-sky-700 to-emerald-700 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2">
                        <p class="text-sky-200 text-sm uppercase tracking-widest">Performance de facturación</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Control total de presupuestos e ingresos</h1>
                        <p class="text-sm text-sky-200">Identifica tendencias, clientes clave y oportunidades para acelerar los cobros.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-5 mt-10">
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg xl:col-span-1">
                            <p class="text-xs uppercase tracking-[0.3em] text-sky-200">Presupuestos</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalBudgets }}</p>
                            <p class="text-sm text-sky-200 mt-3">Importe medio €{{ averageBudget }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg xl:col-span-1">
                            <p class="text-xs uppercase tracking-[0.3em] text-sky-200">Facturas</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalInvoices }}</p>
                            <p class="text-sm text-sky-200 mt-3">Ticket medio €{{ averageInvoice }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg xl:col-span-1">
                            <p class="text-xs uppercase tracking-[0.3em] text-sky-200">Clientes</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalClients }}</p>
                            <p class="text-sm text-sky-200 mt-3">Top cliente {{ topClientName }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg xl:col-span-1">
                            <p class="text-xs uppercase tracking-[0.3em] text-sky-200">Cobros pendientes</p>
                            <p class="text-3xl font-semibold mt-2">€{{ outstandingAmount }}</p>
                            <p class="text-sm text-sky-200 mt-3">{{ outstandingInvoices }} facturas en trámite</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg xl:col-span-1">
                            <p class="text-xs uppercase tracking-[0.3em] text-sky-200">Cobrado</p>
                            <p class="text-3xl font-semibold mt-2">€{{ totalIncome }}</p>
                            <p class="text-sm text-sky-200 mt-3">{{ paidPercentage }}% facturas cobradas</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Distribución de presupuestos</h2>
                        <p class="text-sm text-slate-500">Mira qué clientes concentran mayor inversión estimada.</p>
                        <div class="mt-6">
                            <PieChart :data="budgetClientData" class="h-48" />
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Ingresos mensuales</h2>
                        <p class="text-sm text-slate-500">Evolución de la facturación cobrada a lo largo del año.</p>
                        <div class="mt-6">
                            <LineChart :data="invoiceMonthlyData" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <div class="bg-white rounded-3xl shadow-xl p-6 xl:col-span-2">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 pb-5">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-800">Presupuestos recientes</h2>
                                <p class="text-sm text-slate-500">Últimas propuestas emitidas y su valor estimado.</p>
                            </div>
                            <NavLink href="/budgets" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Ver todos</NavLink>
                        </div>
                        <ul class="divide-y divide-slate-100 text-sm text-slate-600">
                            <li v-for="budget in recentBudgets" :key="budget.id" class="flex items-center justify-between py-4">
                                <div>
                                    <p class="font-semibold text-slate-700">{{ getClientName(budget.client_id) }}</p>
                                    <p class="text-xs text-slate-400">{{ formatDate(budget.date) }}</p>
                                </div>
                                <p class="font-semibold text-slate-800">€{{ (budget.total ?? 0).toFixed(2) }}</p>
                            </li>
                            <li v-if="recentBudgets.length === 0" class="py-4 text-center text-slate-400">No hay presupuestos registrados.</li>
                        </ul>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Estado de facturas</h2>
                        <p class="text-sm text-slate-500">Controla qué porcentaje está cobrado, pendiente o vencido.</p>
                        <div class="mt-6">
                            <DoughnutChart :data="invoiceStatusChart" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <div class="bg-white rounded-3xl shadow-xl p-6 xl:col-span-2">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 pb-5">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-800">Facturas recientes</h2>
                                <p class="text-sm text-slate-500">Seguimiento de cobros generados en las últimas semanas.</p>
                            </div>
                            <NavLink href="/invoices" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Ver todos</NavLink>
                        </div>
                        <ul class="divide-y divide-slate-100 text-sm text-slate-600">
                            <li v-for="invoice in recentInvoices" :key="invoice.id" class="flex items-center justify-between py-4">
                                <div>
                                    <p class="font-semibold text-slate-700">{{ getClientName(invoice.client_id) }}</p>
                                    <p class="text-xs text-slate-400">{{ formatDate(invoice.date) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-slate-800">€{{ (invoice.total ?? 0).toFixed(2) }}</p>
                                    <p class="text-xs text-slate-400 uppercase tracking-widest">{{ invoice.state ?? 'pending' }}</p>
                                </div>
                            </li>
                            <li v-if="recentInvoices.length === 0" class="py-4 text-center text-slate-400">No hay facturas registradas.</li>
                        </ul>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Comparativa presupuestos vs facturación</h2>
                        <p class="text-sm text-slate-500">Evalúa la conversión de propuestas en ventas efectivas.</p>
                        <div class="mt-6">
                            <BarChart :data="budgetVsInvoiceChart" />
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
import LineChart from '@/Components/LineChart.vue';
import DoughnutChart from '@/Components/DoughnutChart.vue';
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    budgets: Array,
    invoices: Array,
    clients: Array,
});

const totalBudgets = computed(() => props.budgets.length);
const totalInvoices = computed(() => props.invoices.length);
const totalClients = computed(() => props.clients.length);
const totalIncome = computed(() => props.invoices.reduce((acc, invoice) => acc + (invoice.total ?? 0), 0).toFixed(2));
const outstandingAmount = computed(() => props.invoices
    .filter(invoice => invoice.state !== 'paid')
    .reduce((acc, invoice) => acc + (invoice.total ?? 0), 0)
    .toFixed(2));
const outstandingInvoices = computed(() => props.invoices.filter(invoice => invoice.state !== 'paid').length);
const paidPercentage = computed(() => {
    if (!props.invoices.length) {
        return 0;
    }
    const paid = props.invoices.filter(invoice => invoice.state === 'paid').length;
    return Math.round((paid / props.invoices.length) * 100);
});

const averageBudget = computed(() => props.budgets.length ? (props.budgets.reduce((acc, budget) => acc + (budget.total ?? 0), 0) / props.budgets.length).toFixed(2) : '0.00');
const averageInvoice = computed(() => props.invoices.length ? (props.invoices.reduce((acc, invoice) => acc + (invoice.total ?? 0), 0) / props.invoices.length).toFixed(2) : '0.00');

const recentBudgets = computed(() => [...props.budgets].sort((a, b) => new Date(b.date) - new Date(a.date)).slice(0, 5));
const recentInvoices = computed(() => [...props.invoices].sort((a, b) => new Date(b.date) - new Date(a.date)).slice(0, 5));

const getClientName = (clientId) => {
    const client = props.clients.find(c => c.id === clientId);
    return client ? client.name : 'Cliente desconocido';
};

const formatDate = (date) => {
    return date ? new Date(date).toLocaleDateString() : '—';
};

const budgetClientData = computed(() => {
    const clientTotals = props.budgets.reduce((acc, budget) => {
        if (!acc[budget.client_id]) acc[budget.client_id] = 0;
        acc[budget.client_id] += budget.total ?? 0;
        return acc;
    }, {});

    return {
        labels: Object.keys(clientTotals).map(clientId => getClientName(parseInt(clientId))),
        datasets: [
            {
                label: 'Presupuestos',
                backgroundColor: ['#38BDF8', '#22D3EE', '#818CF8', '#C4B5FD', '#FECACA'],
                data: Object.values(clientTotals),
            },
        ],
    };
});

const invoiceMonthlyData = computed(() => {
    const monthlyTotals = props.invoices.reduce((acc, invoice) => {
        if (!invoice.date) return acc;
        const month = new Date(invoice.date).toLocaleString('default', { month: 'short' });
        acc[month] = (acc[month] || 0) + (invoice.total ?? 0);
        return acc;
    }, {});

    return {
        labels: Object.keys(monthlyTotals),
        datasets: [
            {
                label: 'Facturación',
                backgroundColor: 'rgba(59, 130, 246, 0.35)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                data: Object.values(monthlyTotals),
            },
        ],
    };
});

const invoiceStatusChart = computed(() => {
    const statuses = props.invoices.reduce((acc, invoice) => {
        const status = invoice.state ?? 'pending';
        acc[status] = (acc[status] || 0) + 1;
        return acc;
    }, {});

    return {
        labels: Object.keys(statuses),
        datasets: [
            {
                backgroundColor: ['#34D399', '#FBBF24', '#F87171', '#A5B4FC'],
                data: Object.values(statuses),
            },
        ],
    };
});

const budgetVsInvoiceChart = computed(() => {
    const months = new Set();
    props.budgets.forEach(budget => { if (budget.date) months.add(new Date(budget.date).getMonth()); });
    props.invoices.forEach(invoice => { if (invoice.date) months.add(new Date(invoice.date).getMonth()); });

    const sortedMonths = [...months].sort((a, b) => a - b);
    const labels = sortedMonths.map(month => new Date(2024, month).toLocaleString('default', { month: 'short' }));

    const budgetTotals = Array(sortedMonths.length).fill(0);
    const invoiceTotals = Array(sortedMonths.length).fill(0);

    props.budgets.forEach(budget => {
        if (!budget.date) return;
        const monthIndex = sortedMonths.indexOf(new Date(budget.date).getMonth());
        budgetTotals[monthIndex] += budget.total ?? 0;
    });

    props.invoices.forEach(invoice => {
        if (!invoice.date) return;
        const monthIndex = sortedMonths.indexOf(new Date(invoice.date).getMonth());
        invoiceTotals[monthIndex] += invoice.total ?? 0;
    });

    return {
        labels,
        datasets: [
            {
                label: 'Presupuestos',
                backgroundColor: 'rgba(129, 140, 248, 0.6)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 1,
                data: budgetTotals,
            },
            {
                label: 'Facturación',
                backgroundColor: 'rgba(20, 184, 166, 0.6)',
                borderColor: 'rgba(13, 148, 136, 1)',
                borderWidth: 1,
                data: invoiceTotals,
            },
        ],
    };
});

const topClientName = computed(() => {
    const totals = props.invoices.reduce((acc, invoice) => {
        const clientId = invoice.client_id;
        acc[clientId] = (acc[clientId] || 0) + (invoice.total ?? 0);
        return acc;
    }, {});

    const best = Object.entries(totals).sort((a, b) => b[1] - a[1])[0];
    if (!best) return '—';
    return getClientName(Number(best[0]));
});
</script>
