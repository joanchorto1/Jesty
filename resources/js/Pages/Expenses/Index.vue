<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 print:bg-white">
            <FinancePageHeader
                eyebrow="Control financiero"
                title="Gastos operativos"
                description="Visualiza los gastos con filtros dinámicos, gráficas comparables y tarjetas en línea con el panel contable."
            >
                <template #actions>
                    <NavLink
                        :href="route('expenses.create')"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        <AddIcon class="h-4 w-4" />
                        <span>Registrar gasto</span>
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
                    <FinanceSummaryCard label="Gasto total" :value="formatCurrency(totalGrossAmount)" :helper="`${visibleExpenses.length} registros visibles`" />
                    <FinanceSummaryCard label="Base imponible" :value="formatCurrency(totalNetAmount)" :helper="`IVA acumulado ${formatCurrency(totalTaxAmount)}`" />
                    <FinanceSummaryCard label="Ticket medio" :value="formatCurrency(averageGrossAmount)" :helper="`Rango ${formatCurrency(lowestExpenseGross)} – ${formatCurrency(highestExpenseGross)}`" />
                    <FinanceSummaryCard label="Mayor gasto" :value="formatCurrency(highestExpenseGross)" :helper="highestExpenseDescriptor" />
                </template>
            </FinancePageHeader>

            <main class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10 print:mt-0 print:space-y-6 print:px-0">
                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Filtrar gastos</h2>
                            <p class="text-sm text-slate-500">Combina filtros para focalizar tus análisis. Las gráficas y totales se recalculan al instante.</p>
                        </div>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-200"
                            @click="resetFilters"
                        >
                            Limpiar filtros
                        </button>
                    </header>
                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <label class="flex flex-col text-sm font-medium text-slate-600">
                            Método de pago
                            <select
                                v-model="selectedPaymentMethod"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            >
                                <option value="">Todos</option>
                                <option
                                    v-for="paymentMethod in paymentMethods"
                                    :key="paymentMethod.id"
                                    :value="String(paymentMethod.id)"
                                >
                                    {{ paymentMethod.name }}
                                </option>
                            </select>
                        </label>

                        <label class="flex flex-col text-sm font-medium text-slate-600">
                            Desde
                            <input
                                type="date"
                                v-model="startDate"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            />
                        </label>

                        <label class="flex flex-col text-sm font-medium text-slate-600">
                            Hasta
                            <input
                                type="date"
                                v-model="endDate"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            />
                        </label>

                        <label class="flex flex-col text-sm font-medium text-slate-600">
                            Categoría
                            <select
                                v-model="selectedCategory"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            >
                                <option value="">Todas</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="String(category.id)"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                        </label>
                    </div>
                </section>

                <section class="grid grid-cols-1 gap-8 xl:grid-cols-2">
                    <article class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                        <header class="flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-800">Gasto mensual</h2>
                                <p class="text-sm text-slate-500">Controla la evolución temporal de los desembolsos seleccionados.</p>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-500">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                                Total mensual
                            </div>
                        </header>
                        <div class="mt-6">
                            <BarChart :data="monthlyExpensesData" />
                        </div>
                    </article>

                    <article class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                        <header class="border-b border-slate-100 pb-4">
                            <h2 class="text-xl font-semibold text-slate-800">Distribución por categoría</h2>
                            <p class="text-sm text-slate-500">Comprende qué líneas de gasto concentran más recursos.</p>
                        </header>
                        <div class="mt-6">
                            <DoughnutChart :data="categoryDistributionData" />
                        </div>
                    </article>
                </section>

                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-0">
                    <header class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Relación detallada de gastos</h2>
                            <p class="text-sm text-slate-500">Información preparada para manejar grandes volúmenes y exportar en papel.</p>
                        </div>
                        <div class="text-right text-sm text-slate-500">
                            {{ filteredCountMessage }}
                        </div>
                    </header>
                    <div class="mt-6 overflow-x-auto print:overflow-visible">
                        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600">
                            <thead class="bg-slate-50/80 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Identificador</th>
                                    <th scope="col" class="px-4 py-3">Nombre</th>
                                    <th scope="col" class="px-4 py-3">Descripción</th>
                                    <th scope="col" class="px-4 py-3 text-right">Base imponible</th>
                                    <th scope="col" class="px-4 py-3 text-right">IVA</th>
                                    <th scope="col" class="px-4 py-3 text-right">Total</th>
                                    <th scope="col" class="px-4 py-3">Fecha</th>
                                    <th scope="col" class="px-4 py-3">Método</th>
                                    <th scope="col" class="px-4 py-3">Categoría</th>
                                    <th scope="col" class="px-4 py-3 text-center print:hidden">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr
                                    v-for="expense in visibleExpenses"
                                    :key="expense.id"
                                    class="bg-white/60 transition hover:bg-rose-50/60"
                                >
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ expense.id }}</td>
                                    <td class="px-4 py-3">{{ expense.name }}</td>
                                    <td class="px-4 py-3 max-w-xs truncate" :title="expense.description">{{ expense.description || '—' }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(expense.amount ?? 0) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(taxAmount(expense)) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(grossAmount(expense)) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(expense.date) }}</td>
                                    <td class="px-4 py-3">{{ paymentMethodName(expense.payment_method_id) }}</td>
                                    <td class="px-4 py-3">{{ categoryName(expense.expense_category_id) }}</td>
                                    <td class="px-4 py-3 text-center print:hidden">
                                        <div class="flex justify-center gap-3">
                                            <NavLink :href="route('expenses.show', expense.id)" class="text-slate-500 hover:text-slate-700" title="Ver detalle">
                                                <InfoIcon class="w-5 h-5" />
                                            </NavLink>
                                            <NavLink :href="route('expenses.edit', expense.id)" class="text-emerald-600 hover:text-emerald-800" title="Editar">
                                                <EditIcon class="w-5 h-5" />
                                            </NavLink>
                                            <button
                                                type="button"
                                                @click="deleteExpense(expense.id)"
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
                                    <td class="px-4 py-3" colspan="3">Totales</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalNetAmount) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalTaxAmount) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalGrossAmount) }}</td>
                                    <td class="px-4 py-3" colspan="4"></td>
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
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import FinancePageHeader from '@/Components/Finance/FinancePageHeader.vue';
import FinanceSummaryCard from '@/Components/Finance/FinanceSummaryCard.vue';
import BarChart from '@/Components/BarChart.vue';
import DoughnutChart from '@/Components/DoughnutChart.vue';
import NavLink from '@/Components/NavLink.vue';
import InfoIcon from '@/Components/Icons/InfoIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';

