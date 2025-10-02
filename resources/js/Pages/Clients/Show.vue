<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-emerald-600 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-6xl mx-auto px-6 pt-10">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div>
                            <p class="text-emerald-200 text-sm uppercase tracking-widest">Ficha de cliente</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white">{{ client.name }}</h1>
                            <p class="text-sm text-emerald-200 mt-2">Visualiza la información clave y el historial de presupuestos y facturas.</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <NavLink :href="route('clients.edit', client.id)" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition">
                                Editar cliente
                            </NavLink>
                            <NavLink :href="route('clients.index')" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition">
                                Volver al listado
                            </NavLink>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-10">
                        <SummaryCard eyebrow="Presupuestos" :value="filteredBudgets.length" :description="`Importe total €${totalBudgetAmount}`" />
                        <SummaryCard eyebrow="Facturas" :value="filteredInvoices.length" :description="`Importe total €${totalInvoiceAmount}`" />
                        <SummaryCard eyebrow="Estado del cliente" :value="statusCopy(client.status)" description="Situación actual" />
                        <SummaryCard eyebrow="Última actividad" :value="lastActivity" description="Último documento registrado" />
                    </div>
                </div>
            </div>

            <div class="max-w-6xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <Panel title="Datos generales" description="Información de contacto y datos fiscales del cliente.">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
                        <div v-for="detail in clientDetails" :key="detail.label" class="rounded-2xl border border-slate-100 bg-slate-50 px-5 py-4">
                            <dt class="text-xs uppercase tracking-[0.25em] text-slate-400">{{ detail.label }}</dt>
                            <dd class="mt-2 text-base font-medium text-slate-700">{{ detail.value || '—' }}</dd>
                        </div>
                    </dl>
                </Panel>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <Panel title="Presupuestos" description="Filtra los presupuestos por estado o rango de fechas.">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="budgetStatusFilter" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">Estado</label>
                                <select id="budgetStatusFilter" v-model="selectedBudgetStatus" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                                    <option value="">Todos</option>
                                    <option value="pending">Pendiente</option>
                                    <option value="approved">Aprobado</option>
                                    <option value="rejected">Rechazado</option>
                                </select>
                            </div>
                            <div>
                                <label for="budgetStartDate" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">Fecha inicio</label>
                                <input id="budgetStartDate" type="date" v-model="budgetStartDate" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" />
                            </div>
                            <div>
                                <label for="budgetEndDate" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">Fecha fin</label>
                                <input id="budgetEndDate" type="date" v-model="budgetEndDate" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" />
                            </div>
                        </div>
                        <DataTable
                            :columns="budgetColumns"
                            :items="filteredBudgets"
                            empty-message="No hay presupuestos que coincidan con el filtro seleccionado."
                            caption="Tabla de presupuestos"
                        >
                            <template #row="{ item: budget }">
                                <td class="py-4 font-semibold text-slate-700">{{ budget.name }}</td>
                                <td class="py-4">{{ formatDate(budget.date) }}</td>
                                <td class="py-4">
                                    <span :class="statusPillClass(budget.state)">{{ statusCopy(budget.state) }}</span>
                                </td>
                                <td class="py-4">€{{ Number(budget.total ?? 0).toFixed(2) }}</td>
                                <td class="py-4">
                                    <NavLink :href="route('budgets.show', budget.id)" class="flex justify-end text-blue-500 hover:text-blue-600 transition" aria-label="Ver presupuesto">
                                        <InfoIcon class="w-5 h-5" />
                                    </NavLink>
                                </td>
                            </template>
                        </DataTable>
                    </Panel>

                    <Panel title="Facturas" description="Controla las facturas emitidas y su estado de cobro.">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="invoiceStatusFilter" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">Estado</label>
                                <select id="invoiceStatusFilter" v-model="selectedInvoiceStatus" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                                    <option value="">Todos</option>
                                    <option value="paid">Pagada</option>
                                    <option value="pending">Pendiente</option>
                                    <option value="cancelled">Cancelada</option>
                                </select>
                            </div>
                            <div>
                                <label for="invoiceStartDate" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">Fecha inicio</label>
                                <input id="invoiceStartDate" type="date" v-model="invoiceStartDate" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" />
                            </div>
                            <div>
                                <label for="invoiceEndDate" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">Fecha fin</label>
                                <input id="invoiceEndDate" type="date" v-model="invoiceEndDate" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" />
                            </div>
                        </div>
                        <DataTable
                            :columns="invoiceColumns"
                            :items="filteredInvoices"
                            empty-message="No hay facturas que coincidan con el filtro seleccionado."
                            caption="Tabla de facturas"
                        >
                            <template #row="{ item: invoice }">
                                <td class="py-4 font-semibold text-slate-700">{{ invoice.name }}</td>
                                <td class="py-4">{{ formatDate(invoice.date) }}</td>
                                <td class="py-4">
                                    <span :class="statusPillClass(invoice.state)">{{ statusCopy(invoice.state) }}</span>
                                </td>
                                <td class="py-4">€{{ Number(invoice.total ?? 0).toFixed(2) }}</td>
                                <td class="py-4">
                                    <NavLink :href="route('invoices.show', invoice.id)" class="flex justify-end text-blue-500 hover:text-blue-600 transition" aria-label="Ver factura">
                                        <InfoIcon class="w-5 h-5" />
                                    </NavLink>
                                </td>
                            </template>
                        </DataTable>
                    </Panel>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import SummaryCard from '@/Components/UI/SummaryCard.vue';
