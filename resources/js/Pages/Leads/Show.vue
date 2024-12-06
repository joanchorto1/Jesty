<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Oportunidades</h2>
                        <p class="text-blue-300 text-2xl">{{ filteredOpportunities.length }}</p>
                    </div>
                </div>
            </div>

            <!-- Información del cliente -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-2xl text-blue-500 font-bold">Detalles del Cliente</h2>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <p><strong>Nombre:</strong> {{ lead.name }}</p>
                    <p><strong>Empresa:</strong> {{ lead.company_name }}</p>
                    <p><strong>Email:</strong> {{ lead.email }}</p>
                    <p><strong>Teléfono:</strong> {{ lead.phone }}</p>
                    <p><strong>Posaición:</strong> {{ lead.position }}</p>
                    <p><strong>Origen:</strong> {{ lead.source }}</p>
                    <p><strong>Estado:</strong> {{ lead.status }}</p>
                </div>
                <!-- Botones de acción -->
                <div class="mt-6 flex space-x-4">
                    <!-- Editar -->
                    <NavLink :href="route('leads.edit', lead.id)" class="text-blue-500 hover:text-blue-700">
                        <EditIcon class="w-5 h-5" />
                    </NavLink>
                    <!-- Eliminar -->
                    <button @click="deleteLead" class="text-red-500 hover:text-red-700">
                        <DeleteIcon class="w-5 h-5" />
                    </button>
                    <!-- Convertir a Cliente -->
                    <button @click="convertToClient" class="text-green-500 hover:text-green-700">
                        <ConvertToInvoiceIcon class="w-5 h-5" />
                    </button>
                </div>
            </div>


            <!-- Oportunidades -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4">Oportunidades</h3>
                <div class="justify-end flex items-center">
                    <button @click="createOpportunity" class="bg-blue-500 text-white px-4 py-2 rounded-md flex space-x-5 hover:bg-blue-700">
                        <AddProductIcon class="w-5 stroke-gray-50"/> <span>Nueva Oportunidad</span>
                    </button>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                    <div class="flex flex-wrap gap-4 mb-4">
                        <!-- Filtro por estado -->
                        <div>
                            <label for="opportunityStatusFilter" class="block text-sm text-blue-500 font-semibold">Estado</label>
                            <select v-model="selectedOpportunityStatus" id="opportunityStatusFilter" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm">
                                <option value="">Todos</option>
                                <option value="open">Abierta</option>
                                <option value="closed">Cerrada</option>
                                <option value="won">Ganada</option>
                                <option value="lost">Perdida</option>
                            </select>
                        </div>

                        <!-- Filtro por rango de fechas -->
                        <div>
                            <label for="opportunityStartDate" class="block text-sm text-blue-500 font-semibold">Fecha de Inicio</label>
                            <input type="date" v-model="opportunityStartDate" id="opportunityStartDate" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" />
                        </div>

                        <div>
                            <label for="opportunityEndDate" class="block text-sm text-blue-500 font-semibold">Fecha de Finalización</label>
                            <input type="date" v-model="opportunityEndDate" id="opportunityEndDate" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" />
                        </div>
                    </div>

                    <!-- Tabla de oportunidades -->
                    <table class="w-full table-auto">


                        <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Descripción</th>
                            <th class="px-4 py-2 text-left">Fecha de Creación</th>
                            <th class="px-4 py-2 text-left">Estado</th>
                            <th class="px-4 py-2 text-left">Valor</th>
                            <th class="px-4 py-2 text-left">Probabilidad</th>
                            <th class="px-4 py-2 text-left">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="opportunity in filteredOpportunities" :key="opportunity.id" class="border-t">
                            <td class="px-4 py-2">{{ opportunity.description }}</td>
                            <td class="px-4 py-2">{{ opportunity.created_at }}</td>
                            <td class="px-4 py-2">{{ opportunity.status }}</td>
                            <td class="px-4 py-2">{{ opportunity.value }}</td>
                            <td class="px-4 py-2">{{ opportunity.probability }} %</td>
                            <td class="px-4 py-2 flex gap-2">
                                <!-- Ver detalles -->
                                <NavLink :href="route('opportunities.show', opportunity.id)" class="text-blue-500">
                                    <InfoIcon class="w-5 h-5" />
                                </NavLink>

                                <!-- Editar oportunidad -->
                                <NavLink :href="route('opportunities.edit', opportunity.id)" class="text-yellow-500">
                                    <EditIcon class="w-5 h-5" />
                                </NavLink>

                                <!-- Eliminar oportunidad -->
                                <button @click="deleteOpportunity(opportunity.id)" class="text-red-500">
                                    <DeleteIcon class="w-5 h-5" />
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
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
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import ConvertToInvoiceIcon from "@/Components/Icons/ConvertToInvoiceIcon.vue";
import {Inertia} from "@inertiajs/inertia";
import AddProductIcon from "@/Components/Icons/AddProductIcon.vue";

const props = defineProps({
    lead: Object,
    opportunities: Array,
});

const selectedOpportunityStatus = ref('');
const opportunityStartDate = ref('');
const opportunityEndDate = ref('');

// Filtrar oportunidades
const filteredOpportunities = computed(() => {
    return props.opportunities.filter(opportunity => {
        const statusMatches = !selectedOpportunityStatus.value || opportunity.status === selectedOpportunityStatus.value;
        const startDateMatches = !opportunityStartDate.value || new Date(opportunity.created_at) >= new Date(opportunityStartDate.value);
        const endDateMatches = !opportunityEndDate.value || new Date(opportunity.created_at) <= new Date(opportunityEndDate.value);
        return statusMatches && startDateMatches && endDateMatches;
    });
});

// Funciones para las acciones
const deleteOpportunity = (id) => {
Inertia.delete(route('opportunities.destroy', id));
};
const createOpportunity = () => {
Inertia.visit(route('opportunities.goToCreateWL', {lead: props.lead.id}));
};

const deleteLead = () => {
if (confirm('¿Estás seguro de que deseas eliminar este cliente?')) {
Inertia.delete(route('leads.destroy', props.lead.id));
}
};

const convertToClient = () => {
if (confirm('¿Estás seguro de que deseas convertir este cliente en un cliente?')) {
Inertia.post(route('leads.convertToClient', props.lead.id));
}
};




</script>

