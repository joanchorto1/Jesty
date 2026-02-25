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
                    title="Resumen de presupuestos"
                    description="Controla el rendimiento comercial y haz seguimiento de los presupuestos para convertir más oportunidades en ventas."
                    :icon="MenuBudgetIcon"
                >
                    <template #actions>
                        <div class="flex flex-wrap items-center gap-3">
                            <NavLink :href="route('budgets.create')" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                                <AddIcon class="w-5 h-5" />
                                Nuevo presupuesto
                            </NavLink>
                            <a :href="route('migration.export.budgets')" class="inline-flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-100">
                                Exportar CSV
                            </a>
                        </div>
                    </template>
                </CrudPageHeader>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <CrudStatCard label="Presupuestos activos" :value="filteredBudgets.length" :icon="MenuBudgetIcon" icon-background="bg-sky-500/10 text-sky-600">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ budgets.length }} totales registrados</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Clientes" :value="clients.length" :icon="InfoIcon" icon-background="bg-indigo-500/10 text-indigo-600">
                        <template #description>
                            <p class="text-xs text-slate-500">Relacionados con presupuestos</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Tasa de conversión" :value="`${PrecentAcceptedBudgets}%`" :icon="AddIcon" icon-background="bg-emerald-500/10 text-emerald-600">
                        <template #description>
                            <p class="text-xs text-slate-500">Presupuestos aceptados frente al total</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Importe filtrado" :value="formatCurrency(filteredBudgetsTotal)" :icon="EditIcon" icon-background="bg-amber-500/10 text-amber-600">
                        <template #description>
                            <p class="text-xs text-slate-500">Suma de los presupuestos visibles</p>
                        </template>
                    </CrudStatCard>
                </div>

                <CrudFilterBar>
                    <div class="grid flex-1 grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Estado</label>
                            <select v-model="filters.state" class="w-full rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200/40">
                                <option value="">Todos</option>
                                <option value="accepted">Aceptado</option>
                                <option value="in_process">En proceso</option>
                                <option value="rejected">Rechazado</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Cliente</label>
                            <select v-model="filters.client_id" class="w-full rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200/40">
                                <option value="">Todos</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Fecha inicio</label>
                            <input v-model="filters.start_date" type="date" class="w-full rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200/40"/>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Fecha fin</label>
                            <input v-model="filters.end_date" type="date" class="w-full rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200/40"/>
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
                    <tr v-for="budget in filteredBudgets" :key="budget.id" class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ budget.name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ getClientName(budget.client_id) || '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(budget.date) }}</td>
                        <td class="px-6 py-4 text-sm">
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
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ formatCurrency(budget.total) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-3 text-slate-500">
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
                </CrudTable>
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
import CrudFilterBar from '@/Components/Crud/CrudFilterBar.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import CrudTable from '@/Components/Crud/CrudTable.vue';
import NavLink from "@/Components/NavLink.vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import MenuBudgetIcon from "@/Components/Icons/MenuBudgetIcon.vue";
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

const filteredBudgets = computed(() => props.budgets
    .filter(budget => {
        const matchesState = !filters.state || budget.state === filters.state;
        const matchesClient = !filters.client_id || budget.client_id === filters.client_id;
        const matchesStartDate = !filters.start_date || new Date(budget.date) >= new Date(filters.start_date);
        const matchesEndDate = !filters.end_date || new Date(budget.date) <= new Date(filters.end_date);

        return matchesState && matchesClient && matchesStartDate && matchesEndDate;
    })
    .sort((a, b) => new Date(b.date || 0) - new Date(a.date || 0))
);

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
