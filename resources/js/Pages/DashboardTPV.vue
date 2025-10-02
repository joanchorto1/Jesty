<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-fuchsia-600 via-purple-600 to-slate-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2">
                        <p class="text-fuchsia-200 text-sm uppercase tracking-widest">Analítica TPV</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Actividad en punto de venta</h1>
                        <p class="text-sm text-fuchsia-200">Monitoriza ventas, tickets y categorías para optimizar tu estrategia comercial.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-5 mt-10">
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-fuchsia-200">Tickets</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalTickets }}</p>
                            <p class="text-sm text-fuchsia-200 mt-3">Ticket medio €{{ averageTicket }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-fuchsia-200">Productos vendidos</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalTicketItems }}</p>
                            <p class="text-sm text-fuchsia-200 mt-3">{{ uniqueProducts }} referencias únicas</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-fuchsia-200">Productos activos</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalProducts }}</p>
                            <p class="text-sm text-fuchsia-200 mt-3">{{ lowStockProducts }} con stock crítico</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-fuchsia-200">Categorías</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalCategories }}</p>
                            <p class="text-sm text-fuchsia-200 mt-3">{{ categoriesWithSales }} con ventas registradas</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-fuchsia-200">Ingresos</p>
                            <p class="text-3xl font-semibold mt-2">€{{ totalRevenue }}</p>
                            <p class="text-sm text-fuchsia-200 mt-3">{{ monthlyRevenueTrend.length }} meses con ventas</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <div class="bg-white rounded-3xl shadow-xl p-6 xl:col-span-2">
                        <h2 class="text-xl font-semibold text-slate-800">Ventas mensuales</h2>
                        <p class="text-sm text-slate-500">Evolución del volumen de tickets y facturación.</p>
                        <div class="mt-6">
                            <LineChart :data="ticketMonthlyData" />
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Distribución por productos</h2>
                        <p class="text-sm text-slate-500">Ventas agrupadas según cada referencia.</p>
                        <div class="mt-6">
                            <PieChart :data="ticketProductData" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Ventas por categoría</h2>
                        <p class="text-sm text-slate-500">Comparativa entre familias de productos.</p>
                        <div class="mt-6">
                            <PieChart :data="productCategoryData" />
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-6 xl:col-span-2">
                        <h2 class="text-xl font-semibold text-slate-800">Top productos vendidos</h2>
                        <p class="text-sm text-slate-500">Ranking de artículos con mayor rotación.</p>
                        <div class="mt-6">
                            <BarChart :data="topSellingProductsData" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <h2 class="text-xl font-semibold text-slate-800">Tendencia de ingresos acumulados</h2>
                    <p class="text-sm text-slate-500">Sigue cómo evolucionan los ingresos en el tiempo.</p>
                    <div class="mt-6">
                        <AreaChart :data="revenueTrendChart" />
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
import AreaChart from '@/Components/AreaChart.vue';

const props = defineProps({
    tickets: Array,
    ticketItems: Array,
    products: Array,
    categories: Array,
});

const totalTickets = computed(() => props.tickets.length);
const totalProducts = computed(() => props.products.length);
const totalCategories = computed(() => props.categories.length);
const totalTicketItems = computed(() => props.ticketItems.length);

const averageTicket = computed(() => {
    if (!props.tickets.length) {
        return '0.00';
    }
    const total = props.tickets.reduce((acc, ticket) => acc + (ticket.total ?? 0), 0);
    return (total / props.tickets.length).toFixed(2);
});

const uniqueProducts = computed(() => new Set(props.ticketItems.map(item => item.product_id)).size);
const lowStockProducts = computed(() => props.products.filter(product => (product.stock ?? 0) <= 10).length);
const categoriesWithSales = computed(() => new Set(props.ticketItems.map(item => {
    const product = props.products.find(p => p.id === item.product_id);
    return product?.category_id;
})).size);

const totalRevenue = computed(() => props.tickets.reduce((acc, ticket) => acc + (ticket.total ?? 0), 0).toFixed(2));

const ticketMonthlyData = computed(() => {
    const monthlyTotals = props.tickets.reduce((acc, ticket) => {
        if (!ticket.date) return acc;
        const month = new Date(ticket.date).toLocaleString('default', { month: 'short' });
        if (!acc[month]) acc[month] = { tickets: 0, revenue: 0 };
        acc[month].tickets += 1;
        acc[month].revenue += ticket.total ?? 0;
        return acc;
    }, {});

    const labels = Object.keys(monthlyTotals);
    return {
        labels,
        datasets: [
            {
                label: 'Tickets',
                backgroundColor: 'rgba(236, 72, 153, 0.35)',
                borderColor: 'rgba(236, 72, 153, 1)',
                borderWidth: 2,
                tension: 0.4,
                data: labels.map(label => monthlyTotals[label].tickets),
                yAxisID: 'y',
            },
            {
                label: 'Ingresos',
                backgroundColor: 'rgba(124, 58, 237, 0.15)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 2,
                tension: 0.4,
                data: labels.map(label => monthlyTotals[label].revenue),
                yAxisID: 'y1',
            },
        ],
    };
});

const ticketProductData = computed(() => {
    const productTotals = props.ticketItems.reduce((acc, item) => {
        const product = props.products.find(p => p.id === item.product_id);
        const name = product ? product.name : `Producto ${item.product_id}`;
        if (!acc[name]) acc[name] = 0;
        acc[name] += item.quantity ?? 1;
        return acc;
    }, {});

    return {
        labels: Object.keys(productTotals),
        datasets: [
            {
                backgroundColor: ['#EC4899', '#6366F1', '#22D3EE', '#FB7185', '#A855F7', '#F97316'],
                data: Object.values(productTotals),
            },
        ],
    };
});

const productCategoryData = computed(() => {
    const categoryTotals = props.products.reduce((acc, product) => {
        const category = props.categories.find(c => c.id === product.category_id);
        const name = category ? category.name : 'Sin categoría';
        if (!acc[name]) acc[name] = 0;
        acc[name] += product.sales ?? product.stock ?? 0;
        return acc;
    }, {});

    return {
        labels: Object.keys(categoryTotals),
        datasets: [
            {
                backgroundColor: ['#C4B5FD', '#F9A8D4', '#FECACA', '#A5F3FC', '#E9D5FF'],
                data: Object.values(categoryTotals),
            },
        ],
    };
});

const topSellingProductsData = computed(() => {
    const productSales = props.products.map(product => ({
        name: product.name,
        sales: product.sales ?? props.ticketItems.filter(item => item.product_id === product.id).reduce((acc, item) => acc + (item.quantity ?? 1), 0),
    })).sort((a, b) => b.sales - a.sales).slice(0, 10);

    return {
        labels: productSales.map(p => p.name),
        datasets: [
            {
                label: 'Ventas',
                backgroundColor: 'rgba(147, 51, 234, 0.45)',
                borderColor: 'rgba(147, 51, 234, 1)',
                borderWidth: 1,
                data: productSales.map(p => p.sales),
            },
        ],
    };
});

const monthlyRevenueTrend = computed(() => {
    const totals = props.tickets.reduce((acc, ticket) => {
        if (!ticket.date) return acc;
        const key = new Date(ticket.date).toISOString().slice(0, 7);
        acc[key] = (acc[key] || 0) + (ticket.total ?? 0);
        return acc;
    }, {});

    return Object.entries(totals).sort(([a], [b]) => a.localeCompare(b));
});

const revenueTrendChart = computed(() => {
    const entries = monthlyRevenueTrend.value;
    return {
        labels: entries.map(([key]) => {
            const [year, month] = key.split('-');
            return new Date(parseInt(year), parseInt(month) - 1).toLocaleString('default', { month: 'short', year: 'numeric' });
        }),
        datasets: [
            {
                label: 'Ingresos acumulados',
                data: entries.reduce((acc, [, value]) => {
                    const previous = acc.length ? acc[acc.length - 1] : 0;
                    acc.push(previous + value);
                    return acc;
                }, []),
                backgroundColor: 'rgba(236, 72, 153, 0.25)',
                borderColor: 'rgba(236, 72, 153, 1)',
                borderWidth: 2,
                fill: true,
            },
        ],
    };
});
</script>
