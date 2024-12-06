<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6" >
            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Facturas</h2>
                        <p class="text-blue-300 text-2xl">{{ filteredInvoices.length }}</p>
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
                        <h2 class="text-lg text-blue-500 font-semibold">Tasa de pagos completos</h2>
                        <p class="text-blue-300 text-2xl">{{ PrecentPaidInvoices }}%</p>
                    </div>
                    <i class="icono-factura"></i>
                </div>
            </div>

            <!-- Filtros -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
                <div class="flex flex-wrap gap-4">
                    <!-- Filtro por cliente -->
                    <div>
                        <label for="clientFilter" class="block text-sm text-blue-500 font-semibold">Cliente</label>
                        <select v-model="selectedClient" id="clientFilter" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm">
                            <option value="">Todos</option>
                            <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                        </select>
                    </div>

                    <!-- Filtro por estado -->
                    <div >
                        <label for="statusFilter" class="block text-sm text-blue-500 font-semibold">Estado</label>
                        <select v-model="selectedStatus" id="statusFilter" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm">
                            <option value="">Todos</option>
                            <option value="paid">Pagado</option>
                            <option value="pending">Pendiente</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                    </div>

                    <!-- Filtro por rango de fechas -->
                    <div>
                        <label for="startDateFilter" class="block text-sm text-blue-500 font-semibold">Fecha de Inicio</label>
                        <input type="date" v-model="startDate" id="startDateFilter" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" />
                    </div>

                    <div>
                        <label for="endDateFilter" class="block text-sm text-blue-500 font-semibold">Fecha de Finalización</label>
                        <input type="date" v-model="endDate" id="endDateFilter" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" />
                    </div>


                    <!-- Botón para limpiar filtros -->
                    <button @click="clearFilters" class="mt-6 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Limpiar Filtros
                    </button>
                </div>
            </div>

            <!-- Tabla de facturas -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('invoices.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Nueva Factura
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
                    <tr v-for="invoice in filteredInvoices" :key="invoice.id" class="border-t">
                        <td class="px-4 py-2">{{ invoice.name }}</td>

                        <!-- Mostrar nombre del cliente -->
                        <td class="px-4 py-2">
                            <template v-for="client in clients" :key="client.id">
                                <span v-if="client.id === invoice.client_id">{{ client.name }}</span>
                            </template>
                        </td>

                        <td class="px-4 py-2">{{ invoice.date }}</td>

                        <!-- Mostrar estado con punto de color -->
                        <td class="px-4 py-2 flex items-center">
                            <span
                                :class="{
                                    'bg-green-500': invoice.state === 'paid',
                                    'bg-yellow-500': invoice.state === 'pending',
                                    'bg-red-500': invoice.state === 'cancelled'
                                }"
                                class="w-3 h-3 rounded-full inline-block mr-2"
                            ></span>
                            {{ invoice.state }}
                        </td>

                        <td class="px-4 py-2">{{ invoice.total }}</td>

                        <!-- Acciones -->
                        <td class="px-4 py-2">
                            <NavLink :href="route('invoices.show', invoice.id)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('invoices.edit', invoice.id)" class="text-yellow-500 ml-2">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteInvoice(invoice.id)" class="text-red-500 ml-2">
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
    invoices: Array,
    clients: Array,
});

const selectedClient = ref('');
const selectedStatus = ref('');
const startDate = ref('');
const endDate = ref('');


// Filtrar las facturas basadas en los filtros seleccionados
const filteredInvoices = computed(() => {
    return props.invoices.filter(invoice => {
        const clientMatch = selectedClient.value === '' || invoice.client_id === selectedClient.value;
        const statusMatch = selectedStatus.value === '' || invoice.state === selectedStatus.value;

        // Convertir las fechas de las facturas y los filtros a objetos Date para comparar
        const invoiceDate = new Date(invoice.date);
        const startDateMatch = startDate.value === '' || invoiceDate >= new Date(startDate.value);
        const endDateMatch = endDate.value === '' || invoiceDate <= new Date(endDate.value);

        return clientMatch && statusMatch && startDateMatch && endDateMatch;
    });
});


const clearFilters = () => {
    selectedClient.value = '';
    selectedStatus.value = '';
    startDate.value = '';
    endDate.value = '';
};

const deleteInvoice = (id) => {
    if (confirm("¿Estás seguro de que deseas eliminar esta factura?")) {
        Inertia.delete(route('invoices.destroy', id));
    }
};

// Calcula la tasa de conversión de facturas pagadas
const PrecentPaidInvoices = computed(() => {
    const paidInvoices = props.invoices.filter(invoice => invoice.state === 'paid');
    return Math.round((paidInvoices.length / props.invoices.length) * 100);
});
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>
