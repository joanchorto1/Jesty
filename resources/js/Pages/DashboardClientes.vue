<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Clientes</h2>
                        <p class="text-blue-300 text-2xl">{{ totalClients }}</p>
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

            <!-- Tabla de clientes -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <!-- Botón para crear un nuevo cliente -->
                <NavLink :href="route('clients.create')" class="bg-blue-500 hover:text-gray-50 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Agregar nuevo Cliente
                    <AddIcon class="w-6 h-6 fill-gray-200 ml-3"/>
                </NavLink>
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Teléfono</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="client in filteredClients" :key="client.id" class="border-t">
                        <td class="px-4 py-2">{{ client.name }}</td>
                        <td class="px-4 py-2">{{ client.email }}</td>
                        <td class="px-4 py-2">{{ client.phone }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('clients.show', client.id)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('clients.edit', client.id)" class="text-yellow-500">
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
import AppLayout from "@/Layouts/AppLayout.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import NavLink from "@/Components/NavLink.vue";
import { computed, reactive } from 'vue';
import InfoIcon from "@/Components/Icons/InfoIcon.vue";

const props = defineProps({
    auth: Object,
    clients: Array,
});

// Datos reactivos
const filteredClients = reactive(props.clients);
const totalClients = computed(() => filteredClients.length);
const activeClients = computed(() => filteredClients.filter(client => client.status === 'active').length);
const inactiveClients = computed(() => totalClients.value - activeClients.value);

// Filtrar clientes

// Método para eliminar un cliente
const deleteClient = (clientId) => {
    if (confirm("¿Estás seguro de que deseas eliminar este cliente?")) {
        Inertia.delete(route('clients.destroy', clientId));
    }
};

// Al crear el componente
</script>

<style scoped>
/* Estilos personalizados aquí */
</style>