const props = defineProps({
    expenses: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    paymentMethods: {
        type: Array,
        default: () => [],
    },
});

const expenses = computed(() => props.expenses ?? []);
const categories = computed(() => props.categories ?? []);
const paymentMethods = computed(() => props.paymentMethods ?? []);

const selectedPaymentMethod = ref('');
const startDate = ref('');
const endDate = ref('');
const selectedCategory = ref('');

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

const taxAmount = (expense) => {
    const amount = Number(expense?.amount ?? 0);
    const rate = Number(expense?.iva ?? 0);
    return amount * rate / 100;
};

const grossAmount = (expense) => {
    const amount = Number(expense?.amount ?? 0);
    return amount + taxAmount(expense);
};

const matchesDate = (expense) => {
    const date = new Date(expense.date);
    if (Number.isNaN(date.getTime())) {
        return true;
    }
    const from = startDate.value ? new Date(startDate.value) : null;
    const to = endDate.value ? new Date(endDate.value) : null;

    if (from && date < from) {
        return false;
    }

    if (to && date > to) {
        return false;
    }

    return true;
};

const visibleExpenses = computed(() => {
    return expenses.value.filter(expense => {
        const matchesPaymentMethod = selectedPaymentMethod.value
            ? String(expense.payment_method_id) === selectedPaymentMethod.value
            : true;

        const matchesCategory = selectedCategory.value
            ? String(expense.expense_category_id) === selectedCategory.value
            : true;

        return matchesPaymentMethod && matchesCategory && matchesDate(expense);
    });
});

