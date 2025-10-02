<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-fuchsia-600 via-purple-600 to-slate-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2 text-white">
                        <p class="uppercase tracking-[0.3em] text-fuchsia-200 text-xs">Informes de venda</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold">Anàlisi de productes</h1>
                        <p class="text-sm text-fuchsia-200 max-w-2xl">
                            Explora el rendiment de cada referència, genera comparatives i detecta oportunitats des d'una vista unificada.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-10">
                        <KpiCard
                            v-for="card in kpiCards"
                            :key="card.key"
                            gradient
                            :subtitle="card.subtitle"
                            :value="card.value"
                            :description="card.description"
                        />
                    </div>
                </div>
            </div>

            <main class="max-w-7xl mx-auto px-6 -mt-16 pb-20 space-y-10">
                <section class="bg-white rounded-3xl shadow-xl p-6">
                    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Filtres temporals</h2>
                            <p class="text-sm text-slate-500">Acota l'informe al període que vulguis analitzar.</p>
                        </div>
                        <p class="text-xs font-medium uppercase tracking-[0.3em] text-slate-400">Informació cohesionada</p>
                    </header>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                        <label class="flex flex-col gap-2">
                            <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">Data d'inici</span>
                            <input
                                v-model="startDate"
                                type="date"
                                class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-fuchsia-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
                            />
                        </label>
                        <label class="flex flex-col gap-2">
                            <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">Data de finalització</span>
                            <input
                                v-model="endDate"
                                type="date"
                                class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-fuchsia-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
                            />
                        </label>
                        <div class="flex items-end">
                            <button
                                class="w-full rounded-xl bg-gradient-to-r from-fuchsia-500 to-purple-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-purple-500/20 transition hover:from-fuchsia-400 hover:to-purple-500"
                                @click="applyFilters"
                            >
                                Aplicar
                            </button>
                        </div>
                        <div class="flex items-end">
                            <button
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-fuchsia-300 hover:text-fuchsia-500"
                                @click="clearFilters"
                            >
                                Netejar
                            </button>
                        </div>
                        <div class="flex items-end">
                            <select
                                v-model="sortBy"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-fuchsia-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
                            >
                                <option value="default">Ordre natural</option>
                                <option value="quantitySold">Quantitat venuda</option>
                                <option value="totalSales">Ingressos</option>
                            </select>
                        </div>
                    </div>
                </section>

                <section class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <article class="bg-white rounded-3xl shadow-xl p-6 xl:col-span-2">
                        <header>
                            <h2 class="text-xl font-semibold text-slate-800">Evolució de vendes</h2>
                            <p class="text-sm text-slate-500">Seguiment temporal de quantitats i facturació.</p>
                        </header>
                        <div class="mt-6">
                            <LineChart :data="salesTimeline" />
                        </div>
                    </article>
                    <article class="bg-white rounded-3xl shadow-xl p-6">
                        <header>
                            <h2 class="text-xl font-semibold text-slate-800">Top productes</h2>
                            <p class="text-sm text-slate-500">Classificació dels articles amb millor rendiment.</p>
                        </header>
                        <div class="mt-6">
                            <BarChart :data="topProductsChart" />
                        </div>
                    </article>
                </section>

                <section class="bg-white rounded-3xl shadow-xl p-6">
                    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Informe detallat</h2>
                            <p class="text-sm text-slate-500">Analitza cada producte amb mètriques clau per a la decisió.</p>
                        </div>
                        <p class="text-xs font-medium uppercase tracking-[0.3em] text-slate-400">Resum i gràfiques integrades</p>
                    </header>
                    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-100">
                        <table class="min-w-full divide-y divide-slate-100 text-sm text-slate-600">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="px-6 py-3 text-left">Producte</th>
                                    <th class="px-6 py-3 text-left">Quantitat venuda</th>
                                    <th class="px-6 py-3 text-left">Ingressos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in sortedProducts" :key="product.id" class="divide-y divide-slate-100">
                                    <td class="px-6 py-4 font-semibold text-slate-800">{{ product.name }}</td>
                                    <td class="px-6 py-4">{{ product.quantitySold }}</td>
                                    <td class="px-6 py-4">{{ formatCurrency(product.totalSales) }}</td>
                                </tr>
                                <tr v-if="!sortedProducts.length">
                                    <td class="px-6 py-10 text-center text-slate-400" colspan="3">No hi ha dades per al període seleccionat.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import KpiCard from '@/Components/KpiCard.vue';
import LineChart from '@/Components/LineChart.vue';
import BarChart from '@/Components/BarChart.vue';

const props = defineProps({
    tickets: Array,
    ticketItems: Array,
    products: Array,
});

const startDate = ref('');
const endDate = ref('');
const appliedRange = reactive({ start: '', end: '' });
const sortBy = ref('default');

const applyFilters = () => {
    appliedRange.start = startDate.value;
    appliedRange.end = endDate.value;
};

