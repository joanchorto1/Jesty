<template>
    <div class="p-6">
        <h1 class="text-3xl font-bold mb-8">Reporte de Gastos e Ingresos</h1>

        <!-- Sección de Gastos -->
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
            <tr v-for="expense in expenses" :key="expense.id">
                <td class="py-2 px-4 border-b">{{ expense.date }}</td>
                <td class="py-2 px-4 border-b">{{ expense.name }}</td>
                <td class="py-2 px-4 border-b">{{ expense.description }}</td>
                <td class="py-2 px-4 border-b">{{ expense.amount }}</td>
            </tr>
            </tbody>
        </table>

        <!-- Sección de Ingresos -->
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
            <tr v-for="income in incomes" :key="income.id">
                <td class="py-2 px-4 border-b">{{ income.date }}</td>
                <td class="py-2 px-4 border-b">{{ income.name }}</td>
                <td class="py-2 px-4 border-b">{{ income.source }}</td>
                <td class="py-2 px-4 border-b">{{ income.total_amount }}</td>
            </tr>
            </tbody>
        </table>

        <!-- Sección de Comparativa de Beneficios -->
        <h2 class="text-2xl font-semibold mt-8 mb-4">Comparativa de Beneficios</h2>
        <div class="bg-white border border-gray-300 p-4 mb-8">
            <p><strong>Beneficio Bruto:</strong> {{ grossBenefit }}</p>
            <p><strong>Monto de IVA:</strong> {{ ivaAmount }}</p>
            <p><strong>Beneficio Neto:</strong> {{ netBenefit }}</p>
        </div>

        <!-- Información de fechas -->
        <div class="mt-8">
            <p><strong>Fechas:</strong> {{ start_date }} - {{ end_date }}</p>
        </div>

        <!-- Botón de imprimir -->
        <div class="mt-8">
            <button @click="printReport" class="bg-blue-900 text-white px-4 py-2 rounded">
                Imprimir Reporte
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    expenses: Array,
    incomes: Array,
    clients: Array,
    start_date: String,
    end_date: String,
});

// Filtrado de gastos e ingresos por fecha
const filteredExpenses = computed(() => {
    return props.expenses
});
const filteredIncomes = computed(() => {
    return props.incomes
});

console.log(filteredExpenses.value);
console.log(filteredIncomes.value);

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
    window.print(); // Llama a la función de impresión del navegador
};
</script>

<style>
@media print {
    /* Estilos para la impresión */
    body {
        font-family: Arial, sans-serif;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    th {
        background-color: #f2f2f2;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    button {
        display: none;
    }
}
</style>
