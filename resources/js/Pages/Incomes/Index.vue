<template>
    <AppLayout>
    <div class="max-w-6xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Listado de Ingresos</h1>

        <div class="mb-4 flex justify-between items-center">
            <a
                :href="route('incomes.create')"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
            >
                Crear Ingreso
            </a>
        </div>

        <table class="table-auto w-full bg-white rounded shadow">
            <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Nombre</th>
                <th class="px-4 py-2 text-left">Origen</th>
                <th class="px-4 py-2 text-right">Base Imponible</th>
                <th class="px-4 py-2 text-right">IVA (%)</th>
                <th class="px-4 py-2 text-right">Monto IVA</th>
                <th class="px-4 py-2 text-right">Total</th>
                <th class="px-4 py-2 text-left">Fecha</th>
                <th class="px-4 py-2 text-center">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="income in incomes"
                :key="income.id"
                class="border-b hover:bg-gray-50"
            >
                <td class="px-4 py-2">{{ income.name }}</td>
                <td class="px-4 py-2">{{ income.source }}</td>
                <td class="px-4 py-2 text-right">{{ income.tax_base.toFixed(2) }}</td>
                <td class="px-4 py-2 text-right">{{ income.tax_rate }}%</td>
                <td class="px-4 py-2 text-right">
                    {{ (income.tax_base * income.tax_rate / 100).toFixed(2) }}
                </td>
                <td class="px-4 py-2 text-right">
                    {{ (income.tax_base + income.tax_base * income.tax_rate / 100).toFixed(2) }}
                </td>
                <td class="px-4 py-2">{{ income.date }}</td>
                <td class="px-4 py-2 text-center flex justify-center space-x-2">
                    <a
                        :href="route('incomes.edit', income.id)"
                        class="text-blue-500 hover:text-blue-700"
                        title="Editar"
                    >
                        <EditIcon class="stroke-gray-600 w-5" />
                    </a>
                    <button
                        @click="deleteIncome(income.id)"
                        class="text-red-500 hover:text-red-700"
                        title="Eliminar"
                    >
                        <DeleteIcon class="stroke-gray-600 w-5"/>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    </AppLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
const props = defineProps({
    incomes: Array, // Lista de ingresos enviada desde el controlador
});

const deleteIncome = (id) => {
    if (confirm('¿Estás seguro de eliminar este ingreso?')) {
        router.delete(route('incomes.destroy', id));
    }
};
</script>
