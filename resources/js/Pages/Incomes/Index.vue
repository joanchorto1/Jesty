<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 print:bg-white">
            <FinancePageHeader
                eyebrow="Control financiero"
                title="Ingresos registrados"
                description="Explora la evolución de tus ingresos, analiza su origen y mantén un registro claro con herramientas preparadas para impresión."
            >
                <template #actions>
                    <NavLink
                        :href="route('incomes.create')"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        <span>Registrar ingreso</span>
                    </NavLink>
                    <button
                        type="button"
                        @click="printPage"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        Imprimir informe
                    </button>
                </template>

                <template #metrics>
                    <FinanceSummaryCard label="Total ingresos" :value="formatCurrency(totalGross)" :helper="`Impuestos incluidos: ${formatCurrency(totalTax)}`" />
                    <FinanceSummaryCard label="Base imponible" :value="formatCurrency(totalBase)" :helper="`IVA medio ${averageTaxRate}%`" />
                    <FinanceSummaryCard label="Ticket medio" :value="formatCurrency(averageTicket)" :helper="`${incomes.length} registros`" />
                    <FinanceSummaryCard label="Máximo registrado" :value="formatCurrency(highestIncome)" :helper="highestIncomeSource" />
                </template>
            </FinancePageHeader>

            <main class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10 print:mt-0 print:space-y-6 print:px-0">
                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Resumen mensual</h2>
                            <p class="text-sm text-slate-500">Suma las entradas por mes para detectar patrones de estacionalidad.</p>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-500">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                            Ingresos brutos
                        </div>
                    </header>
                    <div class="mt-6">
                        <BarChart :data="monthlyIncomeData" />
                    </div>
                </section>

                <section class="grid grid-cols-1 gap-8 xl:grid-cols-2">
                    <article class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                        <header class="border-b border-slate-100 pb-4">
                            <h2 class="text-xl font-semibold text-slate-800">Distribución por origen</h2>
                            <p class="text-sm text-slate-500">Comprueba qué fuentes aportan un mayor volumen de ingresos.</p>
                        </header>
                        <div class="mt-6">
                            <DoughnutChart :data="sourceDistributionData" />
                        </div>
                    </article>

                    <article class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                        <header class="border-b border-slate-100 pb-4">
                            <h2 class="text-xl font-semibold text-slate-800">Detalle registrado</h2>
                            <p class="text-sm text-slate-500">Consulta rápidamente las cifras clave de cada ingreso.</p>
                        </header>
                        <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                                <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Desviación media</dt>
                                <dd class="mt-2 text-xl font-semibold">{{ formatCurrency(averageDeviation) }}</dd>
                                <p class="mt-2 text-xs text-slate-500">Indicador de dispersión frente al ticket medio.</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                                <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Último registro</dt>
                                <dd class="mt-2 text-xl font-semibold">{{ lastIncomeFormatted.amount }}</dd>
                                <p class="mt-2 text-xs text-slate-500">{{ lastIncomeFormatted.date }} · {{ lastIncomeFormatted.source }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                                <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Primer registro</dt>
                                <dd class="mt-2 text-xl font-semibold">{{ firstIncomeFormatted.amount }}</dd>
                                <p class="mt-2 text-xs text-slate-500">{{ firstIncomeFormatted.date }} · {{ firstIncomeFormatted.source }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                                <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Fuentes activas</dt>
                                <dd class="mt-2 text-xl font-semibold">{{ uniqueSources }}</dd>
                                <p class="mt-2 text-xs text-slate-500">Referencias distintas en el periodo.</p>
                            </div>
                        </dl>
                    </article>
                </section>

                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-0">
                    <header class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Detalle de ingresos</h2>
                            <p class="text-sm text-slate-500">Tabla optimizada para manejar grandes volúmenes con totales calculados.</p>
                        </div>
                        <div class="text-right text-sm text-slate-500">
                            Total registros: {{ incomes.length }}
                        </div>
                    </header>
                    <div class="mt-6 overflow-x-auto print:overflow-visible">
                        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600">
                            <thead class="bg-slate-50/80 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Nombre</th>
                                    <th scope="col" class="px-4 py-3">Origen</th>
                                    <th scope="col" class="px-4 py-3 text-right">Base imponible</th>
                                    <th scope="col" class="px-4 py-3 text-right">IVA (%)</th>
                                    <th scope="col" class="px-4 py-3 text-right">IVA</th>
                                    <th scope="col" class="px-4 py-3 text-right">Total</th>
                                    <th scope="col" class="px-4 py-3">Fecha</th>
                                    <th scope="col" class="px-4 py-3 text-center print:hidden">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr
                                    v-for="income in incomes"
                                    :key="income.id"
                                    class="bg-white/60 transition hover:bg-emerald-50/60"
                                >
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ income.name }}</td>
                                    <td class="px-4 py-3">{{ income.source || 'Sin especificar' }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(income.tax_base ?? 0) }}</td>
                                    <td class="px-4 py-3 text-right">{{ income.tax_rate ?? 0 }}%</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(taxAmount(income)) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalAmount(income)) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(income.date) }}</td>
                                    <td class="px-4 py-3 text-center print:hidden">
                                        <div class="flex justify-center gap-3">
                                            <NavLink :href="route('incomes.edit', income.id)" class="text-emerald-600 hover:text-emerald-800" title="Editar">
                                                <EditIcon class="w-5 h-5" />
                                            </NavLink>
                                            <button
                                                type="button"
                                                @click="deleteIncome(income.id)"
                                                class="text-rose-500 hover:text-rose-700"
                                                title="Eliminar"
                                            >
                                                <DeleteIcon class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-slate-50/80 text-xs uppercase tracking-widest text-slate-500">
                                <tr>
                                    <td class="px-4 py-3" colspan="2">Totales</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalBase) }}</td>
                                    <td class="px-4 py-3 text-right">—</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalTax) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalGross) }}</td>
                                    <td class="px-4 py-3" colspan="2"></td>
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
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FinancePageHeader from '@/Components/Finance/FinancePageHeader.vue';
import FinanceSummaryCard from '@/Components/Finance/FinanceSummaryCard.vue';
import BarChart from '@/Components/BarChart.vue';
import DoughnutChart from '@/Components/DoughnutChart.vue';
import NavLink from '@/Components/NavLink.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';

