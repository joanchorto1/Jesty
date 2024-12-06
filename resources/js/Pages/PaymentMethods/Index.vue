<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Métodos de Pago</h2>
                        <p class="text-blue-300 text-2xl">{{ paymentMethods.length }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabla de métodos de pago -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('paymentMethods.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Nuevo Método
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
                    <tr v-for="method in paymentMethods" :key="method.id" class="border-t">
                        <td class="px-4 py-2">{{ method.id }}</td>
                        <td class="px-4 py-2">{{ method.name }}</td>
                        <td class="px-4 py-2">{{ method.description }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('paymentMethods.show', method.id)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('paymentMethods.edit', method.id)" class="text-yellow-500 ml-2">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteMethod(method.id)" class="text-red-500 ml-2">
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

const props = defineProps({
    paymentMethods: Array,
});

const deleteMethod = (id) => {
    if (confirm("¿Estás seguro de que deseas eliminar este método de pago?")) {
        Inertia.delete(route('paymentMethods.destroy', id));
    }
};
</script>
