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
                        <SummaryCard
                            v-for="card in summaryCards"
                            :key="card.eyebrow"
                            :eyebrow="card.eyebrow"
                            :value="card.value"
                            :description="card.description"
                        />
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <Panel title="Filtrado inteligente" description="Encuentra el cliente ideal combinando filtros avanzados.">
                    <template #actions>
                        <button @click="clearFilters" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Limpiar filtros
                        </button>
                    </template>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div v-for="field in filterFields" :key="field.id">
                            <label :for="field.id" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">{{ field.label }}</label>
                            <input
                                :id="field.id"
                                v-model="field.model.value"
                                :type="field.type"
                                :placeholder="field.placeholder"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            />
                        </div>
                    </div>
                </Panel>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <Panel
                        class="xl:col-span-2"
                        title="Clientes"
                        description="Listado actualizado con accesos rápidos a cada ficha."
                    >
                        <DataTable
                            :columns="clientTableColumns"
                            :items="filteredClients"
                            empty-message="No se han encontrado clientes con los filtros actuales."
                            caption="Listado de clientes"
                        >
                            <template #row="{ item: client }">
                                <td class="py-4 font-semibold text-slate-700">#{{ client.id }}</td>
                                <td class="py-4">
                                    <p class="font-medium text-slate-700">{{ client.name }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ statusCopy(client.status) }}</p>
                                </td>
                                <td class="py-4">{{ client.email }}</td>
                                <td class="py-4">{{ client.phone }}</td>
                                <td class="py-4">
                                    <div class="flex items-center justify-end gap-3 text-slate-500">
                                        <NavLink
                                            :href="route('clients.show', client.id)"
                                            class="hover:text-blue-500 transition"
                                            aria-label="Ver detalles del cliente"
                                        >
                                            <InfoIcon class="w-5 h-5" />
                                        </NavLink>
                                        <NavLink
                                            :href="route('clients.edit', client.id)"
                                            class="hover:text-amber-500 transition"
                                            aria-label="Editar cliente"
                                        >
                                            <EditIcon class="w-5 h-5" />
                                        </NavLink>
                                        <button
                                            type="button"
                                            @click="deleteClient(client.id)"
                                            class="hover:text-rose-500 transition"
                                            aria-label="Eliminar cliente"
                                        >
                                            <DeleteIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </td>
                            </template>
                        </DataTable>
                    </Panel>

                    <div class="space-y-8">
                        <Panel title="Estado de la cartera" description="Visualiza la salud actual de tus relaciones comerciales." :header-border="false">
                            <div class="mt-5">
                                <DoughnutChart :data="clientStatusChart" />
                            </div>
                        </Panel>

                        <Panel title="Ingresos por cliente" description="Identifica los clientes con mayor contribución económica." :header-border="false">
                            <div class="mt-5">
                                <BarChart :data="revenueByClientChart" />
                            </div>
                        </Panel>
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
import SummaryCard from '@/Components/UI/SummaryCard.vue';
import Panel from '@/Components/UI/Panel.vue';
import DataTable from '@/Components/UI/DataTable.vue';

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
    invoices: {
        type: Array,
        default: () => [],
    },
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

const summaryCards = computed(() => [
    {
        eyebrow: 'Clientes totales',
        value: filteredClients.value.length,
        description: `${activeClients.value} activos`,
    },
    {
        eyebrow: 'Clientes inactivos',
        value: inactiveClients.value,
        description: `${churnRate.value}% tasa de baja`,
    },
    {
        eyebrow: 'Ingresos acumulados',
        value: `€${totalBilled.value}`,
        description: `Ticket medio €${averageInvoice.value}`,
    },
    {
        eyebrow: 'Facturas cobradas',
        value: `${paidInvoicesPercentage.value}%`,
        description: `${paidInvoices.value} de ${props.invoices.length}`,
    },
]);

const filterFields = [
    {
        id: 'nameFilter',
        label: 'Nombre',
        placeholder: 'Buscar por nombre',
        type: 'text',
        model: nameFilter,
    },
    {
        id: 'phoneFilter',
        label: 'Teléfono',
        placeholder: 'Buscar por teléfono',
        type: 'text',
        model: phoneFilter,
    },
    {
        id: 'nifFilter',
        label: 'NIF',
        placeholder: 'Buscar por NIF',
        type: 'text',
        model: nifFilter,
    },
];

const clientTableColumns = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Nombre' },
    { key: 'email', label: 'Correo' },
    { key: 'phone', label: 'Teléfono' },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

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
