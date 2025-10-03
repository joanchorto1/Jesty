<template>
    <AppLayout title="Informes contables">
        <div class="min-h-screen bg-slate-950">
            <FinancePageHeader
                eyebrow="Análisis"
                title="Informe contable"
                description="Cruza ingresos y gastos con filtros temporales y obtén una visión clara de la salud financiera de la empresa."
                :metrics-columns="3"
            >
                <template #actions>
                    <PrimaryButton type="button" @click="printReport">
                        Imprimir informe
                    </PrimaryButton>
                </template>
                <template #metrics>
                    <FinanceSummaryCard label="Ingresos filtrados" :value="formatCurrency(totalIncomes)" :helper="`${filteredIncomes.length} registros`" />
                    <FinanceSummaryCard label="Gastos filtrados" :value="formatCurrency(totalExpenses)" :helper="`${filteredExpenses.length} registros`" />
                    <FinanceSummaryCard :label="netBalanceLabel" :value="formatCurrency(netBalance)" :helper="ivaHelper" />
                </template>
            </FinancePageHeader>

            <main class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <section class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="mb-6 border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Filtros temporales</h2>
                        <p class="mt-1 text-sm text-slate-500">Selecciona un periodo para recalcular automáticamente todos los indicadores y tablas.</p>
                    </header>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div class="md:col-span-1">
                            <InputLabel for="start_date" value="Fecha de inicio" />
                            <TextInput id="start_date" v-model="filter.start_date" type="date" class="mt-2 block w-full" />
                        </div>
                        <div class="md:col-span-1">
                            <InputLabel for="end_date" value="Fecha de fin" />
                            <TextInput id="end_date" v-model="filter.end_date" type="date" class="mt-2 block w-full" />
                        </div>
                        <div class="md:col-span-1 flex items-end">
                            <SecondaryButton type="button" class="w-full justify-center" @click="resetFilters">
                                Limpiar filtros
                            </SecondaryButton>
                        </div>
                    </div>
                    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-emerald-700">
                            <h3 class="text-xs font-semibold uppercase tracking-widest">Ingresos netos</h3>
                            <p class="mt-3 text-2xl font-semibold">{{ formatCurrency(totalIncomes - totalIncomeTaxes) }}</p>
                            <p class="mt-2 text-xs text-emerald-800/70">Importe sin impuestos.</p>
                        </div>
                        <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4 text-rose-700">
                            <h3 class="text-xs font-semibold uppercase tracking-widest">Gastos totales</h3>
                            <p class="mt-3 text-2xl font-semibold">{{ formatCurrency(totalExpenses + totalExpenseTaxes) }}</p>
                            <p class="mt-2 text-xs text-rose-800/70">Incluye base imponible e IVA.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-slate-700">
                            <h3 class="text-xs font-semibold uppercase tracking-widest">Resultado neto</h3>
                            <p class="mt-3 text-2xl font-semibold">{{ formatCurrency(netBalance) }}</p>
                            <p class="mt-2 text-xs text-slate-500">Beneficio tras impuestos.</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="flex flex-col gap-2 border-b border-slate-200 pb-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Gastos</h2>
                            <p class="text-sm text-slate-500">Detalle de gastos filtrados por fecha con su base imponible e impuestos asociados.</p>
                        </div>
                        <span class="text-sm text-slate-500">{{ filteredExpenses.length }} registros</span>
                    </header>
                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Fecha</th>
                                    <th scope="col" class="px-4 py-3">Nombre</th>
                                    <th scope="col" class="px-4 py-3">Descripción</th>
                                    <th scope="col" class="px-4 py-3 text-right">Base imponible</th>
                                    <th scope="col" class="px-4 py-3 text-right">IVA</th>
                                    <th scope="col" class="px-4 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="expense in filteredExpenses" :key="expense.id" class="bg-white/60">
                                    <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(expense.date) }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ expense.name }}</td>
                                    <td class="px-4 py-3">{{ expense.description || '—' }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(expense.amount) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(expense.amount * (expense.iva ?? 0) / 100) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(expense.amount + expense.amount * (expense.iva ?? 0) / 100) }}</td>
                                </tr>
                                <tr v-if="filteredExpenses.length === 0">
                                    <td colspan="6" class="px-4 py-4 text-center text-slate-400">No hay gastos en este rango de fechas.</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-slate-50 text-xs uppercase tracking-widest text-slate-500">
                                <tr>
                                    <td colspan="3" class="px-4 py-3">Totales</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalExpenses) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalExpenseTaxes) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalExpenses + totalExpenseTaxes) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

                <section class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="flex flex-col gap-2 border-b border-slate-200 pb-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Ingresos</h2>
                            <p class="text-sm text-slate-500">Incluye nombre, origen y cantidades con impuestos para facilitar la conciliación.</p>
                        </div>
                        <span class="text-sm text-slate-500">{{ filteredIncomes.length }} registros</span>
                    </header>
                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Fecha</th>
                                    <th scope="col" class="px-4 py-3">Nombre</th>
                                    <th scope="col" class="px-4 py-3">Origen</th>
                                    <th scope="col" class="px-4 py-3 text-right">Base imponible</th>
                                    <th scope="col" class="px-4 py-3 text-right">IVA</th>
                                    <th scope="col" class="px-4 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="income in filteredIncomes" :key="income.id" class="bg-white/60">
                                    <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(income.date) }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ income.name }}</td>
                                    <td class="px-4 py-3">{{ income.source || '—' }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(income.tax_base ?? 0) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(income.tax_amount ?? (income.tax_base ?? 0) * (income.tax_rate ?? 0) / 100) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(income.total_amount ?? (income.tax_base ?? 0) + (income.tax_amount ?? 0)) }}</td>
                                </tr>
                                <tr v-if="filteredIncomes.length === 0">
                                    <td colspan="6" class="px-4 py-4 text-center text-slate-400">No hay ingresos en este rango de fechas.</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-slate-50 text-xs uppercase tracking-widest text-slate-500">
                                <tr>
                                    <td colspan="3" class="px-4 py-3">Totales</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalIncomeBase) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalIncomeTaxes) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalIncomes) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import FinancePageHeader from '@/Components/Finance/FinancePageHeader.vue';