const props = defineProps({
    incomes: {
        type: Array,
        default: () => [],
    },
});

const incomes = computed(() => props.incomes ?? []);

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

const taxAmount = (income) => {
    const base = Number(income?.tax_base ?? 0);
    const rate = Number(income?.tax_rate ?? 0);
    return base * rate / 100;
};

const totalAmount = (income) => {
    const base = Number(income?.tax_base ?? 0);
    return base + taxAmount(income);
};

const totalBase = computed(() => incomes.value.reduce((acc, income) => acc + Number(income?.tax_base ?? 0), 0));
const totalTax = computed(() => incomes.value.reduce((acc, income) => acc + taxAmount(income), 0));
const totalGross = computed(() => totalBase.value + totalTax.value);

const averageTicket = computed(() => {
    if (!incomes.value.length) {
        return 0;
    }
    return totalGross.value / incomes.value.length;
});

const averageTaxRate = computed(() => {
    if (!incomes.value.length) {
        return 0;
    }
    const totalRate = incomes.value.reduce((acc, income) => acc + Number(income?.tax_rate ?? 0), 0);
    return (totalRate / incomes.value.length).toFixed(1);
});

const highestIncome = computed(() => {
    if (!incomes.value.length) {
        return 0;
    }
    return Math.max(...incomes.value.map(income => totalAmount(income)));
});

const highestIncomeSource = computed(() => {
    if (!incomes.value.length) {
        return 'Sin registros';
    }
    const richest = incomes.value.reduce((acc, income) => {
        const amount = totalAmount(income);
        if (!acc || amount > acc.amount) {
            return { amount, source: income.source || 'Sin origen' };
        }
        return acc;
    }, null);

    return richest ? `Origen ${richest.source}` : 'Sin registros';
});

const uniqueSources = computed(() => {
    const sources = new Set(incomes.value.map(income => income.source || 'Sin origen'));
    return sources.size;
});

const sortedIncomes = computed(() => {
    return [...incomes.value].sort((a, b) => new Date(a.date) - new Date(b.date));
});

const lastIncomeFormatted = computed(() => {
    if (!sortedIncomes.value.length) {
        return { amount: formatCurrency(0), date: 'Sin fecha', source: 'Sin origen' };
    }
    const income = sortedIncomes.value[sortedIncomes.value.length - 1];
    return {
        amount: formatCurrency(totalAmount(income)),
        date: formatDate(income.date),
        source: income.source || 'Sin origen',
    };
});

const firstIncomeFormatted = computed(() => {
    if (!sortedIncomes.value.length) {
        return { amount: formatCurrency(0), date: 'Sin fecha', source: 'Sin origen' };
    }
    const income = sortedIncomes.value[0];
    return {
        amount: formatCurrency(totalAmount(income)),
        date: formatDate(income.date),
        source: income.source || 'Sin origen',
    };
});

const averageDeviation = computed(() => {
    if (incomes.value.length <= 1) {
        return 0;
    }
    const avg = averageTicket.value;
    const variance = incomes.value.reduce((acc, income) => {
        const diff = totalAmount(income) - avg;
        return acc + diff * diff;
    }, 0) / (incomes.value.length - 1);
    return Math.sqrt(variance);
});

const monthlyIncomeData = computed(() => {
    const monthlyTotals = Array(12).fill(0);
    incomes.value.forEach(income => {
        const date = new Date(income.date);
        if (!Number.isNaN(date.getTime())) {
            monthlyTotals[date.getMonth()] += totalAmount(income);
        }
    });

    return {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [
            {
                label: 'Ingresos brutos',
                backgroundColor: 'rgba(16, 185, 129, 0.45)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1,
                data: monthlyTotals,
            },
        ],
    };
});

const palette = ['#0EA5E9', '#10B981', '#6366F1', '#F97316', '#F43F5E', '#8B5CF6', '#14B8A6', '#F59E0B', '#0F172A', '#84CC16', '#A855F7', '#0EA5E9'];

const sourceDistributionData = computed(() => {
    if (!incomes.value.length) {
        return {
            labels: ['Sin datos'],
            datasets: [
                {
                    data: [1],
                    backgroundColor: ['rgba(148, 163, 184, 0.45)'],
                    borderColor: ['rgba(148, 163, 184, 1)'],
                },
            ],
        };
    }

    const totals = incomes.value.reduce((acc, income) => {
        const source = income.source || 'Sin origen';
        acc[source] = (acc[source] || 0) + totalAmount(income);
        return acc;
    }, {});

    const labels = Object.keys(totals);
    const data = Object.values(totals);

    return {
        labels,
        datasets: [
            {
                data,
                backgroundColor: labels.map((_, index) => palette[index % palette.length] + '73'),
                borderColor: labels.map((_, index) => palette[index % palette.length]),
            },
        ],
    };
});

const printPage = () => {
    window.print();
};

const deleteIncome = (id) => {
    if (confirm('¿Estás seguro de eliminar este ingreso?')) {
        router.delete(route('incomes.destroy', id));
    }
};
</script>
