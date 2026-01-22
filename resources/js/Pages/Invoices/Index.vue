<template>
    <AppLayout>
        <transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="duration-200 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-2 opacity-0"
        >
            <div v-if="toast.show" class="fixed right-6 top-24 z-[60] w-80">
                <div :class="toastContainerClasses">
                    <span :class="toastIconClasses">
                        <svg v-if="toast.variant === 'error'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                            <path fill-rule="evenodd" d="M10.29 3.86a2 2 0 0 1 3.42 0l7.162 12.321c.73 1.257-.174 2.819-1.71 2.819H4.838c-1.536 0-2.44-1.562-1.71-2.819L10.29 3.86Zm1.71 4.64a.75.75 0 0 0-1.5 0v4.5a.75.75 0 0 0 1.5 0v-4.5Zm-.75 8.25a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z" clip-rule="evenodd" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                            <path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm14.28-1.53a.75.75 0 0 0-1.06-1.06l-4.72 4.72-1.94-1.94a.75.75 0 1 0-1.06 1.06l2.47 2.47a.75.75 0 0 0 1.06 0l5.25-5.25Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-700">{{ toastTitle }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ toast.message }}</p>
                    </div>
                    <button
                        type="button"
                        class="-mr-2 -mt-2 inline-flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                        @click="dismissToast"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 0 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </transition>

        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Gestión de facturas"
                    description="Visualiza el estado de cobros, consulta tendencias y toma decisiones rápidas sobre tu cartera de clientes."
                    :icon="MenuInvoiceIcon"
                >
                    <template #actions>
                        <NavLink :href="route('invoices.create')" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                            <AddIcon class="w-5 h-5" />
                            Nueva factura
                        </NavLink>
                    </template>
                </CrudPageHeader>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <CrudStatCard label="Facturas visibles" :value="filteredInvoices.length" :icon="MenuInvoiceIcon" icon-background="bg-sky-500/10 text-sky-600">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ invoices.length }} registradas en total</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Clientes facturados" :value="clients.length" :icon="InfoIcon" icon-background="bg-indigo-500/10 text-indigo-600">
                        <template #description>
                            <p class="text-xs text-slate-500">Con facturas activas</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Pagadas a tiempo" :value="`${PrecentPaidInvoices}%`" :icon="AddIcon" icon-background="bg-emerald-500/10 text-emerald-600">
                        <template #description>
                            <p class="text-xs text-slate-500">Facturas en estado pagado</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Importe filtrado" :value="formatCurrency(filteredInvoicesTotal)" :icon="EditIcon" icon-background="bg-amber-500/10 text-amber-600">
                        <template #description>
                            <p class="text-xs text-slate-500">Suma de las facturas visibles</p>
                        </template>
                    </CrudStatCard>
                </div>

                <CrudFilterBar>
                    <div class="grid flex-1 grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Cliente</label>
                            <select v-model="selectedClient" id="clientFilter" class="w-full rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200/40">
                                <option value="">Todos</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Estado</label>
                            <select v-model="selectedStatus" id="statusFilter" class="w-full rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200/40">
                                <option value="">Todos</option>
                                <option value="paid">Pagado</option>
                                <option value="pending">Pendiente</option>
                                <option value="cancelled">Cancelado</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Fecha de inicio</label>
                            <input type="date" v-model="startDate" id="startDateFilter" class="w-full rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200/40" />
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Fecha de fin</label>
                            <input type="date" v-model="endDate" id="endDateFilter" class="w-full rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200/40" />
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Importe mínimo</label>
                            <input type="number" min="0" step="0.01" v-model.number="minTotal" class="w-full rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200/40" placeholder="0,00 €" />
                        </div>
                    </div>

                    <template #actions>
                        <button @click="clearFilters" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
                            Limpiar filtros
                        </button>
                    </template>
                </CrudFilterBar>

                <CrudTable>
                    <template #head>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-400">Identificador</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-400">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-400">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-400">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-400">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-widest text-slate-400">Acciones</th>
                        </tr>
                    </template>
                    <tr v-for="invoice in filteredInvoices" :key="invoice.id" class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ invoice.name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ getClientName(invoice.client_id) || '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(invoice.date) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span
                                :class="[statusBadgeClasses(invoice.state), 'cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition']"
                                role="button"
                                tabindex="0"
                                @click="openInvoiceStatusModal(invoice)"
                                @keydown.enter.prevent="openInvoiceStatusModal(invoice)"
                                @keydown.space.prevent="openInvoiceStatusModal(invoice)"
                            >
                                {{ statusCopy(invoice.state) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ formatCurrency(invoice.total) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-3 text-slate-500">
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
                </CrudTable>
            </div>
        </div>

        <StatusUpdateModal
            :show="showInvoiceStatusModal"
            title="Actualitzar estat de la factura"
            :document-name="selectedInvoice ? selectedInvoice.name : ''"
            :current-status-label="selectedInvoice ? statusCopy(selectedInvoice.state) : ''"
            :current-status-class="selectedInvoice ? statusBadgeClasses(selectedInvoice.state) : ''"
            :options="invoiceStatusOptions"
            v-model="invoiceStatusForm.state"
            :loading="invoiceStatusForm.processing"
            :confirm-disabled="!canSubmitInvoiceStatus"
            :error="invoiceStatusForm.errors.state"
            confirm-label="Actualitzar estat"
            @close="closeInvoiceStatusModal"
            @confirm="submitInvoiceStatus"
        />
    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudFilterBar from '@/Components/Crud/CrudFilterBar.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import CrudTable from '@/Components/Crud/CrudTable.vue';
import NavLink from "@/Components/NavLink.vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import MenuInvoiceIcon from "@/Components/Icons/MenuInvoiceIcon.vue";
import StatusUpdateModal from '@/Components/StatusUpdateModal.vue';
import { computed, reactive, ref } from "vue";

const props = defineProps({
    invoices: Array,
    clients: Array,
});

const invoiceStatusOptions = [
    {
        value: 'paid',
        label: 'Pagado',
        badgeClass: 'bg-emerald-100 text-emerald-700 ring-emerald-500/30',
        description: 'Marca la factura como cobrada y registra el ingreso asociado.',
    },
    {
        value: 'pending',
        label: 'Pendiente',
        badgeClass: 'bg-amber-100 text-amber-700 ring-amber-500/30',
        description: 'Mantén la factura abierta hasta recibir el pago del cliente.',
    },
    {
        value: 'cancelled',
        label: 'Cancelado',
        badgeClass: 'bg-rose-100 text-rose-700 ring-rose-500/30',
        description: 'Anula la factura y deja constancia de que no generará cobro.',
    },
];

const showInvoiceStatusModal = ref(false);
const selectedInvoice = ref(null);
const invoiceStatusForm = useForm({
    state: '',
});

const toast = reactive({
    show: false,
    message: '',
    variant: 'success',
});

let toastTimeout;

const toastTitle = computed(() => (toast.variant === 'error' ? 'Hi ha hagut un problema' : 'Estat actualitzat'));

const toastContainerClasses = computed(() => {
    const base = 'flex w-full items-start gap-3 rounded-2xl border px-4 py-4 shadow-xl backdrop-blur bg-white/95';
    return toast.variant === 'error' ? `${base} border-rose-200` : `${base} border-emerald-200`;
});

const toastIconClasses = computed(() =>
    toast.variant === 'error'
        ? 'flex h-9 w-9 items-center justify-center rounded-xl bg-rose-500/10 text-rose-500'
        : 'flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-500'
);

const showToast = (message, variant = 'success') => {
    toast.message = message;
    toast.variant = variant;
    toast.show = true;

    if (toastTimeout) {
        clearTimeout(toastTimeout);
    }

    toastTimeout = setTimeout(() => {
        toast.show = false;
        toastTimeout = null;
    }, 3200);
};

const dismissToast = () => {
    toast.show = false;
    if (toastTimeout) {
        clearTimeout(toastTimeout);
        toastTimeout = null;
    }
};

const openInvoiceStatusModal = (invoice) => {
    selectedInvoice.value = invoice;
    invoiceStatusForm.state = invoice.state;
    invoiceStatusForm.clearErrors();
    showInvoiceStatusModal.value = true;
};

const closeInvoiceStatusModal = () => {
    showInvoiceStatusModal.value = false;
    invoiceStatusForm.clearErrors();
    selectedInvoice.value = null;
};

const canSubmitInvoiceStatus = computed(() => {
    if (!selectedInvoice.value) {
        return false;
    }

    return invoiceStatusForm.state !== '' && invoiceStatusForm.state !== selectedInvoice.value.state;
});

const submitInvoiceStatus = () => {
    if (!selectedInvoice.value || !canSubmitInvoiceStatus.value) {
        return;
    }

    invoiceStatusForm.patch(route('invoices.updateStatus', selectedInvoice.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({
                only: ['invoices'],
                preserveScroll: true,
                onSuccess: () => {
                    showToast('Estat de la factura actualitzat correctament.');
                    closeInvoiceStatusModal();
                },
                onError: () => {
                    showToast('No s\'ha pogut refrescar la llista de factures.', 'error');
                },
            });
        },
        onError: () => {
            showToast('No s\'ha pogut actualitzar l\'estat de la factura.', 'error');
        },
    });
};

const selectedClient = ref('');
const selectedStatus = ref('');
const startDate = ref('');
const endDate = ref('');
const minTotal = ref(null);

const filteredInvoices = computed(() => props.invoices
    .filter(invoice => {
        const clientMatch = selectedClient.value === '' || invoice.client_id === selectedClient.value;
        const statusMatch = selectedStatus.value === '' || invoice.state === selectedStatus.value;

        const invoiceDate = new Date(invoice.date);
        const startDateMatch = startDate.value === '' || invoiceDate >= new Date(startDate.value);
        const endDateMatch = endDate.value === '' || invoiceDate <= new Date(endDate.value);

        const totalMatch = minTotal.value === null || Number(invoice.total) >= Number(minTotal.value || 0);

        return clientMatch && statusMatch && startDateMatch && endDateMatch && totalMatch;
    })
    .sort((a, b) => new Date(b.date || 0) - new Date(a.date || 0))
);

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
    const base = 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold shadow-sm ring-1 ring-inset';

    switch (state) {
        case 'paid':
            return `${base} bg-emerald-100 text-emerald-700 ring-emerald-500/30`;
        case 'pending':
            return `${base} bg-amber-100 text-amber-700 ring-amber-500/30`;
        case 'cancelled':
            return `${base} bg-rose-100 text-rose-700 ring-rose-500/30`;
        default:
            return `${base} bg-slate-100 text-slate-600 ring-slate-300/60`;
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
