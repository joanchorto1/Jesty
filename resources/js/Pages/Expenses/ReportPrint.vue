<template>
    <div class="p-6 space-y-10 text-slate-800">
        <header class="border-b border-slate-200 pb-6">
            <h1 class="text-3xl font-bold">Reporte de Gastos e Ingresos</h1>
            <p class="mt-2 text-sm text-slate-600">Modo: {{ modeLabel }}</p>
            <p class="text-sm text-slate-600">Periodo: {{ formatRange(filters) }}</p>
        </header>

        <section>
            <h2 class="text-2xl font-semibold mb-4">Resumen general</h2>
            <table class="min-w-full border border-slate-300 text-sm">
                <thead class="bg-slate-100 text-left font-semibold text-slate-600">
                    <tr>
                        <th class="px-4 py-2">Indicador</th>
                        <th class="px-4 py-2 text-right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border-t border-slate-200 px-4 py-2">Ingresos totales (IVA incluido)</td>
                        <td class="border-t border-slate-200 px-4 py-2 text-right">{{ formatCurrency(overallSummary.total_income) }}</td>
                    </tr>
                    <tr>
                        <td class="border-t border-slate-200 px-4 py-2">Gastos totales (IVA incluido)</td>
                        <td class="border-t border-slate-200 px-4 py-2 text-right">{{ formatCurrency(overallSummary.total_expense_total) }}</td>
                    </tr>
                    <tr>
                        <td class="border-t border-slate-200 px-4 py-2">IVA neto</td>
                        <td class="border-t border-slate-200 px-4 py-2 text-right">{{ formatCurrency(overallSummary.iva_balance) }}</td>
                    </tr>
                    <tr>
                        <td class="border-t border-slate-200 px-4 py-2">Margen bruto</td>
                        <td class="border-t border-slate-200 px-4 py-2 text-right">{{ formatCurrency(overallSummary.gross_margin) }}</td>
                    </tr>
                    <tr>
                        <td class="border-t border-slate-200 px-4 py-2 font-semibold">Saldo neto</td>
                        <td class="border-t border-slate-200 px-4 py-2 text-right font-semibold">{{ formatCurrency(overallSummary.net_balance) }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section>
            <h2 class="text-2xl font-semibold mb-4">Resumen por periodo</h2>
            <div v-if="mode === 'monthly'" class="space-y-8">
                <div v-for="year in monthlyYears" :key="year.year" class="border border-slate-300">
                    <header class="border-b border-slate-200 bg-slate-50 px-4 py-3 font-semibold">{{ year.year }}</header>
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-100 text-left text-slate-600">
                            <tr>
                                <th class="px-4 py-2">Mes</th>
                                <th class="px-4 py-2 text-right">Ingresos</th>
                                <th class="px-4 py-2 text-right">Gastos</th>
                                <th class="px-4 py-2 text-right">IVA</th>
                                <th class="px-4 py-2 text-right">Margen bruto</th>
                                <th class="px-4 py-2 text-right">Saldo neto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="month in year.months" :key="month.key" class="border-t border-slate-200">
                                <td class="px-4 py-2">{{ month.label }}</td>
                                <td class="px-4 py-2 text-right">{{ formatCurrency(month.summary.total_income) }}</td>
                                <td class="px-4 py-2 text-right">{{ formatCurrency(month.summary.total_expense_total) }}</td>
                                <td class="px-4 py-2 text-right">{{ formatCurrency(month.summary.iva_balance) }}</td>
                                <td class="px-4 py-2 text-right">{{ formatCurrency(month.summary.gross_margin) }}</td>
                                <td class="px-4 py-2 text-right">{{ formatCurrency(month.summary.net_balance) }}</td>
                            </tr>
                            <tr v-if="year.months.length === 0">
                                <td colspan="6" class="px-4 py-3 text-center text-slate-400">Sin datos para este año.</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-50 font-semibold text-slate-700">
                            <tr>
                                <td class="px-4 py-2">Totales {{ year.year }}</td>
                                <td class="px-4 py-2 text-right">{{ formatCurrency(year.summary.total_income) }}</td>
                                <td class="px-4 py-2 text-right">{{ formatCurrency(year.summary.total_expense_total) }}</td>
                                <td class="px-4 py-2 text-right">{{ formatCurrency(year.summary.iva_balance) }}</td>
                                <td class="px-4 py-2 text-right">{{ formatCurrency(year.summary.gross_margin) }}</td>
                                <td class="px-4 py-2 text-right">{{ formatCurrency(year.summary.net_balance) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div v-else>
                <table class="min-w-full border border-slate-300 text-sm">
                    <thead class="bg-slate-100 text-left text-slate-600">
                        <tr>
                            <th class="px-4 py-2">Periodo</th>
                            <th class="px-4 py-2 text-right">Ingresos</th>
                            <th class="px-4 py-2 text-right">Gastos</th>
                            <th class="px-4 py-2 text-right">IVA</th>
                            <th class="px-4 py-2 text-right">Margen bruto</th>
                            <th class="px-4 py-2 text-right">Saldo neto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="group in groups" :key="group.key" class="border-t border-slate-200">
                            <td class="px-4 py-2">{{ group.label }}</td>
                            <td class="px-4 py-2 text-right">{{ formatCurrency(group.summary.total_income) }}</td>
                            <td class="px-4 py-2 text-right">{{ formatCurrency(group.summary.total_expense_total) }}</td>
                            <td class="px-4 py-2 text-right">{{ formatCurrency(group.summary.iva_balance) }}</td>
                            <td class="px-4 py-2 text-right">{{ formatCurrency(group.summary.gross_margin) }}</td>
                            <td class="px-4 py-2 text-right">{{ formatCurrency(group.summary.net_balance) }}</td>
                        </tr>
                        <tr v-if="groups.length === 0">
                            <td colspan="6" class="px-4 py-3 text-center text-slate-400">No hay datos para el periodo seleccionado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold mb-4">Detalle por periodo</h2>
            <div class="space-y-8">
                <article v-for="group in groups" :key="group.key" class="border border-slate-300">
                    <header class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                        <h3 class="text-lg font-semibold">{{ group.label }}</h3>
                        <p class="text-sm text-slate-600">Periodo: {{ formatRange(group.range) }}</p>
                        <p class="text-sm text-slate-600">
                            IVA: {{ formatCurrency(group.summary.iva_balance) }} · Margen bruto: {{ formatCurrency(group.summary.gross_margin) }} · Saldo neto: {{ formatCurrency(group.summary.net_balance) }}
                        </p>
                    </header>
                    <div class="grid grid-cols-1 gap-6 p-4 lg:grid-cols-2">
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-widest text-slate-500 mb-2">Ingresos ({{ group.incomes.totals.count }})</h4>
                            <table class="min-w-full border border-slate-200 text-xs">
                                <thead class="bg-slate-100 text-left text-slate-600">
                                    <tr>
                                        <th class="px-3 py-2">Fecha</th>
                                        <th class="px-3 py-2">Nombre</th>
                                        <th class="px-3 py-2">Origen</th>
                                        <th class="px-3 py-2 text-right">Base</th>
                                        <th class="px-3 py-2 text-right">IVA</th>
                                        <th class="px-3 py-2 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="income in group.incomes.items" :key="income.id" class="border-t border-slate-200">
                                        <td class="px-3 py-2">{{ formatDate(income.date) }}</td>
                                        <td class="px-3 py-2">{{ income.name }}</td>
                                        <td class="px-3 py-2">{{ income.source || '—' }}</td>
                                        <td class="px-3 py-2 text-right">{{ formatCurrency(income.tax_base ?? 0) }}</td>
                                        <td class="px-3 py-2 text-right">{{ formatCurrency(income.tax_amount ?? (income.tax_base ?? 0) * (income.tax_rate ?? 0) / 100) }}</td>
                                        <td class="px-3 py-2 text-right">{{ formatCurrency(income.total_amount ?? (income.tax_base ?? 0) + (income.tax_amount ?? 0)) }}</td>
                                    </tr>
                                    <tr v-if="group.incomes.items.length === 0">
                                        <td colspan="6" class="px-3 py-3 text-center text-slate-400">Sin ingresos registrados.</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-slate-100 font-semibold text-slate-600">
                                    <tr>
                                        <td colspan="3" class="px-3 py-2">Totales</td>
                                        <td class="px-3 py-2 text-right">{{ formatCurrency(group.incomes.totals.base) }}</td>
                                        <td class="px-3 py-2 text-right">{{ formatCurrency(group.incomes.totals.tax) }}</td>
                                        <td class="px-3 py-2 text-right">{{ formatCurrency(group.incomes.totals.total) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-widest text-slate-500 mb-2">Gastos ({{ group.expenses.totals.count }})</h4>
                            <table class="min-w-full border border-slate-200 text-xs">
                                <thead class="bg-slate-100 text-left text-slate-600">
                                    <tr>
                                        <th class="px-3 py-2">Fecha</th>
                                        <th class="px-3 py-2">Nombre</th>
                                        <th class="px-3 py-2">Descripción</th>
                                        <th class="px-3 py-2 text-right">Base</th>
                                        <th class="px-3 py-2 text-right">IVA</th>
                                        <th class="px-3 py-2 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="expense in group.expenses.items" :key="expense.id" class="border-t border-slate-200">
                                        <td class="px-3 py-2">{{ formatDate(expense.date) }}</td>
                                        <td class="px-3 py-2">{{ expense.name }}</td>
                                        <td class="px-3 py-2">{{ expense.description || '—' }}</td>
                                        <td class="px-3 py-2 text-right">{{ formatCurrency(expense.amount) }}</td>
                                        <td class="px-3 py-2 text-right">{{ formatCurrency(expense.amount * (expense.iva ?? 0) / 100) }}</td>
                                        <td class="px-3 py-2 text-right">{{ formatCurrency(expense.amount + expense.amount * (expense.iva ?? 0) / 100) }}</td>
                                    </tr>
                                    <tr v-if="group.expenses.items.length === 0">
                                        <td colspan="6" class="px-3 py-3 text-center text-slate-400">Sin gastos registrados.</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-slate-100 font-semibold text-slate-600">
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
                </article>
            </div>
        </section>

        <footer class="border-t border-slate-200 pt-6">
            <button @click="printReport" class="bg-blue-900 text-white px-4 py-2 rounded shadow">Imprimir reporte</button>
        </footer>
    </div>
</template>

<script setup>
import { computed } from 'vue';

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

const mode = computed(() => props.report?.mode ?? 'general');
const modeLabel = computed(() => {
    switch (mode.value) {
        case 'monthly':
            return 'Mensual';
        case 'annual':
            return 'Anual';
        default:
            return 'General';
    }
});

const groups = computed(() => props.report?.groups ?? []);
const overallSummary = computed(() => props.report?.overall?.summary ?? createEmptySummary());

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

const filters = computed(() => ({
    start: props.filters.start_date ?? null,
    end: props.filters.end_date ?? null,
}));

const printReport = () => window.print();

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
    if (range.start && range.end && range.start === range.end) {
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
