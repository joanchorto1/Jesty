<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Proveedores</h2>
                        <p class="text-blue-300 text-2xl">{{ totalSuppliers }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabla de proveedores -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <!-- Botón para crear un nuevo proveedor -->
                <NavLink :href="route('suppliers.create')" class="bg-blue-500 hover:text-gray-50 text-white font-bold py-2 px-4 rounded mb-4 inline-flex items-center">
                    Agregar nuevo Proveedor
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
                    <tr v-for="supplier in suppliers" :key="supplier.id" class="border-t">
                        <td class="px-4 py-2">{{ supplier.name }}</td>
                        <td class="px-4 py-2">{{ supplier.email || 'N/A' }}</td>
                        <td class="px-4 py-2">{{ supplier.phone || 'N/A' }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('suppliers.show', supplier.id)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('suppliers.edit', supplier.id)" class="text-yellow-500">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteSupplier(supplier.id)" class="text-red-500 ml-2">
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
    suppliers: Array,
});

// Datos reactivos
const totalSuppliers = computed(() => props.suppliers.length);

// Filtrar proveedores por compañía


// Método para eliminar un proveedor
const deleteSupplier = (supplierId) => {
    if (confirm("¿Estás seguro de que deseas eliminar este proveedor?")) {
        Inertia.delete(route('suppliers.delete', supplierId));
    }
};

// Al crear el componente
</script>

<style scoped>
/* Estilos personalizados aquí */
</style>
