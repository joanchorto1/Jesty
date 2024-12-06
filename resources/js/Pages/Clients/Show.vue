<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">

            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Presupuestos</h2>
                        <p class="text-blue-300 text-2xl">{{ filteredBudgets.length }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Facturas</h2>
                        <p class="text-blue-300 text-2xl">{{ filteredInvoices.length }}</p>
                    </div>
                </div>
<!--                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">-->
<!--                    <div>-->
<!--                        <h2 class="text-lg text-blue-500 font-semibold">Total Clientes</h2>-->
<!--                        <p class="text-blue-300 text-2xl">{{ clients.length }}</p>-->
<!--                    </div>-->
<!--                </div>-->
            </div>

            <!-- Información del cliente -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-2xl text-blue-500 font-bold">Detalles del Cliente</h2>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <p><strong>Nombre:</strong> {{ client.name }}</p>
                    <p><strong>NIF:</strong> {{ client.nif }}</p>
                    <p><strong>Email:</strong> {{ client.email }}</p>
                    <p><strong>Teléfono:</strong> {{ client.phone }}</p>
                    <p><strong>Numero de cuenta:</strong> {{ client.bank }}</p>
                    <p><strong>Dirección:</strong> {{ client.address }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sección de presupuestos -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">Presupuestos</h3>
                    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                        <div class="flex flex-wrap gap-4 mb-4">
                            <!-- Filtro por estado -->
                            <div>
                                <label for="budgetStatusFilter" class="block text-sm text-blue-500 font-semibold">Estado</label>
                                <select v-model="selectedBudgetStatus" id="budgetStatusFilter" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm">
                                    <option value="">Todos</option>
                                    <option value="pending">Pendiente</option>
                                    <option value="approved">Aprobado</option>
                                    <option value="rejected">Rechazado</option>
                                </select>
                            </div>

                            <!-- Filtro por rango de fechas -->
                            <div>
                                <label for="budgetStartDate" class="block text-sm text-blue-500 font-semibold">Fecha de Inicio</label>
                                <input type="date" v-model="budgetStartDate" id="budgetStartDate" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" />
                            </div>

                            <div>
                                <label for="budgetEndDate" class="block text-sm text-blue-500 font-semibold">Fecha de Finalización</label>
                                <input type="date" v-model="budgetEndDate" id="budgetEndDate" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" />
                            </div>
                        </div>

                        <!-- Tabla de presupuestos -->
                        <table class="w-full table-auto">
                            <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Identificador</th>
                                <th class="px-4 py-2 text-left">Fecha</th>
                                <th class="px-4 py-2 text-left">Estado</th>
                                <th class="px-4 py-2 text-left">Total</th>
                                <th class="px-4 py-2 text-left">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="budget in filteredBudgets" :key="budget.id" class="border-t">
                                <td class="px-4 py-2">{{ budget.name }}</td>
                                <td class="px-4 py-2">{{ budget.date }}</td>
                                <td class="px-4 py-2">{{ budget.state }}</td>
                                <td class="px-4 py-2">{{ budget.total }}</td>
                                <td class="px-4 py-2">
                                    <NavLink :href="route('budgets.show', budget.id)" class="text-blue-500">
                                        <InfoIcon class="w-5 h-5" />
                                    </NavLink>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sección de facturas -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">Facturas</h3>
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <div class="flex flex-wrap gap-4 mb-4">
                            <!-- Filtro por estado -->
                            <div>
                                <label for="invoiceStatusFilter" class="block text-sm text-blue-500 font-semibold">Estado</label>
                                <select v-model="selectedInvoiceStatus" id="invoiceStatusFilter" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm">
                                    <option value="">Todos</option>
                                    <option value="paid">Pagado</option>
                                    <option value="pending">Pendiente</option>
                                    <option value="cancelled">Cancelado</option>
                                </select>
                            </div>

                            <!-- Filtro por rango de fechas -->
                            <div>
                                <label for="invoiceStartDate" class="block text-sm text-blue-500 font-semibold">Fecha de Inicio</label>
                                <input type="date" v-model="invoiceStartDate" id="invoiceStartDate" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" />
                            </div>

                            <div>
                                <label for="invoiceEndDate" class="block text-sm text-blue-500 font-semibold">Fecha de Finalización</label>
                                <input type="date" v-model="invoiceEndDate" id="invoiceEndDate" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" />
                            </div>
                        </div>

                        <!-- Tabla de facturas -->
                        <table class="w-full table-auto">
                            <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Identificador</th>
                                <th class="px-4 py-2 text-left">Fecha</th>
                                <th class="px-4 py-2 text-left">Estado</th>
                                <th class="px-4 py-2 text-left">Total</th>
                                <th class="px-4 py-2 text-left">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="invoice in filteredInvoices" :key="invoice.id" class="border-t">
                                <td class="px-4 py-2">{{ invoice.name }}</td>
                                <td class="px-4 py-2">{{ invoice.date }}</td>
                                <td class="px-4 py-2">{{ invoice.state }}</td>
                                <td class="px-4 py-2">{{ invoice.total }}</td>
                                <td class="px-4 py-2">
                                    <NavLink :href="route('invoices.show', invoice.id)" class="text-blue-500">
                                        <InfoIcon class="w-5 h-5" />
                                    </NavLink>
                                </td>
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
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from "@/Components/NavLink.vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";

const props = defineProps({
    client: Object,
    budgets: Array,
    invoices: Array,
    clients: Array,
});

const selectedBudgetStatus = ref('');
const budgetStartDate = ref('');
const budgetEndDate = ref('');
const selectedInvoiceStatus = ref('');
const invoiceStartDate = ref('');
const invoiceEndDate = ref('');

// Filtrar presupuestos
const filteredBudgets = computed(() => {
    return props.budgets.filter(budget => {
        const statusMatches = !selectedBudgetStatus.value || budget.state === selectedBudgetStatus.value;
        const startDateMatches = !budgetStartDate.value || new Date(budget.date) >= new Date(budgetStartDate.value);
        const endDateMatches = !budgetEndDate.value || new Date(budget.date) <= new Date(budgetEndDate.value);
        return statusMatches && startDateMatches && endDateMatches;
    });
});

// Filtrar facturas
const filteredInvoices = computed(() => {
    return props.invoices.filter(invoice => {
        const statusMatches = !selectedInvoiceStatus.value || invoice.state === selectedInvoiceStatus.value;
        const startDateMatches = !invoiceStartDate.value || new Date(invoice.date) >= new Date(invoiceStartDate.value);
        const endDateMatches = !invoiceEndDate.value || new Date(invoice.date) <= new Date(invoiceEndDate.value);
        return statusMatches && startDateMatches && endDateMatches;
    });
});
</script>
