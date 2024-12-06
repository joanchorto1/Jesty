<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md mb-6 flex justify-between items-center">
                <h1 class="text-lg text-blue-500 font-semibold">Planes</h1>
                <NavLink :href="route('plans.create')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    <AddIcon class="w-5 h-5 inline-block mr-2" /> Nuevo Plan
                </NavLink>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="plan in plans" :key="plan.id" class="border-t">
                        <td class="px-4 py-2">{{ plan.name }}</td>
                        <td class="px-4 py-2">{{ plan.description }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('plans.show', plan.id)" class="text-blue-500 mr-2">
                                <InfoIcon class="w-5 h-5" />
                            </NavLink>
                            <NavLink :href="route('plans.edit', plan.id)" class="text-yellow-500 mr-2">
                                <EditIcon class="w-5 h-5" />
                            </NavLink>
                            <button @click="deletePlan(plan.id)" class="text-red-500">
                                <DeleteIcon class="w-5 h-5" />
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

const props = defineProps({
    plans: Array,
});

const deletePlan = (planId) => {
    if (confirm("¿Estás seguro de que deseas eliminar este plan?")) {
        Inertia.delete(route('plans.destroy', planId));
    }
};
</script>

<style scoped>
/* Estilos personalizados aquí */
</style>
