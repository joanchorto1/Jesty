<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-900">
            <div class="bg-gradient-to-r from-sky-600 via-blue-600 to-slate-900 pb-20">
                <div class="max-w-7xl mx-auto px-6 pt-12">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <p class="text-sm uppercase tracking-widest text-sky-200">Facturación</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white mt-2">Gestión de facturas</h1>
                            <p class="text-sm text-sky-100 mt-3 max-w-2xl">Visualiza el estado de cobros, consulta tendencias y toma decisiones rápidas sobre tu cartera de clientes.</p>
                        </div>
                        <NavLink :href="route('invoices.create')" class="inline-flex items-center gap-2 rounded-2xl bg-white/15 px-5 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/25 transition">
                            <AddIcon class="w-5 h-5" />
                            Nueva factura
                        </NavLink>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-widest text-sky-200">Facturas visibles</p>
                            <p class="text-3xl font-semibold mt-2">{{ filteredInvoices.length }}</p>
                            <p class="text-sm text-sky-100 mt-3">{{ invoices.length }} registradas en total</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-widest text-sky-200">Clientes facturados</p>
                            <p class="text-3xl font-semibold mt-2">{{ clients.length }}</p>
                            <p class="text-sm text-sky-100 mt-3">Con facturas activas</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-widest text-sky-200">Pagadas a tiempo</p>
                            <p class="text-3xl font-semibold mt-2">{{ PrecentPaidInvoices }}%</p>
                            <p class="text-sm text-sky-100 mt-3">Facturas en estado pagado</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-widest text-sky-200">Importe filtrado</p>
                            <p class="text-3xl font-semibold mt-2">{{ formatCurrency(filteredInvoicesTotal) }}</p>
                            <p class="text-sm text-sky-100 mt-3">Suma de las facturas visibles</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 pb-5">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Filtros avanzados</h2>
                            <p class="text-sm text-slate-500 mt-1">Encuentra facturas concretas según cliente, estado o periodo de emisión.</p>
                        </div>
                        <button @click="clearFilters" class="inline-flex items-center justify-center gap-2 rounded-xl border border-transparent bg-rose-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-600 transition">
                            Limpiar filtros
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 pt-6">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Cliente</label>
                            <select v-model="selectedClient" id="clientFilter" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40">
                                <option value="">Todos</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Estado</label>
                            <select v-model="selectedStatus" id="statusFilter" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40">
                                <option value="">Todos</option>
                                <option value="paid">Pagado</option>
                                <option value="pending">Pendiente</option>
                                <option value="cancelled">Cancelado</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Fecha de inicio</label>
                            <input type="date" v-model="startDate" id="startDateFilter" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40" />
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Fecha de fin</label>
                            <input type="date" v-model="endDate" id="endDateFilter" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40" />
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Importe mínimo</label>
                            <input type="number" min="0" step="0.01" v-model.number="minTotal" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40" placeholder="0,00 €" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 px-6 py-5">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Histórico de facturas</h2>
                            <p class="text-sm text-slate-500 mt-1">Monitoriza tus cobros y accede a cada documento en cuestión de segundos.</p>
                        </div>
                        <NavLink :href="route('invoices.create')" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition">
                            <AddIcon class="w-5 h-5" />
                            Nueva factura
                        </NavLink>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-left">
                            <thead>
                                <tr class="text-xs uppercase tracking-widest text-slate-400">
                                    <th class="px-6 py-3">Identificador</th>
                                    <th class="px-6 py-3">Cliente</th>
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Estado</th>
                                    <th class="px-6 py-3">Total</th>
                                    <th class="px-6 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                                <tr v-for="invoice in filteredInvoices" :key="invoice.id" class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 font-medium text-slate-700">{{ invoice.name }}</td>
                                    <td class="px-6 py-4">{{ getClientName(invoice.client_id) || '—' }}</td>
                                    <td class="px-6 py-4">{{ formatDate(invoice.date) }}</td>
                                    <td class="px-6 py-4">
                                        <span :class="statusBadgeClasses(invoice.state)">{{ statusCopy(invoice.state) }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-700">{{ formatCurrency(invoice.total) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-3 text-slate-400">
                                            <NavLink :href="route('invoices.show', invoice.id)" class="hover:text-blue-500 transition" title="Ver detalles">
                                                <InfoIcon class="w-5 h-5"/>
                                            </NavLink>
                                            <NavLink :href="route('invoices.edit', invoice.id)" class="hover:text-amber-500 transition" title="Editar">
                                                <EditIcon class="w-5 h-5"/>
                                            </NavLink>
                                            <button @click="deleteInvoice(invoice.id)" class="hover:text-rose-500 transition" title="Eliminar">
                                                <DeleteIcon class="w-5 h-5"/>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredInvoices.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400">No hay facturas que coincidan con los filtros seleccionados.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from "@/Components/NavLink.vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import { computed, ref } from "vue";

const props = defineProps({
    invoices: Array,
    clients: Array,
});

const selectedClient = ref('');
const selectedStatus = ref('');
const startDate = ref('');
const endDate = ref('');
const minTotal = ref(null);

const filteredInvoices = computed(() => props.invoices.filter(invoice => {
    const clientMatch = selectedClient.value === '' || invoice.client_id === selectedClient.value;
    const statusMatch = selectedStatus.value === '' || invoice.state === selectedStatus.value;

    const invoiceDate = new Date(invoice.date);
    const startDateMatch = startDate.value === '' || invoiceDate >= new Date(startDate.value);
    const endDateMatch = endDate.value === '' || invoiceDate <= new Date(endDate.value);

    const totalMatch = minTotal.value === null || Number(invoice.total) >= Number(minTotal.value || 0);

    return clientMatch && statusMatch && startDateMatch && endDateMatch && totalMatch;
}));

const filteredInvoicesTotal = computed(() => filteredInvoices.value.reduce((total, invoice) => total + Number(invoice.total || 0), 0));

const clearFilters = () => {
    selectedClient.value = '';
    selectedStatus.value = '';
    startDate.value = '';
    endDate.value = '';
    minTotal.value = null;
};

const deleteInvoice = (id) => {
    if (confirm("¿Estás seguro de que deseas eliminar esta factura?")) {
        Inertia.delete(route('invoices.destroy', id));
    }
};

const getClientName = (clientId) => {
    const client = props.clients.find(client => client.id === clientId);
    return client ? client.name : '';
};

const PrecentPaidInvoices = computed(() => {
    if (props.invoices.length === 0) {
        return 0;
    }

    const paidInvoices = props.invoices.filter(invoice => invoice.state === 'paid');
    return Math.round((paidInvoices.length / props.invoices.length) * 100);
});

const statusCopy = (state) => {
    switch (state) {
        case 'paid':
            return 'Pagado';
        case 'pending':
            return 'Pendiente';
        case 'cancelled':
            return 'Cancelado';
        default:
            return 'Sin estado';
    }
};

const statusBadgeClasses = (state) => {
    switch (state) {
        case 'paid':
            return 'inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700';
        case 'pending':
            return 'inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700';
        case 'cancelled':
            return 'inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700';
        default:
            return 'inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600';
    }
};

const formatDate = (date) => {
    if (!date) {
        return '—';
    }

    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatCurrency = (value) => {
    const numericValue = Number(value);

    if (Number.isNaN(numericValue)) {
        return '—';
    }

    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(numericValue);
};
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>