import Panel from '@/Components/UI/Panel.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import InfoIcon from '@/Components/Icons/InfoIcon.vue';

const props = defineProps({
    client: {
        type: Object,
        required: true,
    },
    budgets: {
        type: Array,
        default: () => [],
    },
    invoices: {
        type: Array,
        default: () => [],
    },
});

const client = computed(() => props.client);

const selectedBudgetStatus = ref('');
const budgetStartDate = ref('');
const budgetEndDate = ref('');
const selectedInvoiceStatus = ref('');
const invoiceStartDate = ref('');
const invoiceEndDate = ref('');

const filteredBudgets = computed(() => {
    return props.budgets.filter(budget => {
        const statusMatches = !selectedBudgetStatus.value || budget.state === selectedBudgetStatus.value;
        const startDateMatches = !budgetStartDate.value || new Date(budget.date) >= new Date(budgetStartDate.value);
        const endDateMatches = !budgetEndDate.value || new Date(budget.date) <= new Date(budgetEndDate.value);
        return statusMatches && startDateMatches && endDateMatches;
    });
});

const filteredInvoices = computed(() => {
    return props.invoices.filter(invoice => {
        const statusMatches = !selectedInvoiceStatus.value || invoice.state === selectedInvoiceStatus.value;
        const startDateMatches = !invoiceStartDate.value || new Date(invoice.date) >= new Date(invoiceStartDate.value);
        const endDateMatches = !invoiceEndDate.value || new Date(invoice.date) <= new Date(invoiceEndDate.value);
        return statusMatches && startDateMatches && endDateMatches;
    });
});

const totalBudgetAmount = computed(() => filteredBudgets.value.reduce((acc, budget) => acc + Number(budget.total ?? 0), 0).toFixed(2));
const totalInvoiceAmount = computed(() => filteredInvoices.value.reduce((acc, invoice) => acc + Number(invoice.total ?? 0), 0).toFixed(2));

const lastActivity = computed(() => {
    const combined = [...props.budgets, ...props.invoices]
        .filter(item => item.date)
        .sort((a, b) => new Date(b.date) - new Date(a.date));
    if (!combined.length) {
        return 'Sin actividad';
    }
    const latest = combined[0];
    const amount = latest.total ? ` · €${Number(latest.total).toFixed(2)}` : '';
    return `${formatDate(latest.date)}${amount}`;
});

const clientDetails = computed(() => [
    { label: 'Nombre', value: client.value.name },
    { label: 'NIF', value: client.value.nif },
    { label: 'Email', value: client.value.email },
    { label: 'Teléfono', value: client.value.phone },
    { label: 'Banco', value: client.value.bank },
    { label: 'Dirección', value: client.value.address },
]);

const budgetColumns = [
    { key: 'name', label: 'Identificador' },
    { key: 'date', label: 'Fecha' },
    { key: 'state', label: 'Estado' },
    { key: 'total', label: 'Total' },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

const invoiceColumns = [
    { key: 'name', label: 'Identificador' },
    { key: 'date', label: 'Fecha' },
    { key: 'state', label: 'Estado' },
    { key: 'total', label: 'Total' },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

function formatDate(value) {
    if (!value) {
        return '—';
    }
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }
    return date.toLocaleDateString();
}

function statusCopy(status) {
    switch (status) {
        case 'active':
            return 'Activo';
        case 'inactive':
            return 'Inactivo';
        case 'pending':
            return 'Pendiente';
        case 'approved':
            return 'Aprobado';
        case 'rejected':
            return 'Rechazado';
        case 'paid':
            return 'Pagada';
        case 'cancelled':
            return 'Cancelada';
        default:
            return 'Sin estado';
    }
}

function statusPillClass(status) {
    const base = 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold capitalize';
    const mapping = {
        active: 'bg-emerald-100 text-emerald-700',
        approved: 'bg-emerald-100 text-emerald-700',
        paid: 'bg-emerald-100 text-emerald-700',
        pending: 'bg-amber-100 text-amber-700',
        inactive: 'bg-slate-200 text-slate-600',
        rejected: 'bg-rose-100 text-rose-700',
        cancelled: 'bg-rose-100 text-rose-700',
    };
    return `${base} ${mapping[status] || 'bg-slate-200 text-slate-600'}`;
}
</script>
