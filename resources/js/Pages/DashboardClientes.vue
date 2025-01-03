<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Clientes</h2>
                        <p class="text-blue-300 text-2xl">{{ filteredClients.length }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Clientes Activos</h2>
                        <p class="text-blue-300 text-2xl">{{ activeClients }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Clientes Inactivos</h2>
                        <p class="text-blue-300 text-2xl">{{ inactiveClients }}</p>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
                <div class="flex flex-wrap gap-4">
                   <!-- Filtro por nombre -->
                    <div>
                        <label for="nameFilter" class="block text-sm text-blue-500 font-semibold">Nombre</label>
                        <input type="text" v-model="nameFilter" id="nameFilter" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" placeholder="Filtrar por nombre" />
                    </div>

                    <!-- Filtro por teléfono -->
                    <div>
                        <label for="phoneFilter" class="block text-sm text-blue-500 font-semibold">Teléfono</label>
                        <input type="text" v-model="phoneFilter" id="phoneFilter" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" placeholder="Filtrar por teléfono" />
                    </div>

                    <!-- Filtro por NIF -->
                    <div>
                        <label for="nifFilter" class="block text-sm text-blue-500 font-semibold">NIF</label>
                        <input type="text" v-model="nifFilter" id="nifFilter" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" placeholder="Filtrar por NIF" />
                    </div>
                    <!-- Botón para limpiar filtros -->
                    <button @click="clearFilters" class="mt-6 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Limpiar Filtros
                    </button>
                </div>
            </div>

            <!-- Tabla de clientes -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('clients.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Nuevo Cliente
                    <AddIcon class="w-6 h-6 fill-gray-200 ml-5"/>
                </NavLink>
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Identificador</th>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Correo</th>
                        <th class="px-4 py-2 text-left">Teléfono</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="client in filteredClients" :key="client.id" class="border-t">
                        <td class="px-4 py-2">{{ client.id }}</td>
                        <td class="px-4 py-2">{{ client.name }}</td>
                        <td class="px-4 py-2">{{ client.email }}</td>
                        <td class="px-4 py-2">{{ client.phone }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('clients.show', client.id)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('clients.edit', client.id)" class="text-yellow-500 ml-2">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteClient(client.id)" class="text-red-500 ml-2">
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
import { ref, computed } from "vue";

const props = defineProps({
    clients: Array,
    invoices: Array,
});

const selectedClient = ref('');
const selectedStatus = ref('');
const startDate = ref('');
const endDate = ref('');
const nameFilter = ref('');
const phoneFilter = ref('');
const nifFilter = ref('');

// Filtrar los clientes basados en los filtros seleccionados
const filteredClients = computed(() => {
    return props.clients.filter(client => {
        const clientMatch = selectedClient.value === '' || client.id === selectedClient.value;
        const nameMatch = !nameFilter.value || client.name.toLowerCase().includes(nameFilter.value.toLowerCase());
        const phoneMatch = !phoneFilter.value || client.phone.includes(phoneFilter.value);
        const nifMatch = !nifFilter.value || client.nif.includes(nifFilter.value);

        return clientMatch && nameMatch && phoneMatch && nifMatch;
    });
});

// Limpiar los filtros
const clearFilters = () => {
    selectedClient.value = '';
    selectedStatus.value = '';
    startDate.value = '';
    endDate.value = '';
    nameFilter.value = '';
    phoneFilter.value = '';
    nifFilter.value = '';
};

// Eliminar cliente
const deleteClient = (id) => {
    if (confirm("¿Estás seguro de que deseas eliminar este cliente?")) {
        Inertia.delete(route('clients.destroy', id));
    }
};

// Calcular la tasa de facturas pagadas (opcional si necesitas algún dato relacionado)
const PrecentPaidInvoices = computed(() => {
    const paidInvoices = props.invoices.filter(invoice => invoice.state === 'paid');
    return Math.round((paidInvoices.length / props.invoices.length) * 100);
});

// Calcular la cantidad de clientes activos
const activeClients = computed(() => {
    return props.clients.filter(client => client.status === 'active').length;
});

// Calcular la cantidad de clientes inactivos
const inactiveClients = computed(() => {
    return props.clients.filter(client => client.status === 'inactive').length;
});
</script>
