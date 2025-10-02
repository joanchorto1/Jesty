<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-emerald-600 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div>
                            <p class="text-emerald-200 text-sm uppercase tracking-widest">Gestión de clientes</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white">Clientes de la compañía</h1>
                            <p class="text-sm text-emerald-200 mt-2">Consulta, filtra y administra tu cartera de clientes desde un único lugar.</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <NavLink :href="route('clients.create')" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition">
                                <AddIcon class="w-4 h-4" /> Nuevo cliente
                            </NavLink>
                            <NavLink :href="route('dashboard.clients')" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition">
                                <DashboardIcon class="w-4 h-4" /> Ver dashboard
                            </NavLink>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <SummaryCard v-for="card in summaryCards" :key="card.eyebrow" :eyebrow="card.eyebrow" :value="card.value" :description="card.description" />
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <Panel title="Filtros" description="Refina la lista aplicando varios criterios.">
                    <template #actions>
                        <button @click="resetFilters" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Reiniciar filtros
                        </button>
                    </template>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div v-for="field in filterFields" :key="field.id">
                            <label :for="field.id" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">{{ field.label }}</label>
                            <component
                                :is="field.component"
                                :id="field.id"
                                v-model="field.model.value"
                                v-bind="field.props"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            >
                                <option v-for="option in field.options" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </component>
                        </div>
                    </div>
                </Panel>

                <Panel title="Listado de clientes" description="Accede rápidamente al detalle, edición y eliminación." :header-border="true">
                    <DataTable
                        :columns="tableColumns"
                        :items="filteredClients"
                        empty-message="No hay clientes que coincidan con los criterios actuales."
                        caption="Tabla de clientes"
                    >
                        <template #row="{ item: client }">
                            <td class="py-4 font-semibold text-slate-700">#{{ client.id }}</td>
                            <td class="py-4">
                                <p class="font-medium text-slate-700">{{ client.name }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ statusCopy(client.status) }}</p>
                            </td>
                            <td class="py-4">{{ client.email || '—' }}</td>
                            <td class="py-4">{{ client.phone || '—' }}</td>
                            <td class="py-4">{{ client.nif || '—' }}</td>
                            <td class="py-4">
                                <div class="flex items-center justify-end gap-3 text-slate-500">
                                    <NavLink :href="route('clients.show', client.id)" class="hover:text-blue-500 transition" aria-label="Ver cliente">
                                        <InfoIcon class="w-5 h-5" />
                                    </NavLink>
                                    <NavLink :href="route('clients.edit', client.id)" class="hover:text-amber-500 transition" aria-label="Editar cliente">
                                        <EditIcon class="w-5 h-5" />
                                    </NavLink>
                                    <button type="button" @click="deleteClient(client.id)" class="hover:text-rose-500 transition" aria-label="Eliminar cliente">
                                        <DeleteIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </td>
                        </template>
                    </DataTable>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import SummaryCard from '@/Components/UI/SummaryCard.vue';
import Panel from '@/Components/UI/Panel.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import InfoIcon from '@/Components/Icons/InfoIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import DashboardIcon from '@/Components/Icons/DashboardIcon.vue';

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
});

const nameFilter = ref('');
const phoneFilter = ref('');
const nifFilter = ref('');
const statusFilter = ref('');

const filterFields = [
    {
        id: 'nameFilter',
        label: 'Nombre',
        model: nameFilter,
        component: 'input',
        props: { type: 'text', placeholder: 'Introduce un nombre' },
    },
    {
        id: 'phoneFilter',
        label: 'Teléfono',
        model: phoneFilter,
        component: 'input',
        props: { type: 'text', placeholder: 'Introduce un teléfono' },
    },
    {
        id: 'nifFilter',
        label: 'NIF',
        model: nifFilter,
        component: 'input',
        props: { type: 'text', placeholder: 'Introduce un NIF' },
    },
    {
        id: 'statusFilter',
        label: 'Estado',
        model: statusFilter,
        component: 'select',
        props: {},
        options: [
            { value: '', label: 'Todos' },
            { value: 'active', label: 'Activo' },
            { value: 'inactive', label: 'Inactivo' },
            { value: 'unknown', label: 'Sin estado' },
        ],
    },
];

const filteredClients = computed(() => {
    return props.clients.filter(client => {
        const nameMatches = !nameFilter.value || (client.name ?? '').toLowerCase().includes(nameFilter.value.toLowerCase());
        const phoneMatches = !phoneFilter.value || (client.phone ?? '').includes(phoneFilter.value);
        const nifMatches = !nifFilter.value || (client.nif ?? '').toLowerCase().includes(nifFilter.value.toLowerCase());
        const statusValue = client.status ?? 'unknown';
        const statusMatches = !statusFilter.value || statusFilter.value === statusValue;
        return nameMatches && phoneMatches && nifMatches && statusMatches;
    });
});

const activeClients = computed(() => props.clients.filter(client => client.status === 'active').length);
const inactiveClients = computed(() => props.clients.filter(client => client.status === 'inactive').length);

const summaryCards = computed(() => [
    {
        eyebrow: 'Clientes totales',
        value: props.clients.length,
        description: `${activeClients.value} activos`,
    },
    {
        eyebrow: 'Clientes inactivos',
        value: inactiveClients.value,
        description: `${Math.round(props.clients.length ? (inactiveClients.value / props.clients.length) * 100 : 0)}% tasa de baja`,
    },
    {
        eyebrow: 'Datos de contacto',
        value: filteredClients.value.filter(client => client.email || client.phone).length,
        description: 'Clientes con correo o teléfono registrado',
    },
    {
        eyebrow: 'Clientes sin estado',
        value: props.clients.filter(client => !client.status).length,
        description: 'Pendientes de clasificar',
    },
]);

const tableColumns = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Nombre' },
    { key: 'email', label: 'Correo' },
    { key: 'phone', label: 'Teléfono' },
    { key: 'nif', label: 'NIF' },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

function resetFilters() {
    nameFilter.value = '';
    phoneFilter.value = '';
    nifFilter.value = '';
    statusFilter.value = '';
}

function deleteClient(id) {
    if (confirm('¿Deseas eliminar este cliente?')) {
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
