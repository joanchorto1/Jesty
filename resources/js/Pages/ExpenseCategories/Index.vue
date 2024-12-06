<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Categorías de Gasto</h2>
                        <p class="text-blue-300 text-2xl">{{ categories.length }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabla de categorías de gastos -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('expenseCategories.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Nueva Categoría
                    <AddIcon class="w-6 h-6 fill-gray-200 ml-5"/>
                </NavLink>
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Identificador</th>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="category in categories" :key="category.id" class="border-t">
                        <td class="px-4 py-2">{{ category.id }}</td>
                        <td class="px-4 py-2">{{ category.name }}</td>
                        <td class="px-4 py-2">{{ category.description }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('expenseCategories.show', category.id)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('expenseCategories.edit', category.id)" class="text-yellow-500 ml-2">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteCategory(category.id)" class="text-red-500 ml-2">
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
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";

import AddIcon from "@/Components/Icons/AddIcon.vue";
import NavLink from "@/Components/NavLink.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";

const props = defineProps({
    categories: Array,
});

const deleteCategory = (id) => {
    if (confirm("¿Estás seguro de que deseas eliminar esta categoría?")) {
        Inertia.delete(route('expenseCategories.destroy', id));
    }
};
</script>
