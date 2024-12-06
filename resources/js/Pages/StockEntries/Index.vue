<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Entradas de Stock</h2>
                        <p class="text-blue-300 text-2xl">{{ stockEntries.length }}</p>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
                <div class="flex flex-wrap gap-4">
                    <!-- Filtro por proveedor -->
                    <div>
                        <label for="supplierFilter" class="block text-sm text-blue-500 font-semibold">Proveedor</label>
                        <select v-model="selectedSupplier" id="supplierFilter" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm">
                            <option value="">Todos</option>
                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                        </select>
                    </div>

                    <!-- Filtro por estado -->
                    <div>
                        <label for="statusFilter" class="block text-sm text-blue-500 font-semibold">Estado</label>
                        <select v-model="selectedStatus" id="statusFilter" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm">
                            <option value="">Todos</option>
                            <option value="pending">Pendiente</option>
                            <option value="completed">Completado</option>
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

            <!-- Tabla de entradas de stock -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Referencia</th>
                        <th class="px-4 py-2 text-left">Proveedor</th>
                        <th class="px-4 py-2 text-left">Fecha de Entrada</th>
                        <th class="px-4 py-2 text-left">Total</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="entry in filteredStockEntries" :key="entry.id" class="border-t">
                        <td class="px-4 py-2">{{ entry.reference }}</td>

                        <!-- Mostrar nombre del proveedor -->
                        <td class="px-4 py-2">
                            <template v-for="supplier in suppliers" :key="supplier.id">
                                <span v-if="supplier.id === entry.supplier_id">{{ supplier.name }}</span>
                            </template>
                        </td>

                        <td class="px-4 py-2">{{ entry.entry_date }}</td>
                        <td class="px-4 py-2">{{ entry.total }}</td>

                        <!-- Mostrar estado con punto de color -->
                        <td class="px-4 py-2 flex items-center">
                            <span
                                :class="{
                                    'bg-green-500': entry.status === 'completed',
                                    'bg-yellow-500': entry.status === 'pending'
                                }"
                                class="w-3 h-3 rounded-full inline-block mr-2"
                            ></span>
                            {{ entry.status }}
                        </td>

                        <td class="px-4 py-2  space-x-5">
                           <button @click="deleteStockEntry(entry.id)" title="Eliminar" class=" p-2 rounded-full">
                               <DeleteIcon class="w-5 h-5 stroke-red-900 fill-red-300"/>
                            </button>
                            <button @click="editStockEntry(entry.id)" title="Editar" class=" p-2 rounded-full">
                                <EditIcon class="w-5 h-5 stroke-blue-900 fill-blue-300"/>
                            </button>
                            <button @click="showStockEntryDetails(entry.id)" title="Detalles" class=" p-2 rounded-full">
                                <InfoIcon class="w-5 h-5 stroke-gray-900 fill-gray-300"/>
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
import { defineProps, ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";

const props = defineProps({
    stockEntries: Array,
    suppliers: Array,
});

const selectedSupplier = ref('');
const selectedStatus = ref('');
const startDate = ref('');
const endDate = ref('');

// Filtrar las entradas de stock basadas en los filtros seleccionados
const filteredStockEntries = computed(() => {
    return props.stockEntries.filter(entry => {
        const supplierMatch = selectedSupplier.value === '' || entry.supplier_id === selectedSupplier.value;
        const statusMatch = selectedStatus.value === '' || entry.status === selectedStatus.value;

        // Convertir las fechas de las entradas de stock y los filtros a objetos Date para comparar
        const entryDate = new Date(entry.entry_date);
        const startDateMatch = startDate.value === '' || entryDate >= new Date(startDate.value);
        const endDateMatch = endDate.value === '' || entryDate <= new Date(endDate.value);

        return supplierMatch && statusMatch && startDateMatch && endDateMatch;
    });
});

const clearFilters = () => {
    selectedSupplier.value = '';
    selectedStatus.value = '';
    startDate.value = '';
    endDate.value = '';
};

const deleteStockEntry = (id) => {
    if (confirm("¿Estás seguro de que deseas eliminar esta entrada de stock?")) {
        Inertia.delete(route('stockEntries.destroy', id));
    }
};

const editStockEntry = (id) => {
    Inertia.visit(route('stockEntries.goToEdit', id));
};
const showStockEntryDetails = (id) => {
    Inertia.visit(route('stockEntries.goToShow', id));
};
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>
