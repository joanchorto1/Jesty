<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen">
            <div class="mx-auto max-w-6xl space-y-8">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Reportes de contabilidad</h1>
                    <p class="text-sm text-gray-500">Filtra la información para analizar el desempeño económico de la empresa.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-6 rounded-lg shadow">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Fecha de inicio</label>
                        <input
                            v-model="filter.start_date"
                            type="date"
                            id="start_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Fecha de fin</label>
                        <input
                            v-model="filter.end_date"
                            type="date"
                            id="end_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>
                    <div class="md:col-span-2 flex gap-3">
                        <button @click="clearFilters" type="button" class="rounded-full border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                            Limpiar filtros
                        </button>
                        <button @click="printReport" type="button" class="inline-flex items-center rounded-full bg-blue-900 px-4 py-2 text-white shadow hover:bg-blue-700">
                            <PrintIcon class="mr-2 h-5 w-5 fill-gray-50" />
                            Imprimir reporte
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-lg bg-white p-4 shadow border border-gray-200">
                        <p class="text-sm text-gray-500">Ingresos filtrados</p>
                        <p class="text-2xl font-semibold text-emerald-600">{{ totalIncomes.toFixed(2) }} €</p>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow border border-gray-200">
                        <p class="text-sm text-gray-500">Gastos filtrados</p>
                        <p class="text-2xl font-semibold text-rose-600">{{ totalExpenses.toFixed(2) }} €</p>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow border border-gray-200">
                        <p class="text-sm text-gray-500">Resultado neto</p>
                        <p :class="['text-2xl font-semibold', netBenefit >= 0 ? 'text-blue-700' : 'text-red-600']">{{ netBenefit.toFixed(2) }} €</p>
                    </div>
                </div>

                <section class="bg-white rounded-lg shadow overflow-hidden">
                    <header class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-700">Detalle de gastos</h2>
                    </header>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="expense in filteredExpenses" :key="expense.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ expense.date }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ expense.name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ expense.description || 'Sin descripción' }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-gray-800">{{ Number(expense.amount).toFixed(2) }} €</td>
                                </tr>
                                <tr v-if="filteredExpenses.length === 0">
                                    <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">No hay gastos en este rango de fechas.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="bg-white rounded-lg shadow overflow-hidden">
                    <header class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-700">Detalle de ingresos</h2>
                    </header>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origen</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="income in filteredIncomes" :key="income.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ income.date }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ income.name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ income.source }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-gray-800">{{ Number(income.total_amount).toFixed(2) }} €</td>
                                </tr>
                                <tr v-if="filteredIncomes.length === 0">
                                    <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">No hay ingresos en este rango de fechas.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrintIcon from '@/Components/Icons/PrintIcon.vue';

const props = defineProps({
    expenses: Array,
    incomes: Array,
    clients: Array,
});

const filter = reactive({
    start_date: '',
    end_date: '',
});

const filteredExpenses = computed(() => {
    if (!filter.start_date && !filter.end_date) {
        return props.expenses;
    }
    return props.expenses.filter((expense) => isWithinRange(expense.date));
});

const filteredIncomes = computed(() => {
    if (!filter.start_date && !filter.end_date) {
        return props.incomes;
    }
    return props.incomes.filter((income) => isWithinRange(income.date));
});

const totalExpenses = computed(() => filteredExpenses.value.reduce((total, expense) => total + Number(expense.amount || 0), 0));
const totalIncomes = computed(() => filteredIncomes.value.reduce((total, income) => total + Number(income.total_amount || 0), 0));
const netBenefit = computed(() => totalIncomes.value - totalExpenses.value);

const isWithinRange = (dateString) => {
    const date = new Date(dateString);
    const start = filter.start_date ? new Date(filter.start_date) : null;
    const end = filter.end_date ? new Date(filter.end_date) : null;

    if (start && date < start) {
        return false;
    }
    if (end && date > end) {
        return false;
    }
    return true;
};

const clearFilters = () => {
    filter.start_date = '';
    filter.end_date = '';
};

const printReport = () => {
    const url = route('expenses.reportsPrint', {
        start_date: filter.start_date,
        end_date: filter.end_date,
    });
    window.open(url, '_blank');
};
</script>