const clearFilters = () => {
    startDate.value = '';
    endDate.value = '';
    appliedRange.start = '';
    appliedRange.end = '';
};

const filteredTicketItems = computed(() => {
    return props.ticketItems.filter(item => {
        const ticket = props.tickets.find(t => t.id === item.tiket_id);
        if (!ticket) {
            return false;
        }
        if (!appliedRange.start && !appliedRange.end) {
            return true;
        }
        const ticketDate = new Date(ticket.created_at);
        const hasStart = Boolean(appliedRange.start);
        const hasEnd = Boolean(appliedRange.end);
        const start = hasStart ? new Date(appliedRange.start) : null;
        const end = hasEnd ? new Date(appliedRange.end) : null;
        const afterStart = !hasStart || ticketDate >= start;
        const beforeEnd = !hasEnd || ticketDate <= end;
        return afterStart && beforeEnd;
    });
});

const productMap = computed(() => {
    const map = new Map();
    filteredTicketItems.value.forEach(item => {
        const product = props.products.find(prod => prod.id === item.product_id);
        if (!product) return;
        if (!map.has(product.id)) {
            map.set(product.id, {
                id: product.id,
                name: product.name,
                quantitySold: 0,
                totalSales: 0,
            });
        }
        const entry = map.get(product.id);
        entry.quantitySold += item.quantity ?? 0;
        entry.totalSales += (item.quantity ?? 0) * (item.unit_price ?? 0);
    });
    return Array.from(map.values());
});

const totalQuantity = computed(() => productMap.value.reduce((acc, item) => acc + item.quantitySold, 0));
const totalSales = computed(() => productMap.value.reduce((acc, item) => acc + item.totalSales, 0));
const topProduct = computed(() => productMap.value.slice().sort((a, b) => b.totalSales - a.totalSales)[0] ?? null);
const averageTicketValue = computed(() => {
    if (!filteredTicketItems.value.length) {
        return 0;
    }
    const ticketIds = new Set(filteredTicketItems.value.map(item => item.tiket_id));
    return totalSales.value / ticketIds.size;
});

const formatCurrency = value => new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(Number(value) || 0);

const kpiCards = computed(() => [
    {
        key: 'quantity',
        subtitle: 'Unitats venudes',
        value: totalQuantity.value,
        description: 'Total d\'articles associats al rang seleccionat.',
    },
    {
        key: 'revenue',
        subtitle: 'Ingressos totals',
        value: formatCurrency(totalSales.value),
        description: 'Facturació acumulada per producte.',
    },
    {
        key: 'average-ticket',
        subtitle: 'Ticket mitjà',
        value: formatCurrency(averageTicketValue.value),
        description: 'Valor mig per transacció registrada.',
    },
    {
        key: 'top-product',
        subtitle: 'Millor producte',
        value: topProduct.value ? topProduct.value.name : 'Sense dades',
        description: 'Article amb més ingressos generats.',
    },
]);

const salesTimeline = computed(() => {
    const grouped = filteredTicketItems.value.reduce((acc, item) => {
        const ticket = props.tickets.find(t => t.id === item.tiket_id);
        if (!ticket) return acc;
        const key = new Date(ticket.created_at).toISOString().split('T')[0];
        if (!acc[key]) {
            acc[key] = { quantity: 0, revenue: 0 };
        }
        acc[key].quantity += item.quantity ?? 0;
        acc[key].revenue += (item.quantity ?? 0) * (item.unit_price ?? 0);
        return acc;
    }, {});

    const labels = Object.keys(grouped).sort();
    return {
        labels,
        datasets: [
            {
                label: 'Unitats',
                backgroundColor: 'rgba(236, 72, 153, 0.2)',
                borderColor: 'rgba(236, 72, 153, 1)',
                data: labels.map(label => grouped[label].quantity),
                borderWidth: 2,
                tension: 0.4,
                yAxisID: 'y',
            },
            {
                label: 'Ingressos',
                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                borderColor: 'rgba(99, 102, 241, 1)',
                data: labels.map(label => grouped[label].revenue),
                borderWidth: 2,
                tension: 0.4,
                yAxisID: 'y1',
            },
        ],
    };
});

const topProductsChart = computed(() => {
    const sorted = productMap.value.slice().sort((a, b) => b.totalSales - a.totalSales).slice(0, 8);
    return {
        labels: sorted.map(item => item.name),
        datasets: [
            {
                label: 'Ingressos',
                backgroundColor: 'rgba(147, 51, 234, 0.45)',
                borderColor: 'rgba(147, 51, 234, 1)',
                borderWidth: 1,
                data: sorted.map(item => item.totalSales),
            },
        ],
    };
});

const sortedProducts = computed(() => {
    const base = productMap.value.slice();
    if (sortBy.value === 'quantitySold') {
        return base.sort((a, b) => b.quantitySold - a.quantitySold);
    }
    if (sortBy.value === 'totalSales') {
        return base.sort((a, b) => b.totalSales - a.totalSales);
    }
    return base;
});
</script>