import FinanceSummaryCard from '@/Components/Finance/FinanceSummaryCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    expenses: {
        type: Array,
        default: () => [],
    },
    incomes: {
        type: Array,
        default: () => [],
    },
});

const filter = ref({
    start_date: '',
    end_date: '',
});

const parseDate = (value) => {
    if (!value) {
        return null;
    }
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? null : date;
};

const filteredExpenses = computed(() => {
    if (!filter.value.start_date && !filter.value.end_date) {
        return props.expenses;
    }
    const start = parseDate(filter.value.start_date);
    const end = parseDate(filter.value.end_date);
    return props.expenses.filter((expense) => {
        const date = parseDate(expense.date);
        if (!date) {
            return false;
        }
        const afterStart = start ? date >= start : true;
        const beforeEnd = end ? date <= end : true;
        return afterStart && beforeEnd;
    });
});

const filteredIncomes = computed(() => {
    if (!filter.value.start_date && !filter.value.end_date) {
        return props.incomes;
    }
    const start = parseDate(filter.value.start_date);
    const end = parseDate(filter.value.end_date);
    return props.incomes.filter((income) => {
        const date = parseDate(income.date);
        if (!date) {
            return false;
        }
        const afterStart = start ? date >= start : true;
        const beforeEnd = end ? date <= end : true;
        return afterStart && beforeEnd;
    });
});

const sumBy = (collection, callback) => {
    return collection.reduce((total, item) => total + Number(callback(item) || 0), 0);
};

const totalExpenses = computed(() => sumBy(filteredExpenses.value, (expense) => expense.amount));
const totalExpenseTaxes = computed(() => sumBy(filteredExpenses.value, (expense) => expense.amount * (expense.iva ?? 0) / 100));
const totalIncomes = computed(() => sumBy(filteredIncomes.value, (income) => income.total_amount ?? (income.tax_base ?? 0) + (income.tax_amount ?? 0)));
const totalIncomeBase = computed(() => sumBy(filteredIncomes.value, (income) => income.tax_base ?? 0));
const totalIncomeTaxes = computed(() => sumBy(filteredIncomes.value, (income) => income.tax_amount ?? (income.tax_base ?? 0) * (income.tax_rate ?? 0) / 100));

const netBalance = computed(() => totalIncomes.value - (totalExpenses.value + totalExpenseTaxes.value));
const netBalanceLabel = computed(() => (netBalance.value >= 0 ? 'Beneficio neto' : 'Pérdida neta'));
const ivaHelper = computed(() => `IVA: ${formatCurrency(totalIncomeTaxes.value - totalExpenseTaxes.value)}`);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
    }).format(Number(value) || 0);
};

const formatDate = (value) => {
    if (!value) {
        return 'Sin fecha';
    }
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }
    return new Intl.DateTimeFormat('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(date);
};

const resetFilters = () => {
    filter.value.start_date = '';
    filter.value.end_date = '';
};

const printReport = () => {
    const url = route('expenses.reportsPrint', {
        start_date: filter.value.start_date,
        end_date: filter.value.end_date,
    });
    window.open(url, '_blank');
};
</script>
