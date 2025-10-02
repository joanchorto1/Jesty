<template>
    <AppLayout>
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
                                        <span :class="statusBadgeClasses(budget.state)">{{ statusCopy(budget.state) }}</span>
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
import { computed, reactive } from 'vue';

const props = defineProps({
    budgets: Array,
    clients: Array,
});

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
    switch (state) {
        case 'accepted':
            return 'inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700';
        case 'in_process':
            return 'inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700';
        case 'rejected':
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
/* Estilos personalizados aquí */
</style>
