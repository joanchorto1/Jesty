<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen shadow-md">
            <h1 class="text-3xl font-bold mb-8">Reportes de Contabilidad</h1>
            <div class="mb-4">
                <label for="start_date" class="block text-gray-700">Fecha de Inicio</label>
                <input v-model="filter.start_date" type="date" id="start_date" @change="fetchFilteredData"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="end_date" class="block text-gray-700">Fecha de Fin</label>
                <input v-model="filter.end_date" type="date" id="end_date" @change="fetchFilteredData"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <h2 class="text-2xl font-semibold mt-8 mb-4">Gastos</h2>
            <table class="min-w-full bg-white border border-gray-300 mb-8">
                <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Fecha</th>
                    <th class="py-2 px-4 border-b">Nombre</th>
                    <th class="py-2 px-4 border-b">Descripción</th>
                    <th class="py-2 px-4 border-b">Monto</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="expense in filteredExpenses" :key="expense.id">
                    <td class="py-2 px-4 border-b">{{ expense.date }}</td>
                    <td class="py-2 px-4 border-b">{{ expense.name }}</td>
                    <td class="py-2 px-4 border-b">{{ expense.description }}</td>
                    <td class="py-2 px-4 border-b">{{ expense.amount }}</td>
                </tr>
                <tr v-if="filteredExpenses.length === 0">
                    <td colspan="4" class="py-2 px-4 border-b text-center">No hay gastos en este rango de fechas.</td>
                </tr>
                </tbody>
            </table>

            <h2 class="text-2xl font-semibold mt-8 mb-4">Ingresos</h2>
            <table class="min-w-full bg-white border border-gray-300 mb-8">
                <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Fecha</th>
                    <th class="py-2 px-4 border-b">Nombre</th>
                    <th class="py-2 px-4 border-b">Cliente</th>
                    <th class="py-2 px-4 border-b">Monto</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="income in filteredIncomes" :key="income.id">
                    <td class="py-2 px-4 border-b">{{ income.date }}</td>
                    <td class="py-2 px-4 border-b">{{ income.name }}</td>
                    <td class="py-2 px-4 border-b">{{ income.source }}</td>
                    <td class="py-2 px-4 border-b">{{ income.total_amount }}</td>
                </tr>
                <tr v-if="filteredIncomes.length === 0">
                    <td colspan="4" class="py-2 px-4 border-b text-center">No hay ingresos en este rango de fechas.</td>
                </tr>
                </tbody>
            </table>

            <!-- Sección de comparativa de beneficios -->
            <h2 class="text-2xl font-semibold mt-8 mb-4">Comparativa de Beneficios</h2>
            <div class="bg-white border border-gray-300 p-4 mb-8">
                <p><strong>Beneficio Bruto:</strong> {{ grossBenefit }}</p>
                <p><strong>Monto de IVA:</strong> {{ ivaAmount }}</p>
                <p><strong>Beneficio Neto:</strong> {{ netBenefit }}</p>
            </div>

            <div class="mt-8 flex justify-between items-center">
                <button @click="printReport" class="bg-blue-900 p-2 rounded-full" title="Imprimir Reporte">
                    <PrintIcon class="w-6 h-6 fill-gray-50 stroke-0 stroke-gray-50"/>
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import {ref, computed} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrintIcon from "@/Components/Icons/PrintIcon.vue";

const props = defineProps({
    expenses: Array,
    incomes: Array, // Cambié invoices a incomes
    clients: Array,
});

const filter = ref({
    start_date: '',
    end_date: '',
});

// Computed properties para filtrar los gastos y los ingresos
const filteredExpenses = computed(() => {
    if (!filter.value.start_date && !filter.value.end_date) {
        return props.expenses; // Sin filtro, devuelve todos los gastos
    }
    return props.expenses.filter(expense => {
        const expenseDate = new Date(expense.date);
        const startDate = new Date(filter.value.start_date);
        const endDate = new Date(filter.value.end_date);
        return (
            (filter.value.start_date ? expenseDate >= startDate : true) &&
            (filter.value.end_date ? expenseDate <= endDate : true)
        );
    });
});

const filteredIncomes = computed(() => {
    if (!filter.value.start_date && !filter.value.end_date) {
        return props.incomes; // Sin filtro, devuelve todos los ingresos
    }
    return props.incomes.filter(income => {
        const incomeDate = new Date(income.date);
        const startDate = new Date(filter.value.start_date);
        const endDate = new Date(filter.value.end_date);
        return (
            (filter.value.start_date ? incomeDate >= startDate : true) &&
            (filter.value.end_date ? incomeDate <= endDate : true)
        );
    });
});

// Cálculos de beneficios
const grossBenefit = computed(() => {
    const totalExpenses = filteredExpenses.value.reduce((total, expense) => total + expense.amount, 0);
    const totalIncomes = filteredIncomes.value.reduce((total, income) => total + income.total_amount, 0);
    return totalIncomes - totalExpenses; // Beneficio bruto
});

const ivaAmount = computed(() => {
    const totalExpenses = filteredExpenses.value.reduce((total, expense) => total + expense.amount * (expense.iva / 100), 0);
    const totalIncomes = filteredIncomes.value.reduce((total, income) => total + income.total_amount * (income.tax_rate / 100), 0);
    return totalIncomes - totalExpenses; // Monto de IVA
});

const netBenefit = computed(() => {
    return grossBenefit.value - ivaAmount.value; // Beneficio neto
});

// Método para imprimir el reporte
const printReport = () => {
    const url = route('expenses.reportsPrint', {
        start_date: filter.value.start_date,
        end_date: filter.value.end_date,
    });
    window.open(url, '_blank'); // Abrir la vista de impresión en una nueva pestaña
};
</script>
