<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-fuchsia-600 via-purple-600 to-slate-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div class="space-y-2 text-white">
                            <p class="uppercase tracking-[0.3em] text-fuchsia-200 text-xs">Historial de tickets</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold">Control d'operacions i vendes</h1>
                            <p class="text-sm text-fuchsia-200 max-w-2xl">
                                Consulta les vendes realitzades, filtra per període i detecta tendències amb resums visualment cohesionats.
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button
                                class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20"
                                @click="removeFilters"
                            >
                                <span>Restablir filtres</span>
                            </button>
                            <button
                                class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20"
                                @click="navigateToCreate"
                            >
                                <span>Nou ticket</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mt-10">
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
                            <h2 class="text-xl font-semibold text-slate-800">Filtres de període</h2>
                            <p class="text-sm text-slate-500">Selecciona dates per concentrar-te en moments clau de venda.</p>
                        </div>
                        <p class="text-xs font-medium uppercase tracking-[0.3em] text-slate-400">Flux tàctil optimitzat</p>
                    </header>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
                                Aplicar filtres
                            </button>
                        </div>
                        <div class="flex items-end">
                            <button
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-fuchsia-300 hover:text-fuchsia-500"
                                @click="removeFilters"
                            >
                                Netejar
                            </button>
                        </div>
                    </div>
                </section>

                <section class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <article class="bg-white rounded-3xl shadow-xl p-6 xl:col-span-2">
                        <header>
                            <h2 class="text-xl font-semibold text-slate-800">Tendència d'activitat</h2>
                            <p class="text-sm text-slate-500">Comparativa diària entre volum de tickets i facturació.</p>
                        </header>
                        <div class="mt-6">
                            <LineChart :data="activityTrend" />
                        </div>
                    </article>
                    <article class="bg-white rounded-3xl shadow-xl p-6">
                        <header>
                            <h2 class="text-xl font-semibold text-slate-800">Pes per categories</h2>
                            <p class="text-sm text-slate-500">Distribució d'ingressos segons la família d'origen.</p>
                        </header>
                        <div class="mt-6">
                            <BarChart :data="categoryBreakdown" />
                        </div>
                    </article>
                </section>

                <section class="bg-white rounded-3xl shadow-xl p-6">
                    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Llistat de tickets</h2>
                            <p class="text-sm text-slate-500">Gestiona, imprimeix o edita qualsevol operació recent.</p>
                        </div>
                        <p class="text-xs font-medium uppercase tracking-[0.3em] text-slate-400">Flux de venda ràpida</p>
                    </header>
                    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-100">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="px-6 py-3">Ticket</th>
                                    <th class="px-6 py-3">Data</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                    <th class="px-6 py-3 text-right">Accions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                                <tr
                                    v-for="ticket in filteredTickets"
                                    :key="ticket.id"
                                    class="hover:bg-slate-50/60 transition"
                                >
                                    <td class="px-6 py-4 font-semibold text-slate-800">#{{ ticket.id }}</td>
                                    <td class="px-6 py-4">{{ formatDate(ticket.created_at) }}</td>
                                    <td class="px-6 py-4 text-right font-semibold text-slate-800">{{ formatCurrency(ticket.total) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-3 text-slate-500">
                                            <button class="hover:text-fuchsia-500" @click="print(ticket.id)" aria-label="Imprimir ticket">
                                                <PrintIcon class="h-5 w-5" />
                                            </button>
                                            <button class="hover:text-fuchsia-500" @click="editTicket(ticket.id)" aria-label="Editar ticket">
                                                <EditIcon class="h-5 w-5" />
                                            </button>
                                            <button class="hover:text-rose-500" @click="deleteTicket(ticket.id)" aria-label="Eliminar ticket">
                                                <DeleteIcon class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!filteredTickets.length">
                                    <td class="px-6 py-10 text-center text-slate-400" colspan="4">No s'han trobat resultats per als filtres seleccionats.</td>
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
import { computed, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import KpiCard from '@/Components/KpiCard.vue';
import LineChart from '@/Components/LineChart.vue';
import BarChart from '@/Components/BarChart.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import PrintIcon from '@/Components/Icons/PrintIcon.vue';

const props = withDefaults(defineProps({
    tickets: {
        type: Array,
        default: () => [],
    },
    ticketItems: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
}), {
    tickets: () => [],
    ticketItems: () => [],
    products: () => [],
    categories: () => [],
});

const startDate = ref('');
const endDate = ref('');

const totalTickets = computed(() => props.tickets.length);
const ticketsToday = computed(() => {
    const today = new Date().toISOString().split('T')[0];
    return props.tickets.filter(ticket => formatISO(ticket.created_at) === today).length;
});
const totalIncome = computed(() => props.tickets.reduce((sum, ticket) => sum + (ticket.total ?? 0), 0));
const averageTicket = computed(() => {
    if (!totalTickets.value) {
        return 0;
    }
    return totalIncome.value / totalTickets.value;
});

const formatCurrency = value => new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value ?? 0);
const formatDate = value => new Date(value).toLocaleString();
const formatISO = value => new Date(value).toISOString().split('T')[0];

const filteredTickets = computed(() => {
    return props.tickets.filter(ticket => {
        const ticketDate = new Date(ticket.created_at);
        const hasStart = Boolean(startDate.value);
        const hasEnd = Boolean(endDate.value);
        const start = hasStart ? new Date(startDate.value) : null;
        const end = hasEnd ? new Date(endDate.value) : null;

        const afterStart = !hasStart || ticketDate >= start;
        const beforeEnd = !hasEnd || ticketDate <= end;
        return afterStart && beforeEnd;
    });
});

const kpiCards = computed(() => [
    {
        key: 'total-tickets',
        subtitle: 'Total tickets',
        value: totalTickets.value,
        description: `Acumulat històric a la vista.`,
    },
    {
        key: 'tickets-today',
        subtitle: 'Tickets avui',
        value: ticketsToday.value,
        description: 'Moviment registrat en l\'últim dia natural.',
    },
    {
        key: 'total-income',
        subtitle: 'Ingressos totals',
        value: formatCurrency(totalIncome.value),
        description: 'Facturació acumulada en el període seleccionat.',
    },
    {
        key: 'average-ticket',
        subtitle: 'Mitjana per ticket',
        value: formatCurrency(averageTicket.value),
        description: 'Import mitjà que ajuda a calibrar el tiquet mitjà.',
    },
]);

const activityTrend = computed(() => {
    const grouped = filteredTickets.value.reduce((acc, ticket) => {
        const key = formatISO(ticket.created_at);
        if (!acc[key]) {
            acc[key] = { tickets: 0, revenue: 0 };
        }
        acc[key].tickets += 1;
        acc[key].revenue += ticket.total ?? 0;
        return acc;
    }, {});

    const labels = Object.keys(grouped).sort();
    return {
        labels,
        datasets: [
            {
                label: 'Tickets',
                backgroundColor: 'rgba(236, 72, 153, 0.2)',
                borderColor: 'rgba(236, 72, 153, 1)',
                data: labels.map(label => grouped[label].tickets),
                tension: 0.4,
                borderWidth: 2,
                yAxisID: 'y',
            },
            {
                label: 'Ingressos',
                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                borderColor: 'rgba(99, 102, 241, 1)',
                data: labels.map(label => grouped[label].revenue),
                tension: 0.4,
                borderWidth: 2,
                yAxisID: 'y1',
            },
        ],
    };
});

const categoryBreakdown = computed(() => {
    if (!props.ticketItems.length) {
        return { labels: [], datasets: [] };
    }

    const totals = props.ticketItems.reduce((acc, item) => {
        const product = props.products.find(prod => prod.id === item.product_id);
        if (!product) {
            return acc;
        }
        const category = props.categories.find(cat => cat.id === product.category_id);
        const label = category ? category.name : 'Sense categoria';
        if (!acc[label]) {
            acc[label] = 0;
        }
        acc[label] += (item.quantity ?? 1) * (item.unit_price ?? 0);
        return acc;
    }, {});

    const labels = Object.keys(totals);
    return {
        labels,
        datasets: [
            {
                label: 'Ingressos',
                backgroundColor: 'rgba(147, 51, 234, 0.45)',
                borderColor: 'rgba(147, 51, 234, 1)',
                borderWidth: 1,
                data: labels.map(label => totals[label]),
            },
        ],
    };
});

const applyFilters = () => {
    // La reactivitat dels computed actualitza automàticament els resultats
};

const removeFilters = () => {
    startDate.value = '';
    endDate.value = '';
};

const navigateToCreate = () => {
    Inertia.get(route('tikets.create'));
};

const editTicket = ticketId => {
    Inertia.get(route('tikets.goToEdit', ticketId));
};

const deleteTicket = ticketId => {
    Inertia.delete(route('tikets.destroy', ticketId));
};

const print = ticketId => {
    Inertia.get(route('tikets.print', ticketId));
};
</script>
