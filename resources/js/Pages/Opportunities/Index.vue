<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <!-- Estadísticas generales -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Oportunidades</h2>
                        <p class="text-blue-300 text-2xl">{{ totalOpportunities }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Oportunidades Abiertas</h2>
                        <p class="text-blue-300 text-2xl">{{ openOpportunities }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Oportunidades Ganadas</h2>
                        <p class="text-blue-300 text-2xl">{{ wonOpportunities }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabla de oportunidades -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('opportunities.create')" class="bg-blue-500 hover:text-gray-50 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Crear nueva Oportunidad
                    <AddIcon class="w-6 h-6 fill-gray-200 ml-3"/>
                </NavLink>

                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Lead</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-left">Probabilidad</th>

                        <th class="px-4 py-2 text-left">Valor</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="opportunity in opportunities" :key="opportunity.id" class="border-t">
                        <template v-for="lead in props.leads">

                        <td v-if="opportunity.lead_id === lead.id" class="px-4 py-2">{{ lead.name }}</td>

                        </template>
                        <td class="px-4 py-2">{{ opportunity.description }}</td>
                        <td class="px-4 py-2">{{ opportunity.probability }}%</td>
                        <td class="px-4 py-2">{{ opportunity.value }}</td>
                        <td class="px-4 py-2">{{ opportunity.status }}</td>
                        <td class="px-4 py-2 flex items-center">
                            <NavLink :href="route('opportunities.show', opportunity.id)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('opportunities.edit', opportunity.id)" class="text-yellow-500 ml-2">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteOpportunity(opportunity.id)" class="text-red-500 ml-2">
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
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import NavLink from "@/Components/NavLink.vue";
import { computed } from 'vue';
import { defineProps } from 'vue';

const props = defineProps({
    opportunities: Object,
    leads: Array,
});
const opportunities = props.opportunities;
const totalOpportunities = computed(() =>opportunities.length);
const openOpportunities = computed(() =>opportunities.filter(o => o.status === 'Abierta').length);
const wonOpportunities = computed(() =>opportunities.filter(o => o.status === 'Ganada').length);

const deleteOpportunity = (opportunityId) => {
    if (confirm("¿Estás seguro de que deseas eliminar esta oportunidad?")) {
        Inertia.delete(route('opportunities.destroy', opportunityId));

    }
};
</script>
