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

        <div class="min-h-screen bg-slate-900">
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-slate-900 pb-20">
                <div class="max-w-7xl mx-auto px-6 pt-12">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <p class="text-sm uppercase tracking-widest text-blue-200">Facturación</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white mt-2">Resumen de presupuestos</h1>
                            <p class="text-sm text-blue-100 mt-3 max-w-2xl">Controla el rendimiento comercial y haz seguimiento de los presupuestos para convertir más oportunidades en ventas.</p>
                        </div>
                        <NavLink :href="route('budgets.create')" class="inline-flex items-center gap-2 rounded-2xl bg-white/15 px-5 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/25 transition">
                            <AddIcon class="w-5 h-5" />
                            Nuevo presupuesto
                        </NavLink>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-widest text-blue-200">Presupuestos activos</p>
                            <p class="text-3xl font-semibold mt-2">{{ filteredBudgets.length }}</p>
                            <p class="text-sm text-blue-100 mt-3">{{ budgets.length }} totales registrados</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-widest text-blue-200">Clientes</p>
                            <p class="text-3xl font-semibold mt-2">{{ clients.length }}</p>
                            <p class="text-sm text-blue-100 mt-3">Relacionados con presupuestos</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-widest text-blue-200">Tasa de conversión</p>
                            <p class="text-3xl font-semibold mt-2">{{ PrecentAcceptedBudgets }}%</p>
                            <p class="text-sm text-blue-100 mt-3">Presupuestos aceptados frente al total</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-widest text-blue-200">Importe filtrado</p>
                            <p class="text-3xl font-semibold mt-2">{{ formatCurrency(filteredBudgetsTotal) }}</p>
                            <p class="text-sm text-blue-100 mt-3">Suma de los presupuestos visibles</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 pb-5">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Filtros inteligentes</h2>
                            <p class="text-sm text-slate-500 mt-1">Refina la información por estado, cliente o periodo de tiempo.</p>
                        </div>
                        <button @click="clearFilters" class="inline-flex items-center justify-center gap-2 rounded-xl border border-transparent bg-rose-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-600 transition">
                            Limpiar filtros
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 pt-6">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Estado</label>
                            <select v-model="filters.state" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40">
                                <option value="">Todos</option>
                                <option value="accepted">Aceptado</option>
                                <option value="in_process">En proceso</option>
                                <option value="rejected">Rechazado</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Cliente</label>
                            <select v-model="filters.client_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40">
                                <option value="">Todos</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Fecha inicio</label>
                            <input v-model="filters.start_date" type="date" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40"/>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Fecha fin</label>
                            <input v-model="filters.end_date" type="date" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40"/>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 px-6 py-5">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Listado de presupuestos</h2>
                            <p class="text-sm text-slate-500 mt-1">Consulta el detalle de cada propuesta y gestiona su ciclo de venta.</p>
                        </div>
                        <NavLink :href="route('budgets.create')" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition">
                            <AddIcon class="w-5 h-5" />
                            Nuevo presupuesto
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
                                <tr v-for="budget in filteredBudgets" :key="budget.id" class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 font-medium text-slate-700">{{ budget.name }}</td>
                                    <td class="px-6 py-4">{{ getClientName(budget.client_id) || '—' }}</td>
                                    <td class="px-6 py-4">{{ formatDate(budget.date) }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="[statusBadgeClasses(budget.state), 'cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition']"
                                            role="button"
                                            tabindex="0"
                                            @click="openBudgetStatusModal(budget)"
                                            @keydown.enter.prevent="openBudgetStatusModal(budget)"
                                            @keydown.space.prevent="openBudgetStatusModal(budget)"
                                        >
                                            {{ statusCopy(budget.state) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-700">{{ formatCurrency(budget.total) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-3 text-slate-400">
                                            <NavLink :href="route('budgets.show', budget.id)" class="hover:text-blue-500 transition" title="Ver detalles">
                                                <InfoIcon class="w-5 h-5"/>
                                            </NavLink>
                                            <NavLink :href="route('budgets.edit', budget.id)" class="hover:text-amber-500 transition" title="Editar">
                                                <EditIcon class="w-5 h-5"/>
                                            </NavLink>
                                            <button @click="deleteBudget(budget.id)" class="hover:text-rose-500 transition" title="Eliminar">
                                                <DeleteIcon class="w-5 h-5"/>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredBudgets.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400">No hay presupuestos que coincidan con los filtros seleccionados.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <StatusUpdateModal
            :show="showBudgetStatusModal"
            title="Actualitzar estat del pressupost"
            :document-name="selectedBudget ? selectedBudget.name : ''"
            :current-status-label="selectedBudget ? statusCopy(selectedBudget.state) : ''"
            :current-status-class="selectedBudget ? statusBadgeClasses(selectedBudget.state) : ''"
            :options="budgetStatusOptions"
            v-model="budgetStatusForm.state"
            :loading="budgetStatusForm.processing"
            :confirm-disabled="!canSubmitBudgetStatus"
            :error="budgetStatusForm.errors.state"
            confirm-label="Actualitzar estat"
            @close="closeBudgetStatusModal"
            @confirm="submitBudgetStatus"
        />
    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from "@/Components/NavLink.vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import StatusUpdateModal from '@/Components/StatusUpdateModal.vue';
import { computed, reactive, ref } from 'vue';

const props = defineProps({
    budgets: Array,
    clients: Array,
});

const budgetStatusOptions = [
    {
        value: 'accepted',
        label: 'Aceptado',
        badgeClass: 'bg-emerald-100 text-emerald-700 ring-emerald-500/30',
        description: 'Confirma que el presupuesto ha sido aprobado por el cliente.',
    },
    {
        value: 'in_process',
        label: 'En proceso',
        badgeClass: 'bg-blue-100 text-blue-700 ring-blue-500/30',
        description: 'Mantén el presupuesto en revisión mientras se negocian los detalles.',
    },
    {
        value: 'rejected',
        label: 'Rechazado',
        badgeClass: 'bg-rose-100 text-rose-700 ring-rose-500/30',
        description: 'Marca el presupuesto como rechazado para registrar el resultado.',
    },
];

const showBudgetStatusModal = ref(false);
const selectedBudget = ref(null);
const budgetStatusForm = useForm({
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

const openBudgetStatusModal = (budget) => {
    selectedBudget.value = budget;
    budgetStatusForm.state = budget.state;
    budgetStatusForm.clearErrors();
    showBudgetStatusModal.value = true;
};

const closeBudgetStatusModal = () => {
    showBudgetStatusModal.value = false;
    budgetStatusForm.clearErrors();
    selectedBudget.value = null;
};

const canSubmitBudgetStatus = computed(() => {
    if (!selectedBudget.value) {
        return false;
    }

    return budgetStatusForm.state !== '' && budgetStatusForm.state !== selectedBudget.value.state;
});

const submitBudgetStatus = () => {
    if (!selectedBudget.value || !canSubmitBudgetStatus.value) {
        return;
    }

    budgetStatusForm.patch(route('budgets.updateStatus', selectedBudget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({
                only: ['budgets'],
                preserveScroll: true,
                onSuccess: () => {
                    showToast('Estat del pressupost actualitzat correctament.');
                    closeBudgetStatusModal();
                },
                onError: () => {
                    showToast('No s\'ha pogut refrescar la llista de pressupostos.', 'error');
                },
            });
        },
        onError: () => {
            showToast('No s\'ha pogut actualitzar l\'estat del pressupost.', 'error');
        },
    });
};

const filters = reactive({
    state: '',
    client_id: '',
    start_date: '',
    end_date: ''
});

const filteredBudgets = computed(() => props.budgets.filter(budget => {
    const matchesState = !filters.state || budget.state === filters.state;
    const matchesClient = !filters.client_id || budget.client_id === filters.client_id;
    const matchesStartDate = !filters.start_date || new Date(budget.date) >= new Date(filters.start_date);
    const matchesEndDate = !filters.end_date || new Date(budget.date) <= new Date(filters.end_date);

    return matchesState && matchesClient && matchesStartDate && matchesEndDate;
}));

const filteredBudgetsTotal = computed(() => filteredBudgets.value.reduce((total, budget) => total + Number(budget.total || 0), 0));

const clearFilters = () => {
    filters.state = '';
    filters.client_id = '';
    filters.start_date = '';
    filters.end_date = '';
};

const deleteBudget = (id) => {
    if (confirm("¿Estás seguro de que deseas eliminar este presupuesto?")) {
        Inertia.delete(route('budgets.destroy', id));
    }
};

const getClientName = (clientId) => {
    const client = props.clients.find(client => client.id === clientId);
    return client ? client.name : '';
};

const PrecentAcceptedBudgets = computed(() => {
    if (props.budgets.length === 0) {
        return 0;
    }

    const acceptedBudgets = props.budgets.filter(budget => budget.state === 'accepted');
    return Math.round((acceptedBudgets.length / props.budgets.length) * 100);
});

const statusCopy = (state) => {
    switch (state) {
        case 'accepted':
            return 'Aceptado';
        case 'in_process':
            return 'En proceso';
        case 'rejected':
            return 'Rechazado';
        default:
            return 'Sin estado';
    }
};

const statusBadgeClasses = (state) => {
    const base = 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold shadow-sm ring-1 ring-inset';

    switch (state) {
        case 'accepted':
            return `${base} bg-emerald-100 text-emerald-700 ring-emerald-500/30`;
        case 'in_process':
            return `${base} bg-blue-100 text-blue-700 ring-blue-500/30`;
        case 'rejected':
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
/* Estilos personalizados aquí */
</style>