const filteredCountMessage = computed(() => {
    if (visibleExpenses.value.length === expenses.value.length) {
        return `${expenses.value.length} registros`; 
    }
    return `${visibleExpenses.value.length} de ${expenses.value.length} registros`;
});

const totalNetAmount = computed(() => visibleExpenses.value.reduce((acc, expense) => acc + Number(expense?.amount ?? 0), 0));
const totalTaxAmount = computed(() => visibleExpenses.value.reduce((acc, expense) => acc + taxAmount(expense), 0));
const totalGrossAmount = computed(() => totalNetAmount.value + totalTaxAmount.value);

const averageGrossAmount = computed(() => {
    if (!visibleExpenses.value.length) {
        return 0;
    }
    return totalGrossAmount.value / visibleExpenses.value.length;
});

const highestExpenseGross = computed(() => {
    if (!visibleExpenses.value.length) {
        return 0;
    }
    return Math.max(...visibleExpenses.value.map(expense => grossAmount(expense)));
});

const lowestExpenseGross = computed(() => {
    if (!visibleExpenses.value.length) {
        return 0;
    }
    return Math.min(...visibleExpenses.value.map(expense => grossAmount(expense)));
});

const highestExpenseDescriptor = computed(() => {
    if (!visibleExpenses.value.length) {
        return 'Sin registros';
    }
    const richest = visibleExpenses.value.reduce((acc, expense) => {
        const amount = grossAmount(expense);
        if (!acc || amount > acc.amount) {
            return {
                amount,
                categoryId: expense.expense_category_id,
                methodId: expense.payment_method_id,
            };
        }
        return acc;
    }, null);

    const category = categories.value.find(cat => cat.id === richest?.categoryId);
    const method = paymentMethods.value.find(method => method.id === richest?.methodId);
    const categoryNameValue = category?.name || 'Sin categoría';
    const methodNameValue = method?.name || 'Sin método';

    return `${categoryNameValue} · ${methodNameValue}`;
});

const monthlyExpensesData = computed(() => {
    const totals = Array(12).fill(0);
    visibleExpenses.value.forEach(expense => {
        const date = new Date(expense.date);
        if (!Number.isNaN(date.getTime())) {
            totals[date.getMonth()] += grossAmount(expense);
        }
    });

    return {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [
            {
                label: 'Total mensual',
                backgroundColor: 'rgba(244, 63, 94, 0.45)',
                borderColor: 'rgba(244, 63, 94, 1)',
                borderWidth: 1,
                data: totals,
            },
        ],
    };
});

const palette = ['#0EA5E9', '#10B981', '#6366F1', '#F97316', '#F43F5E', '#8B5CF6', '#14B8A6', '#F59E0B', '#0F172A', '#84CC16', '#A855F7', '#0EA5E9'];

const categoryDistributionData = computed(() => {
    if (!visibleExpenses.value.length) {
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

    const totals = visibleExpenses.value.reduce((acc, expense) => {
        const category = categoryName(expense.expense_category_id);
        acc[category] = (acc[category] || 0) + grossAmount(expense);
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

const paymentMethodName = (id) => {
    const method = paymentMethods.value.find(item => item.id === id);
    return method?.name || 'Sin método';
};

const categoryName = (id) => {
    const category = categories.value.find(item => item.id === id);
    return category?.name || 'Sin categoría';
};

const resetFilters = () => {
    selectedPaymentMethod.value = '';
    startDate.value = '';
    endDate.value = '';
    selectedCategory.value = '';
};

const printPage = () => {
    window.print();
};

const deleteExpense = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este gasto?')) {
        Inertia.delete(route('expenses.destroy', id));
    }
};
</script>
