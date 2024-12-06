<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Presupuestos</h2>
                        <p class="text-blue-300 text-2xl">{{ budgets.length }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Clientes</h2>
                        <p class="text-blue-300 text-2xl">{{ clients.length }}</p>
                    </div>
                    <i class="icono-cliente"></i>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Tasa de conversión</h2>
                        <p class="text-blue-300 text-2xl">{{ PrecentAcceptedBudgets }}%</p>
                    </div>
                    <i class="icono-presupuesto"></i>
                </div>
                <!-- Añade más widgets aquí si es necesario -->
            </div>
            <!-- Filtros -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
                <div class="flex flex-wrap gap-4">

                <!-- Filtro por estado -->
                <div>
                    <label class="text-blue-500 text-sm font-semibold">Estado:</label>
                    <select v-model="filters.state" class="w-full p-2 text-gray-500 border rounded-md">
                        <option value="">Todos</option>
                        <option value="accepted">Aceptado</option>
                        <option value="in_process">En proceso</option>
                        <option value="rejected">Rechazado</option>
                    </select>
                </div>

                <!-- Filtro por cliente -->
                <div>
                    <label class="text-blue-500 text-sm font-semibold">Cliente:</label>
                    <select v-model="filters.client_id" class="w-full p-2 text-gray-500 border rounded-md">
                        <option value="">Todos</option>
                        <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                    </select>
                </div>

                <!-- Filtro por fecha de inicio -->
                <div>
                    <label class="text-blue-500 text-sm font-semibold">Fecha inicio:</label>
                    <input v-model="filters.start_date" type="date" class="w-full p-2 text-gray-500 border rounded-md"/>
                </div>

                <!-- Filtro por fecha de fin -->
                <div>
                    <label class="text-blue-500 text-sm font-semibold">Fecha fin:</label>
                    <input v-model="filters.end_date" type="date" class="w-full p-2 text-gray-500 border rounded-md"/>
                </div>

                <!-- Botón Limpiar Filtros -->
                <div class="mt-6">
                    <button @click="clearFilters" class="bg-red-500 w-full hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                        Limpiar Filtros
                    </button>
                </div>
                </div>
            </div>


            <!-- Tabla de presupuestos -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('budgets.create')"
                         class="bg-blue-500 hover:text-gray-50 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Nuevo Presupuesto
                    <AddIcon class="w-6 h-6 fill-gray-200 ml-5"/>
                </NavLink>
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Identificador</th>
                        <th class="px-4 py-2 text-left">Cliente</th>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-left">Total</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="budget in filteredBudgets" :key="budget.id" class="border-t">
                        <td class="px-4 py-2">{{ budget.name }}</td>
                        <td class="px-4 py-2">
                            <span v-if="getClientName(budget.client_id)">{{ getClientName(budget.client_id) }}</span>
                        </td>
                        <td class="px-4 py-2">{{ budget.date }}</td>
                        <td class="px-4 py-2 flex items-center">
                                <span :class="{
                                    'bg-green-500': budget.state === 'accepted',
                                    'bg-yellow-500': budget.state === 'in_process',
                                    'bg-red-500': budget.state === 'rejected'
                                }" class="w-3 h-3 rounded-full inline-block mr-2"></span>
                            {{ budget.state }}
                        </td>
                        <td class="px-4 py-2">{{ budget.total }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('budgets.show', budget.id)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('budgets.edit', budget.id)" class="text-yellow-500 ml-2">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteBudget(budget.id)" class="text-red-500 ml-2">
                                <DeleteIcon class="w-5 h-5"/>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
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

// Filtros reactivos
const filters = reactive({
    state: '',
    client_id: '',
    start_date: '',
    end_date: ''
});

// Filtrar presupuestos basado en los filtros seleccionados
const filteredBudgets = computed(() => {
    return props.budgets.filter(budget => {
        const matchesState = !filters.state || budget.state === filters.state;
        const matchesClient = !filters.client_id || budget.client_id === filters.client_id;
        const matchesStartDate = !filters.start_date || new Date(budget.date) >= new Date(filters.start_date);
        const matchesEndDate = !filters.end_date || new Date(budget.date) <= new Date(filters.end_date);

        return matchesState && matchesClient && matchesStartDate && matchesEndDate;
    });
});

// Función para limpiar los filtros
const clearFilters = () => {
    filters.state = '';
    filters.client_id = '';
    filters.start_date = ''
}


const deleteBudget = (id) => {
    if (confirm("¿Estás seguro de que deseas eliminar este presupuesto?")) {
        Inertia.delete(route('budgets.destroy', id));
    }
};

// Obtener el nombre del cliente
const getClientName = (clientId) => {
    const client = props.clients.find(client => client.id === clientId);
    return client ? client.name : '';
};
// Calcula la tasa de conversión de presupuestos aceptados
const PrecentAcceptedBudgets = computed(() => {
    const acceptedBudgets = props.budgets.filter(budget => budget.state === 'accepted');
    return Math.round((acceptedBudgets.length / props.budgets.length) * 100);
});


</script>

<style scoped>
/* Estilos personalizados aquí */
</style>
