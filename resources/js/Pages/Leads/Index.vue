<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Leads</h2>
                        <p class="text-blue-300 text-2xl">{{ totalLeads }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Leads Activos</h2>
                        <p class="text-blue-300 text-2xl">{{ activeLeads }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Leads Inactivos</h2>
                        <p class="text-blue-300 text-2xl">{{ inactiveLeads }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('leads.create')" class="bg-blue-500 hover:text-gray-50 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Agregar nuevo Lead
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
                    <tr v-for="lead in filteredLeads" :key="lead.id" class="border-t">
                        <td class="px-4 py-2">{{ lead.name }}</td>
                        <td class="px-4 py-2">{{ lead.email }}</td>
                        <td class="px-4 py-2">{{ lead.phone }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('leads.show', lead.id)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('leads.edit', lead.id)" class="text-yellow-500">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteLead(lead.id)" class="text-red-500 ml-2">
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
import { computed, reactive } from 'vue';
import { defineProps } from 'vue';

const props = defineProps({
    leads: Array,
});

const filteredLeads = props.leads;
const totalLeads = computed(() => filteredLeads.length);
const activeLeads = computed(() => filteredLeads.filter(lead => lead.status === 'active').length);
const inactiveLeads = computed(() => totalLeads.value - activeLeads.value);


const deleteLead = (leadId) => {
    if (confirm("¿Estás seguro de que deseas eliminar este lead?")) {
        Inertia.delete(route('leads.destroy', leadId));
    }
};

</script>
