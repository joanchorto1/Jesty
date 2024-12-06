<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">

            <!-- Información del proveedor -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-2xl text-blue-500 font-bold">Detalles del Proveedor</h2>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <p><strong>Nombre:</strong> {{ supplier.name }}</p>
                    <p><strong>Email:</strong> {{ supplier.email || 'N/A' }}</p>
                    <p><strong>Teléfono:</strong> {{ supplier.phone || 'N/A' }}</p>
                    <p><strong>Dirección:</strong> {{ supplier.address || 'N/A' }}</p>
                </div>
                <div class="mt-6">
                    <NavLink :href="route('stockEntries.create',supplier.id)" class="bg-blue-500 items-center justify-center pt-2 flex space-x-5 text-white py-2 px-4 rounded-md shadow-md hover:bg-blue-600">
                        <AddIcon class="w-5 "/>
                        <p>Nueva Entrada de Stock</p>
                    </NavLink>
                </div>
            </div>

            <!-- Sección de entradas de stock -->
            <div class="">
                <!-- Sección de entradas de stock -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">Entradas de Stock</h3>
                    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                        <!-- Filtro de entradas -->
                        <div class="flex flex-wrap gap-4 mb-4">
                            <div>
                                <label for="entryStartDate" class="block text-sm text-blue-500 font-semibold">Fecha de Inicio</label>
                                <input type="date" v-model="entryStartDate" id="entryStartDate" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" />
                            </div>

                            <div>
                                <label for="entryEndDate" class="block text-sm text-blue-500 font-semibold">Fecha de Finalización</label>
                                <input type="date" v-model="entryEndDate" id="entryEndDate" class="mt-1 block w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md shadow-sm" />
                            </div>
                            <div>
                                <button class="bg-red-500 p-2 rounded-md block text-gray-50 font-bold mt-6" @click="deleteFilters()">
                                    Limpiar Filtros
                                </button>
                            </div>
                        </div>

                        <!-- Tabla de entradas de stock -->
                        <table class="w-full table-auto">
                            <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Referencia</th>
                                <th class="px-4 py-2 text-left">Fecha de Entrada</th>
                                <th class="px-4 py-2 text-left">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="entry in filteredStockEntries" :key="entry.id" class="border-t">
                                <td class="px-4 py-2">{{ entry.reference }}</td>
                                <td class="px-4 py-2">{{ entry.entry_date }}</td>
                                <td class="px-4 py-2">
                                    <NavLink :href="route('stockEntries.show', entry.id)" class="text-blue-500">
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
import AddIcon from "@/Components/Icons/AddIcon.vue";

const props = defineProps({
    supplier: Object,
    stockEntries: Array,
});

const entryStartDate = ref('');
const entryEndDate = ref('');

// Filtrar entradas de stock
const filteredStockEntries = computed(() => {
    return props.stockEntries.filter(entry => {
        const startDateMatches = !entryStartDate.value || new Date(entry.entry_date) >= new Date(entryStartDate.value);
        const endDateMatches = !entryEndDate.value || new Date(entry.entry_date) <= new Date(entryEndDate.value);
        return startDateMatches && endDateMatches;
    });
});

const deleteFilters = () =>{
    entryEndDate.value='';
    entryStartDate.value='';

}
</script>
