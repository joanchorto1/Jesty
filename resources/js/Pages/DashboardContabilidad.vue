<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-slate-900 via-emerald-700 to-blue-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2">
                        <p class="text-emerald-200 text-sm uppercase tracking-widest">Salud financiera</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Resumen contable integral</h1>
                        <p class="text-sm text-emerald-200">Comprende la evolución de ingresos, gastos y rentabilidad en un vistazo.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Ingresos</p>
                            <p class="text-3xl font-semibold mt-2">€{{ totalIncomes }}</p>
                            <p class="text-sm text-emerald-200 mt-3">Promedio mensual €{{ averageIncome }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Gastos</p>
                            <p class="text-3xl font-semibold mt-2">€{{ totalExpenses }}</p>
                            <p class="text-sm text-emerald-200 mt-3">Coste medio €{{ averageExpense }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Beneficio neto</p>
                            <p class="text-3xl font-semibold mt-2">€{{ profit }}</p>
                            <p class="text-sm text-emerald-200 mt-3">Margen {{ profitMargin }}%</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Transacciones</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalTransactions }}</p>
                            <p class="text-sm text-emerald-200 mt-3">{{ incomes.length }} ingresos • {{ expenses.length }} gastos</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 pb-5">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Ingresos acumulados</h2>
                            <p class="text-sm text-slate-500 mt-1">Sigue la evolución mensual filtrando por el año deseado.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <label for="year" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Año</label>
                            <select id="year" v-model="selectedYear" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                                <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6">
                        <BarChart :data="cumulativeIncomeData" />
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Balance mensual</h2>
                        <p class="text-sm text-slate-500">Comparativa entre ingresos y gastos con resultado neto.</p>
                        <div class="mt-6">
                            <AreaChart :data="monthlyBalanceChart" />
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Indicadores clave</h2>
                        <p class="text-sm text-slate-500">Analiza métricas estratégicas de rentabilidad y actividad.</p>
                        <div class="mt-6">
                            <LineChart :data="metricsComparisonData" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <h2 class="text-xl font-semibold text-slate-800">Gastos mensuales</h2>
                    <p class="text-sm text-slate-500">Controla la distribución de costes a lo largo del año.</p>
                    <div class="mt-6">
                        <BarChart :data="monthlyExpensesData" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BarChart from '@/Components/BarChart.vue';
import LineChart from '@/Components/LineChart.vue';
import AreaChart from '@/Components/AreaChart.vue';

const props = defineProps({
    incomes: Array,
    expenses: Array,
    clients: Array,
});

const incomes = computed(() => props.incomes);
const expenses = computed(() => props.expenses);

const selectedYear = ref(new Date().getFullYear());

const availableYears = computed(() => {
    const yearsFromIncomes = incomes.value.map(income => new Date(income.date).getFullYear());
    const yearsFromExpenses = expenses.value.map(expense => new Date(expense.date).getFullYear());
    const years = [...new Set([...yearsFromIncomes, ...yearsFromExpenses])].sort((a, b) => b - a);
    return years.length ? years : [selectedYear.value];
});

const totalIncomes = computed(() => incomes.value.reduce((acc, income) => acc + (income.total_amount ?? 0), 0).toFixed(2));
const totalExpenses = computed(() => expenses.value.reduce((acc, expense) => acc + (expense.amount ?? 0), 0).toFixed(2));
const profit = computed(() => (totalIncomes.value - totalExpenses.value).toFixed(2));
const totalTransactions = computed(() => incomes.value.length + expenses.value.length);

const averageIncome = computed(() => {
    if (!incomes.value.length) {
        return '0.00';
    }
    return (incomes.value.reduce((acc, income) => acc + (income.total_amount ?? 0), 0) / incomes.value.length).toFixed(2);
});

const averageExpense = computed(() => {
    if (!expenses.value.length) {
        return '0.00';
    }
    return (expenses.value.reduce((acc, expense) => acc + (expense.amount ?? 0), 0) / expenses.value.length).toFixed(2);
});

const profitMargin = computed(() => {
    const incomesValue = parseFloat(totalIncomes.value);
    if (!incomesValue) {
        return 0;
    }
    return Math.round((parseFloat(profit.value) / incomesValue) * 100);
});

const cumulativeIncomeData = computed(() => {
    const dataByMonth = Array(12).fill(0);

    incomes.value
        .filter(income => new Date(income.date).getFullYear() === selectedYear.value)
        .forEach(income => {
            const monthIndex = new Date(income.date).getMonth();
            dataByMonth[monthIndex] += income.total_amount ?? 0;
        });

    return {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [
            {
                label: 'Ingresos',
                backgroundColor: 'rgba(16, 185, 129, 0.45)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1,
                data: dataByMonth,
            },
        ],
    };
});

const metricsComparisonData = computed(() => ({
    labels: ['Ingresos', 'Gastos', 'Ganancia', 'Transacciones'],
    datasets: [
        {
            label: 'Indicadores',
            data: [parseFloat(totalIncomes.value), parseFloat(totalExpenses.value), parseFloat(profit.value), totalTransactions.value],
            fill: false,
            borderColor: 'rgba(59, 130, 246, 1)',
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderWidth: 2,
        },
    ],
}));

const monthlyExpensesData = computed(() => {
    const monthlyTotals = Array(12).fill(0);

    expenses.value
        .filter(expense => new Date(expense.date).getFullYear() === selectedYear.value)
        .forEach(expense => {
            const monthIndex = new Date(expense.date).getMonth();
            monthlyTotals[monthIndex] += expense.amount ?? 0;
        });

    return {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [
            {
                label: 'Gastos',
                backgroundColor: 'rgba(248, 113, 113, 0.45)',
                borderColor: 'rgba(248, 113, 113, 1)',
                borderWidth: 1,
                data: monthlyTotals,
            },
        ],
    };
});

const monthlyBalanceChart = computed(() => {
    const monthlyIncome = Array(12).fill(0);
    const monthlyExpense = Array(12).fill(0);

    incomes.value.forEach(income => {
        const date = new Date(income.date);
        if (date.getFullYear() !== selectedYear.value) return;
        monthlyIncome[date.getMonth()] += income.total_amount ?? 0;
    });

    expenses.value.forEach(expense => {
        const date = new Date(expense.date);
        if (date.getFullYear() !== selectedYear.value) return;
        monthlyExpense[date.getMonth()] += expense.amount ?? 0;
    });

    const net = monthlyIncome.map((value, index) => value - monthlyExpense[index]);

    return {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [
            {
                label: 'Ingreso',
                data: monthlyIncome,
                fill: 'origin',
                backgroundColor: 'rgba(16, 185, 129, 0.25)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 2,
            },
            {
                label: 'Gasto',
                data: monthlyExpense,
                fill: 'origin',
                backgroundColor: 'rgba(244, 63, 94, 0.25)',
                borderColor: 'rgba(244, 63, 94, 1)',
                borderWidth: 2,
            },
            {
                label: 'Resultado neto',
                data: net,
                fill: false,
                borderColor: 'rgba(14, 165, 233, 1)',
                backgroundColor: 'rgba(14, 165, 233, 0.25)',
                borderWidth: 2,
            },
        ],
    };
});
</script>
