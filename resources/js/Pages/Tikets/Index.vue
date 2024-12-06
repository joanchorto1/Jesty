<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <!-- Sección de Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Tickets</h2>
                        <p class="text-blue-300 text-2xl">{{ totalTickets }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Tickets Hoy</h2>
                        <p class="text-blue-300 text-2xl">{{ ticketsToday }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Ingreso Total</h2>
                        <p class="text-blue-300 text-2xl">{{ totalIncome }} €</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Promedio por Ticket</h2>
                        <p class="text-blue-300 text-2xl">{{ averageTicket }} €</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Filtros -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
                <div class="flex flex-wrap gap-4">
                    <div>
                        <label class="text-blue-500 text-sm font-semibold">Fecha de Inicio:</label>
                        <input type="date" v-model="startDate" class="w-full p-2 text-gray-500 border rounded-md" />
                    </div>
                    <div>
                        <label class="text-blue-500 text-sm font-semibold">Fecha de Fin:</label>
                        <input type="date" v-model="endDate" class="w-full p-2 text-gray-500 border rounded-md" />
                    </div>
                    <div class="mt-6">
                        <button @click="removeFilters" class="bg-red-500 w-full hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
Limpiar Filtro                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de Tickets -->
            <h2 class="text-lg text-blue-500 font-semibold mb-4">Listado de Tickets</h2>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <ul>
                    <li v-for="ticket in filteredTickets" :key="ticket.id" class="mb-2 flex justify-between p-4 border-b">
                        <div>
                            <span class="font-bold">Ticket ID: {{ ticket.id }}</span>
                            <span class="ml-2">Fecha: {{ new Date(ticket.created_at).toLocaleString() }}</span>
                            <span class="ml-2">Total: {{ ticket.total }} €</span>
                        </div>
                        <div class="space-x-5">
                            <button @click="print(ticket.id)">
                                <PrintIcon class="w-5 h-5"/>
                            </button>
                            <button @click="editTicket(ticket.id)" >
                                <EditIcon class="w-5 h-5"/>
                            </button>
                            <button @click="deleteTicket(ticket.id)" >
                                <DeleteIcon class="w-5 h-5"/>
                            </button>

                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import { Inertia } from '@inertiajs/inertia';
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import PrintIcon from "@/Components/Icons/PrintIcon.vue";

// Recibiendo tickets mediante props
const props = defineProps({
    tickets: Array,
});

// Estado local
const startDate = ref('');
const endDate = ref('');

// Computed para contar tickets y calcular ingresos
const totalTickets = computed(() => props.tickets.length);
const ticketsToday = computed(() => {
    const today = new Date().toISOString().split('T')[0];
    return props.tickets.filter(ticket => new Date(ticket.created_at).toISOString().split('T')[0] === today).length;
});
const totalIncome = computed(() => props.tickets.reduce((sum, ticket) => sum + ticket.total, 0).toFixed(2));
const averageTicket = computed(() => {
    const average = totalIncome.value / (totalTickets.value || 1);
    return average.toFixed(2);
});

// Computed para filtrar tickets por fecha
const filteredTickets = computed(() => {
    return props.tickets.filter(ticket => {
        const ticketDate = new Date(ticket.created_at);
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        return (!startDate.value || ticketDate >= start) && (!endDate.value || ticketDate <= end);
    });
});

// Métodos
const removeFilters = () => {
    // Lógica para generar un informe basado en las fechas seleccionadas
    startDate.value = '';
    endDate.value = '';
};

const editTicket = (ticket) => {
    // Redirigir a la vista de edit del ticket
    Inertia.get(route('tikets.goToEdit', ticket));
};

const deleteTicket = (ticketId) => {
    // Lógica para eliminar el ticket
    Inertia.delete(route('tikets.destroy', ticketId));
    // Aquí puedes implementar la lógica para enviar una solicitud de eliminación a la API
};

const print  = (ticketId) => {
    Inertia.get(route('tikets.print', ticketId));
};
</script>

