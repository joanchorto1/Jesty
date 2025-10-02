<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-emerald-600 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div>
                            <p class="text-emerald-200 text-sm uppercase tracking-widest">Inteligencia de clientes</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white">Visión 360º de tu cartera</h1>
                            <p class="text-sm text-emerald-200 mt-2">Segmenta, analiza y toma decisiones informadas sobre tus relaciones comerciales.</p>
                        </div>
                        <NavLink :href="route('clients.create')" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition">
                            <AddIcon class="w-4 h-4" /> Nuevo cliente
                        </NavLink>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Clientes totales</p>
                            <p class="text-3xl font-semibold mt-2">{{ filteredClients.length }}</p>
                            <p class="text-sm text-emerald-200 mt-3">{{ activeClients }} activos</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Clientes inactivos</p>
                            <p class="text-3xl font-semibold mt-2">{{ inactiveClients }}</p>
                            <p class="text-sm text-emerald-200 mt-3">{{ churnRate }}% tasa de baja</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Ingresos acumulados</p>
                            <p class="text-3xl font-semibold mt-2">€{{ totalBilled }}</p>
                            <p class="text-sm text-emerald-200 mt-3">Ticket medio €{{ averageInvoice }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Facturas cobradas</p>
                            <p class="text-3xl font-semibold mt-2">{{ paidInvoicesPercentage }}%</p>
                            <p class="text-sm text-emerald-200 mt-3">{{ paidInvoices }} de {{ props.invoices.length }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 pb-5">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Filtrado inteligente</h2>
                            <p class="text-sm text-slate-500 mt-1">Encuentra el cliente ideal combinando filtros avanzados.</p>
                        </div>
                        <button @click="clearFilters" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Limpiar filtros
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6">
                        <div>
                            <label for="nameFilter" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">Nombre</label>
                            <input id="nameFilter" v-model="nameFilter" type="text" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="Buscar por nombre" />
                        </div>
                        <div>
                            <label for="phoneFilter" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">Teléfono</label>
                            <input id="phoneFilter" v-model="phoneFilter" type="text" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="Buscar por teléfono" />
                        </div>
                        <div>
                            <label for="nifFilter" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">NIF</label>
                            <input id="nifFilter" v-model="nifFilter" type="text" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="Buscar por NIF" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <div class="xl:col-span-2 bg-white rounded-3xl shadow-xl p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 pb-5">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-800">Clientes</h2>
                                <p class="text-sm text-slate-500 mt-1">Listado actualizado con accesos rápidos a cada ficha.</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto pt-6">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-xs uppercase tracking-widest text-slate-400">
                                        <th class="pb-3">ID</th>
                                        <th class="pb-3">Nombre</th>
                                        <th class="pb-3">Correo</th>
                                        <th class="pb-3">Teléfono</th>
                                        <th class="pb-3 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                                    <tr v-for="client in filteredClients" :key="client.id" class="hover:bg-slate-50/80 transition">
                                        <td class="py-4 font-semibold text-slate-700">#{{ client.id }}</td>
                                        <td class="py-4">
                                            <p class="font-medium text-slate-700">{{ client.name }}</p>
                                            <p class="text-xs text-slate-400 mt-1">{{ statusCopy(client.status) }}</p>
                                        </td>
                                        <td class="py-4">{{ client.email }}</td>
                                        <td class="py-4">{{ client.phone }}</td>
                                        <td class="py-4">
                                            <div class="flex items-center justify-end gap-3 text-slate-500">
                                                <NavLink :href="route('clients.show', client.id)" class="hover:text-blue-500 transition"><InfoIcon class="w-5 h-5" /></NavLink>
                                                <NavLink :href="route('clients.edit', client.id)" class="hover:text-amber-500 transition"><EditIcon class="w-5 h-5" /></NavLink>
                                                <button @click="deleteClient(client.id)" class="hover:text-rose-500 transition">
                                                    <DeleteIcon class="w-5 h-5" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredClients.length === 0">
                                        <td colspan="5" class="py-6 text-center text-slate-400">No se han encontrado clientes con los filtros actuales.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="bg-white rounded-3xl shadow-xl p-6">
                            <h2 class="text-xl font-semibold text-slate-800">Estado de la cartera</h2>
                            <p class="text-sm text-slate-500">Visualiza la salud actual de tus relaciones comerciales.</p>
                            <div class="mt-5">
                                <DoughnutChart :data="clientStatusChart" />
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl shadow-xl p-6">
                            <h2 class="text-xl font-semibold text-slate-800">Ingresos por cliente</h2>
                            <p class="text-sm text-slate-500">Identifica los clientes con mayor contribución económica.</p>
                            <div class="mt-5">
                                <BarChart :data="revenueByClientChart" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from "@/Components/NavLink.vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import DoughnutChart from '@/Components/DoughnutChart.vue';
import BarChart from '@/Components/BarChart.vue';

const props = defineProps({
    clients: Array,
    invoices: Array,
});

const nameFilter = ref('');
const phoneFilter = ref('');
const nifFilter = ref('');

const filteredClients = computed(() => {
    return props.clients.filter(client => {
        const nameMatch = !nameFilter.value || (client.name ?? '').toLowerCase().includes(nameFilter.value.toLowerCase());
        const phoneMatch = !phoneFilter.value || (client.phone ?? '').includes(phoneFilter.value);
        const nifMatch = !nifFilter.value || (client.nif ?? '').includes(nifFilter.value);
        return nameMatch && phoneMatch && nifMatch;
    });
});

const activeClients = computed(() => props.clients.filter(client => client.status === 'active').length);
const inactiveClients = computed(() => props.clients.filter(client => client.status === 'inactive').length);
const churnRate = computed(() => props.clients.length ? Math.round((inactiveClients.value / props.clients.length) * 100) : 0);

const paidInvoices = computed(() => props.invoices.filter(invoice => invoice.state === 'paid').length);
const paidInvoicesPercentage = computed(() => props.invoices.length ? Math.round((paidInvoices.value / props.invoices.length) * 100) : 0);
const totalBilled = computed(() => props.invoices.reduce((acc, invoice) => acc + (invoice.total ?? 0), 0).toFixed(2));
const averageInvoice = computed(() => props.invoices.length ? (props.invoices.reduce((acc, invoice) => acc + (invoice.total ?? 0), 0) / props.invoices.length).toFixed(2) : '0.00');

const clientStatusChart = computed(() => {
    const data = {
        Activos: activeClients.value,
        Inactivos: inactiveClients.value,
        "Sin estado": props.clients.length - activeClients.value - inactiveClients.value,
    };

    return {
        labels: Object.keys(data),
        datasets: [
            {
                backgroundColor: ['#10B981', '#F97316', '#CBD5F5'],
                data: Object.values(data),
            },
        ],
    };
});

const revenueByClientChart = computed(() => {
    const totals = props.invoices.reduce((acc, invoice) => {
        const clientId = invoice.client_id;
        acc[clientId] = (acc[clientId] || 0) + (invoice.total ?? 0);
        return acc;
    }, {});

    const sortedClients = Object.entries(totals)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 10);

    const labels = sortedClients.map(([clientId]) => {
        const client = props.clients.find(c => c.id === Number(clientId));
        return client ? client.name : `Cliente ${clientId}`;
    });

    const data = sortedClients.map(([, value]) => value);

    return {
        labels,
        datasets: [
            {
                label: 'Ingresos',
                backgroundColor: 'rgba(52, 211, 153, 0.45)',
                borderColor: 'rgba(52, 211, 153, 1)',
                borderWidth: 1,
                data,
            },
        ],
    };
});

function clearFilters() {
    nameFilter.value = '';
    phoneFilter.value = '';
    nifFilter.value = '';
}

function deleteClient(id) {
    if (confirm("¿Estás seguro de que deseas eliminar este cliente?")) {
        Inertia.delete(route('clients.destroy', id));
    }
}

function statusCopy(status) {
    switch (status) {
        case 'active':
            return 'Activo';
        case 'inactive':
            return 'Inactivo';
        default:
            return 'Sin estado';
    }
}
</script>
