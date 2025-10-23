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
                    <FinanceSummaryCard
                        label="Ingresos filtrados"
                        :value="formatCurrency(totalIncomes)"
                        :helper="`${totalIncomeCount} registros`"
                    />
                    <FinanceSummaryCard
                        label="Gastos filtrados"
                        :value="formatCurrency(totalExpensesWithTax)"
                        :helper="`${totalExpenseCount} registros`"
                    />
                    <FinanceSummaryCard :label="netBalanceLabel" :value="formatCurrency(netBalance)" :helper="ivaAndMarginHelper" />
                </template>
            </FinancePageHeader>

            <main class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <section class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="mb-6 border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Filtros temporales</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Selecciona un periodo y el modo de agrupación para recalcular automáticamente todos los indicadores y tablas.
                        </p>
                    </header>
                    <form class="space-y-6" @submit.prevent="applyFilters">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                            <div class="md:col-span-1">
                                <InputLabel for="start_date" value="Fecha de inicio" />
                                <TextInput id="start_date" v-model="filters.start_date" type="date" class="mt-2 block w-full" />
                            </div>
                            <div class="md:col-span-1">
                                <InputLabel for="end_date" value="Fecha de fin" />
                                <TextInput id="end_date" v-model="filters.end_date" type="date" class="mt-2 block w-full" />
                            </div>
                            <div class="md:col-span-1">
                                <InputLabel for="mode" value="Modo de agrupación" />
                                <select
                                    id="mode"
                                    v-model="filters.mode"
                                    class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500"
                                >
                                    <option value="general">General</option>
                                    <option value="monthly">Mensual</option>
                                    <option value="annual">Anual</option>
                                </select>
                            </div>
                            <div class="md:col-span-1 flex items-end justify-start gap-3">
                                <PrimaryButton type="submit" class="w-full justify-center">
                                    Aplicar filtros
                                </PrimaryButton>
                                <SecondaryButton type="button" class="w-full justify-center" @click="resetFilters">
                                    Limpiar filtros
                                </SecondaryButton>
                            </div>
                        </div>
                    </form>
                    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-emerald-700">
                            <h3 class="text-xs font-semibold uppercase tracking-widest">Ingresos netos</h3>
                            <p class="mt-3 text-2xl font-semibold">{{ formatCurrency(totalIncomes - totalIncomeTaxes) }}</p>
                            <p class="mt-2 text-xs text-emerald-800/70">Importe sin impuestos.</p>
                        </div>
                        <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4 text-rose-700">
                            <h3 class="text-xs font-semibold uppercase tracking-widest">Gastos totales</h3>
                            <p class="mt-3 text-2xl font-semibold">{{ formatCurrency(totalExpensesWithTax) }}</p>
                            <p class="mt-2 text-xs text-rose-800/70">Incluye base imponible e IVA.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-slate-700">
                            <h3 class="text-xs font-semibold uppercase tracking-widest">Resultado neto</h3>
                            <p class="mt-3 text-2xl font-semibold">{{ formatCurrency(netBalance) }}</p>
                            <p class="mt-2 text-xs text-slate-500">Beneficio tras impuestos.</p>
                        </div>
                    </div>
                </section>

                <section v-if="mode === 'general'" class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="flex flex-col gap-2 border-b border-slate-200 pb-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Gastos</h2>
                            <p class="text-sm text-slate-500">Detalle de gastos filtrados por fecha con su base imponible e impuestos asociados.</p>
                        </div>
                        <span class="text-sm text-slate-500">{{ generalExpenseTotals.count }} registros</span>
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
                                <tr v-for="expense in generalExpenses" :key="expense.id" class="bg-white/60">
                                    <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(expense.date) }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ expense.name }}</td>
                                    <td class="px-4 py-3">{{ expense.description || '—' }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(expense.amount) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(expense.amount * (expense.iva ?? 0) / 100) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(expense.amount + expense.amount * (expense.iva ?? 0) / 100) }}</td>
                                </tr>
                                <tr v-if="generalExpenses.length === 0">
                                    <td colspan="6" class="px-4 py-4 text-center text-slate-400">No hay gastos en este rango de fechas.</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-slate-50 text-xs uppercase tracking-widest text-slate-500">
                                <tr>
                                    <td colspan="3" class="px-4 py-3">Totales</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(generalExpenseTotals.base) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(generalExpenseTotals.tax) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(generalExpenseTotals.total) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

                <section v-if="mode === 'general'" class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="flex flex-col gap-2 border-b border-slate-200 pb-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Ingresos</h2>
                            <p class="text-sm text-slate-500">Incluye nombre, origen y cantidades con impuestos para facilitar la conciliación.</p>
                        </div>
                        <span class="text-sm text-slate-500">{{ generalIncomeTotals.count }} registros</span>
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
                                <tr v-for="income in generalIncomes" :key="income.id" class="bg-white/60">
                                    <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(income.date) }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ income.name }}</td>
                                    <td class="px-4 py-3">{{ income.source || '—' }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(income.tax_base ?? 0) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(income.tax_amount ?? (income.tax_base ?? 0) * (income.tax_rate ?? 0) / 100) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(income.total_amount ?? (income.tax_base ?? 0) + (income.tax_amount ?? 0)) }}</td>
                                </tr>
                                <tr v-if="generalIncomes.length === 0">
                                    <td colspan="6" class="px-4 py-4 text-center text-slate-400">No hay ingresos en este rango de fechas.</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-slate-50 text-xs uppercase tracking-widest text-slate-500">
                                <tr>
                                    <td colspan="3" class="px-4 py-3">Totales</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(generalIncomeTotals.base) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(generalIncomeTotals.tax) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(generalIncomeTotals.total) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

                <section v-else class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="flex flex-col gap-2 border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Resumen agrupado</h2>
                        <p class="text-sm text-slate-500">
                            Visualiza los totales por periodo seleccionado y compara de un vistazo los ingresos, gastos, IVA y márgenes asociados.
                        </p>
                    </header>

                    <div v-if="mode === 'monthly'" class="mt-6 space-y-8">
                        <div v-for="year in monthlyYears" :key="year.year" class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow">
                            <header class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <h3 class="text-lg font-semibold text-slate-800">{{ year.year }}</h3>
                                <p class="text-sm text-slate-500">
                                    IVA acumulado: {{ formatCurrency(year.summary.iva_balance) }} · Margen bruto: {{ formatCurrency(year.summary.gross_margin) }} · Saldo neto: {{ formatCurrency(year.summary.net_balance) }}
                                </p>
                            </header>
                            <div class="mt-4 overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600">
                                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">
                                        <tr>
                                            <th scope="col" class="px-4 py-3">Mes</th>
                                            <th scope="col" class="px-4 py-3 text-right">Ingresos (IVA incl.)</th>
                                            <th scope="col" class="px-4 py-3 text-right">Gastos (IVA incl.)</th>
                                            <th scope="col" class="px-4 py-3 text-right">IVA</th>
                                            <th scope="col" class="px-4 py-3 text-right">Margen bruto</th>
                                            <th scope="col" class="px-4 py-3 text-right">Saldo neto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr v-for="month in year.months" :key="month.key" class="bg-white/60">
                                            <td class="px-4 py-3 font-medium text-slate-700">{{ month.label }}</td>
                                            <td class="px-4 py-3 text-right">{{ formatCurrency(month.summary.total_income) }}</td>
                                            <td class="px-4 py-3 text-right">{{ formatCurrency(month.summary.total_expense_total) }}</td>
                                            <td class="px-4 py-3 text-right">{{ formatCurrency(month.summary.iva_balance) }}</td>
                                            <td class="px-4 py-3 text-right">{{ formatCurrency(month.summary.gross_margin) }}</td>
                                            <td class="px-4 py-3 text-right">{{ formatCurrency(month.summary.net_balance) }}</td>
                                        </tr>
                                        <tr v-if="year.months.length === 0">
                                            <td colspan="6" class="px-4 py-4 text-center text-slate-400">Sin datos para este año.</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-slate-50 text-xs uppercase tracking-widest text-slate-500">
                                        <tr>
                                            <td class="px-4 py-3">Totales {{ year.year }}</td>
                                            <td class="px-4 py-3 text-right">{{ formatCurrency(year.summary.total_income) }}</td>
                                            <td class="px-4 py-3 text-right">{{ formatCurrency(year.summary.total_expense_total) }}</td>
                                            <td class="px-4 py-3 text-right">{{ formatCurrency(year.summary.iva_balance) }}</td>
                                            <td class="px-4 py-3 text-right">{{ formatCurrency(year.summary.gross_margin) }}</td>
                                            <td class="px-4 py-3 text-right">{{ formatCurrency(year.summary.net_balance) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div v-else class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Año</th>
                                    <th scope="col" class="px-4 py-3 text-right">Ingresos (IVA incl.)</th>
                                    <th scope="col" class="px-4 py-3 text-right">Gastos (IVA incl.)</th>
                                    <th scope="col" class="px-4 py-3 text-right">IVA</th>
                                    <th scope="col" class="px-4 py-3 text-right">Margen bruto</th>
                                    <th scope="col" class="px-4 py-3 text-right">Saldo neto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="year in annualGroups" :key="year.key" class="bg-white/60">
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ year.label }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(year.summary.total_income) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(year.summary.total_expense_total) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(year.summary.iva_balance) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(year.summary.gross_margin) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(year.summary.net_balance) }}</td>
                                </tr>
                                <tr v-if="annualGroups.length === 0">
                                    <td colspan="6" class="px-4 py-4 text-center text-slate-400">No hay datos para el modo anual.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section v-if="mode !== 'general'" class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="flex flex-col gap-2 border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Detalle por periodo</h2>
                        <p class="text-sm text-slate-500">Cada bloque muestra los registros individuales de ingresos y gastos junto con sus totales para el periodo indicado.</p>
                    </header>
                    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <article
                            v-for="group in groups"
                            :key="group.key"
                            class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow"
                        >
                            <header class="flex flex-col gap-1 border-b border-slate-200 pb-4">
                                <h3 class="text-lg font-semibold text-slate-800">{{ group.label }}</h3>
                                <p class="text-sm text-slate-500">Periodo: {{ formatRange(group.range) }}</p>
                                <p class="text-sm text-slate-500">
                                    IVA: {{ formatCurrency(group.summary.iva_balance) }} · Margen bruto: {{ formatCurrency(group.summary.gross_margin) }} · Saldo neto: {{ formatCurrency(group.summary.net_balance) }}
                                </p>
                            </header>
                            <div class="mt-4 grid grid-cols-1 gap-4">
                                <div>
                                    <h4 class="text-sm font-semibold uppercase tracking-widest text-slate-500">Ingresos ({{ group.incomes.totals.count }})</h4>
                                    <div class="mt-2 overflow-x-auto">
                                        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600">
                                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">
                                                <tr>
                                                    <th scope="col" class="px-3 py-2">Fecha</th>
                                                    <th scope="col" class="px-3 py-2">Nombre</th>
                                                    <th scope="col" class="px-3 py-2">Origen</th>
                                                    <th scope="col" class="px-3 py-2 text-right">Base</th>
                                                    <th scope="col" class="px-3 py-2 text-right">IVA</th>
                                                    <th scope="col" class="px-3 py-2 text-right">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                <tr v-for="income in group.incomes.items" :key="income.id" class="bg-white/60">
                                                    <td class="px-3 py-2 whitespace-nowrap">{{ formatDate(income.date) }}</td>
                                                    <td class="px-3 py-2 font-medium text-slate-700">{{ income.name }}</td>
                                                    <td class="px-3 py-2">{{ income.source || '—' }}</td>
                                                    <td class="px-3 py-2 text-right">{{ formatCurrency(income.tax_base ?? 0) }}</td>
                                                    <td class="px-3 py-2 text-right">{{ formatCurrency(income.tax_amount ?? (income.tax_base ?? 0) * (income.tax_rate ?? 0) / 100) }}</td>
                                                    <td class="px-3 py-2 text-right">{{ formatCurrency(income.total_amount ?? (income.tax_base ?? 0) + (income.tax_amount ?? 0)) }}</td>
                                                </tr>
                                                <tr v-if="group.incomes.items.length === 0">
                                                    <td colspan="6" class="px-3 py-3 text-center text-slate-400">Sin ingresos registrados.</td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="bg-slate-50 text-xs uppercase tracking-widest text-slate-500">
                                                <tr>
                                                    <td colspan="3" class="px-3 py-2">Totales</td>
                                                    <td class="px-3 py-2 text-right">{{ formatCurrency(group.incomes.totals.base) }}</td>
                                                    <td class="px-3 py-2 text-right">{{ formatCurrency(group.incomes.totals.tax) }}</td>
                                                    <td class="px-3 py-2 text-right">{{ formatCurrency(group.incomes.totals.total) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold uppercase tracking-widest text-slate-500">Gastos ({{ group.expenses.totals.count }})</h4>
                                    <div class="mt-2 overflow-x-auto">
                                        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600">
                                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">
                                                <tr>
                                                    <th scope="col" class="px-3 py-2">Fecha</th>
                                                    <th scope="col" class="px-3 py-2">Nombre</th>
                                                    <th scope="col" class="px-3 py-2">Descripción</th>
                                                    <th scope="col" class="px-3 py-2 text-right">Base</th>
                                                    <th scope="col" class="px-3 py-2 text-right">IVA</th>
                                                    <th scope="col" class="px-3 py-2 text-right">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                <tr v-for="expense in group.expenses.items" :key="expense.id" class="bg-white/60">
                                                    <td class="px-3 py-2 whitespace-nowrap">{{ formatDate(expense.date) }}</td>
                                                    <td class="px-3 py-2 font-medium text-slate-700">{{ expense.name }}</td>
                                                    <td class="px-3 py-2">{{ expense.description || '—' }}</td>
                                                    <td class="px-3 py-2 text-right">{{ formatCurrency(expense.amount) }}</td>
                                                    <td class="px-3 py-2 text-right">{{ formatCurrency(expense.amount * (expense.iva ?? 0) / 100) }}</td>
                                                    <td class="px-3 py-2 text-right">{{ formatCurrency(expense.amount + expense.amount * (expense.iva ?? 0) / 100) }}</td>
                                                </tr>
                                                <tr v-if="group.expenses.items.length === 0">
                                                    <td colspan="6" class="px-3 py-3 text-center text-slate-400">Sin gastos registrados.</td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="bg-slate-50 text-xs uppercase tracking-widest text-slate-500">
                                                <tr>
                                                    <td colspan="3" class="px-3 py-2">Totales</td>
                                                    <td class="px-3 py-2 text-right">{{ formatCurrency(group.expenses.totals.base) }}</td>
                                                    <td class="px-3 py-2 text-right">{{ formatCurrency(group.expenses.totals.tax) }}</td>
                                                    <td class="px-3 py-2 text-right">{{ formatCurrency(group.expenses.totals.total) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
            </main>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FinancePageHeader from '@/Components/Finance/FinancePageHeader.vue';
import FinanceSummaryCard from '@/Components/Finance/FinanceSummaryCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    report: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    clients: {
        type: Array,
        default: () => [],
    },
});

const filters = reactive({
    start_date: props.filters.start_date ?? '',
    end_date: props.filters.end_date ?? '',
    mode: props.filters.mode ?? props.report?.mode ?? 'general',
});

watch(
    () => props.filters,
    (value) => {
        filters.start_date = value.start_date ?? '';
        filters.end_date = value.end_date ?? '';
        filters.mode = value.mode ?? props.report?.mode ?? 'general';
    },
    { deep: true }
);

const mode = computed(() => props.report?.mode ?? filters.mode ?? 'general');
const groups = computed(() => props.report?.groups ?? []);
const overallSummary = computed(() => props.report?.overall?.summary ?? createEmptySummary());

const totalIncomes = computed(() => Number(overallSummary.value.total_income ?? 0));
const totalIncomeBase = computed(() => Number(overallSummary.value.total_income_base ?? 0));
const totalIncomeTaxes = computed(() => Number(overallSummary.value.total_income_tax ?? 0));
const totalExpenses = computed(() => Number(overallSummary.value.total_expense_base ?? 0));
const totalExpenseTaxes = computed(() => Number(overallSummary.value.total_expense_tax ?? 0));
const totalExpensesWithTax = computed(() => Number(overallSummary.value.total_expense_total ?? totalExpenses.value + totalExpenseTaxes.value));
const netBalance = computed(() => Number(overallSummary.value.net_balance ?? 0));
const netBalanceLabel = computed(() => (netBalance.value >= 0 ? 'Beneficio neto' : 'Pérdida neta'));
const grossMargin = computed(() => Number(overallSummary.value.gross_margin ?? 0));
const ivaBalance = computed(() => Number(overallSummary.value.iva_balance ?? 0));

const ivaAndMarginHelper = computed(
    () => `IVA: ${formatCurrency(ivaBalance.value)} · Margen bruto: ${formatCurrency(grossMargin.value)}`
);

const totalIncomeCount = computed(() =>
    groups.value.reduce((acc, group) => acc + Number(group?.incomes?.totals?.count ?? 0), 0)
);
const totalExpenseCount = computed(() =>
    groups.value.reduce((acc, group) => acc + Number(group?.expenses?.totals?.count ?? 0), 0)
);

const generalGroup = computed(() => (mode.value === 'general' ? groups.value[0] ?? null : null));
const generalExpenses = computed(() => generalGroup.value?.expenses?.items ?? []);
const generalIncomes = computed(() => generalGroup.value?.incomes?.items ?? []);
const generalExpenseTotals = computed(() => generalGroup.value?.expenses?.totals ?? createEmptyTotals());
const generalIncomeTotals = computed(() => generalGroup.value?.incomes?.totals ?? createEmptyTotals());

const monthlyYears = computed(() => {
    if (mode.value !== 'monthly') {
        return [];
    }

    const yearMap = new Map();

    groups.value.forEach((group) => {
        const [year] = group.key.split('-');
        if (!yearMap.has(year)) {
            yearMap.set(year, []);
        }
        yearMap.get(year).push(group);
    });

    return Array.from(yearMap.entries())
        .map(([year, months]) => {
            months.sort((a, b) => a.key.localeCompare(b.key));
            return {
                year,
                months,
                summary: sumSummaries(months),
            };
        })
        .sort((a, b) => a.year.localeCompare(b.year));
});

const annualGroups = computed(() => {
    if (mode.value !== 'annual') {
        return [];
    }

    return groups.value.map((group) => ({
        ...group,
        summary: group.summary ?? createEmptySummary(),
    }));
});

const applyFilters = () => {
    router.get(
        route('expenses.report'),
        {
            start_date: filters.start_date || undefined,
            end_date: filters.end_date || undefined,
            mode: filters.mode || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const resetFilters = () => {
    filters.start_date = '';
    filters.end_date = '';
    filters.mode = 'general';
    applyFilters();
};

const printReport = () => {
    const url = route('expenses.reportsPrint', {
        start_date: filters.start_date || undefined,
        end_date: filters.end_date || undefined,
        mode: filters.mode || undefined,
    });
    window.open(url, '_blank');
};

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

const formatRange = (range) => {
    if (!range?.start && !range?.end) {
        return 'Sin periodo';
    }

    if (range?.start && range?.end && range.start === range.end) {
        return formatDate(range.start);
    }

    return `${range?.start ? formatDate(range.start) : '—'} – ${range?.end ? formatDate(range.end) : '—'}`;
};

function createEmptySummary() {
    return {
        total_income: 0,
        total_income_base: 0,
        total_income_tax: 0,
        total_expense_base: 0,
        total_expense_tax: 0,
        total_expense_total: 0,
        gross_margin: 0,
        iva_balance: 0,
        net_balance: 0,
    };
}

function createEmptyTotals() {
    return {
        count: 0,
        base: 0,
        tax: 0,
        total: 0,
    };
}

function sumSummaries(items) {
    const accumulator = createEmptySummary();

    items.forEach((item) => {
        const summary = item.summary ?? {};
        Object.keys(accumulator).forEach((key) => {
            accumulator[key] += Number(summary[key] ?? 0);
        });
    });

    return accumulator;
}
</script>
