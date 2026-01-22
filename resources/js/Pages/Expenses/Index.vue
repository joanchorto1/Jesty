<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-100/80 py-12 print:bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6 print:px-0">
                <CrudPageHeader
                    title="Gastos operativos"
                    description="Visualiza los gastos con filtros dinámicos, gráficas comparables y tarjetas en línea con el panel contable."
                    :icon="MenuExpenseIcon"
                >
                    <template #actions>
                        <div class="flex flex-wrap gap-3">
                            <NavLink
                                :href="route('expenses.create')"
                                class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                            >
                                <AddIcon class="h-4 w-4" />
                                <span>Registrar gasto</span>
                            </NavLink>
                            <button
                                type="button"
                                @click="printPage"
                                class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"
                            >
                                Imprimir informe
                            </button>
                        </div>
                    </template>
                </CrudPageHeader>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <CrudStatCard label="Gasto total" :value="formatCurrency(totalGrossAmount)" :icon="MenuExpenseIcon">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ totalGrossHelper }}</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Base imponible" :value="formatCurrency(totalNetAmount)" :icon="MenuExpenseIcon" icon-background="bg-indigo-500/10 text-indigo-600">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ totalNetHelper }}</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Ticket medio" :value="formatCurrency(averageGrossAmount)" :icon="MenuExpenseIcon" icon-background="bg-emerald-500/10 text-emerald-600">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ averageHelper }}</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Mayor gasto" :value="formatCurrency(highestExpenseGross)" :icon="MenuExpenseIcon" icon-background="bg-amber-500/10 text-amber-600">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ highestExpenseHelper }}</p>
                        </template>
                    </CrudStatCard>
                </div>

                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Filtrar gastos</h2>
                        <p class="text-sm text-slate-500">Combina filtros para focalizar tus análisis. Las gráficas y totales se recalculan al instante.</p>
                    </div>
                    <CrudFilterBar>
                        <div class="grid flex-1 grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
                        <label class="flex flex-col text-sm font-medium text-slate-600">
                            Periodo
                            <select
                                v-model="periodMode"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            >
                                <option value="general">Todos los periodos</option>
                                <option value="annual" :disabled="!availableYears.length">Anual</option>
                                <option value="monthly" :disabled="!availableYears.length">Mensual</option>
                            </select>
                        </label>

                        <label v-if="periodMode !== 'general'" class="flex flex-col text-sm font-medium text-slate-600">
                            Año
                            <select
                                v-model="selectedYear"
                                :disabled="!availableYears.length"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            >
                                <option v-if="!availableYears.length" value="">Sin datos</option>
                                <option v-for="year in availableYears" :key="year" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                        </label>

                        <label v-if="periodMode === 'monthly'" class="flex flex-col text-sm font-medium text-slate-600">
                            Mes
                            <select
                                v-model="selectedMonth"
                                :disabled="!availableMonths.length"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            >
                                <option v-if="!availableMonths.length" value="">Sin datos</option>
                                <option v-for="month in availableMonths" :key="month.value" :value="month.value">
                                    {{ month.label }}
                                </option>
                            </select>
                        </label>

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

                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-3 text-sm font-medium text-slate-600 shadow-sm">
                            <input
                                type="checkbox"
                                v-model="showRecurringOnly"
                                class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                            />
                            Solo recurrentes
                        </label>
                    </div>
                        <template #actions>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                                @click="resetFilters"
                            >
                                Limpiar filtros
                            </button>
                        </template>
                    </CrudFilterBar>
                </div>

                <section class="grid grid-cols-1 gap-8 xl:grid-cols-2">
                    <article class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                        <header class="flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-800">Gasto mensual</h2>
                                <p class="text-sm text-slate-500">Controla la evolución temporal de los desembolsos seleccionados · {{ periodContextLabel }}.</p>
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
                            <p class="text-sm text-slate-500">Comprende qué líneas de gasto concentran más recursos · {{ periodContextLabel }}.</p>
                        </header>
                        <div class="mt-6">
                            <DoughnutChart :data="categoryDistributionData" />
                        </div>
                    </article>
                </section>

                <div class="space-y-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-800">Relación detallada de gastos</h2>
                            <p class="text-sm text-slate-500">Información preparada para manejar grandes volúmenes y exportar en papel.</p>
                        </div>
                        <div class="text-right text-sm text-slate-500">
                            {{ filteredCountMessage }}
                        </div>
                    </div>
                    <CrudTable>
                        <template #head>
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Identificador</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Nombre</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Descripción</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-widest text-slate-500">Base imponible</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-widest text-slate-500">IVA</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-widest text-slate-500">Total</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Fecha</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Método</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Categoría</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-widest text-slate-500 print:hidden">Acciones</th>
                            </tr>
                        </template>
                        <tr
                            v-for="expense in visibleExpenses"
                            :key="expense.id"
                            class="bg-white/60 transition hover:bg-rose-50/60"
                        >
                            <td class="px-4 py-3 font-medium text-slate-700">{{ expense.id }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <span>{{ expense.name }}</span>
                                        <span
                                            v-if="expense.is_recurring"
                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700"
                                        >
                                            Recurrente
                                        </span>
                                    </div>
                                    <p
                                        v-if="expense.is_recurring && expense.next_run_at"
                                        class="text-xs text-slate-500"
                                    >
                                        Próxima ejecución: {{ formatDate(expense.next_run_at) }} · {{ recurringStatusLabel(expense.recurring_status) }}
                                    </p>
                                </div>
                            </td>
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
                        <template #footer>
                            <tfoot class="bg-slate-50/80 text-xs uppercase tracking-widest text-slate-500">
                                <tr>
                                    <td class="px-4 py-3" colspan="3">Totales</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalNetAmount) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalTaxAmount) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrency(totalGrossAmount) }}</td>
                                    <td class="px-4 py-3" colspan="4"></td>
                                </tr>
                            </tfoot>
                        </template>
                    </CrudTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudFilterBar from '@/Components/Crud/CrudFilterBar.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import CrudTable from '@/Components/Crud/CrudTable.vue';
import BarChart from '@/Components/BarChart.vue';
import DoughnutChart from '@/Components/DoughnutChart.vue';
import NavLink from '@/Components/NavLink.vue';
import InfoIcon from '@/Components/Icons/InfoIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import MenuExpenseIcon from '@/Components/Icons/MenuExpenseIcon.vue';

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
const showRecurringOnly = ref(false);
const periodMode = ref('general');
const selectedYear = ref('');
const selectedMonth = ref('');

const availableYears = computed(() => {
    const years = new Set();
    expenses.value.forEach(expense => {
        const date = new Date(expense.date);
        if (!Number.isNaN(date.getTime())) {
            years.add(date.getFullYear());
        }
    });
    return Array.from(years).sort((a, b) => b - a).map(year => String(year));
});

const monthOptions = [
    { value: '01', label: 'Enero' },
    { value: '02', label: 'Febrero' },
    { value: '03', label: 'Marzo' },
    { value: '04', label: 'Abril' },
    { value: '05', label: 'Mayo' },
    { value: '06', label: 'Junio' },
    { value: '07', label: 'Julio' },
    { value: '08', label: 'Agosto' },
    { value: '09', label: 'Septiembre' },
    { value: '10', label: 'Octubre' },
    { value: '11', label: 'Noviembre' },
    { value: '12', label: 'Diciembre' },
];

const availableMonths = computed(() => {
    if (!selectedYear.value) {
        return [];
    }

    const months = new Set();
    expenses.value.forEach(expense => {
        const date = new Date(expense.date);
        if (!Number.isNaN(date.getTime()) && String(date.getFullYear()) === selectedYear.value) {
            months.add((date.getMonth() + 1).toString().padStart(2, '0'));
        }
    });

    if (!months.size) {
        return [];
    }

    const orderedMonths = Array.from(months).sort((a, b) => Number(a) - Number(b));
    return monthOptions.filter(option => orderedMonths.includes(option.value));
});

const ensureSelectedYear = () => {
    if (!availableYears.value.length) {
        selectedYear.value = '';
        return;
    }

    if (!availableYears.value.includes(selectedYear.value)) {
        selectedYear.value = availableYears.value[0];
    }
};

const ensureSelectedMonth = () => {
    if (periodMode.value !== 'monthly') {
        return;
    }

    const months = availableMonths.value;
    if (!months.length) {
        selectedMonth.value = '';
        return;
    }

    if (!months.find(month => month.value === selectedMonth.value)) {
        selectedMonth.value = months[0].value;
    }
};

watch([periodMode, availableYears], ([mode]) => {
    if (mode === 'general') {
        selectedYear.value = '';
        selectedMonth.value = '';
        return;
    }

    ensureSelectedYear();

    if (mode === 'annual') {
        selectedMonth.value = '';
    }

    if (mode === 'monthly') {
        ensureSelectedMonth();
    }
});

watch(selectedYear, () => {
    ensureSelectedMonth();
});

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

const matchesPeriod = (expense) => {
    if (periodMode.value === 'general') {
        return true;
    }

    const date = new Date(expense.date);
    if (Number.isNaN(date.getTime())) {
        return false;
    }

    const year = String(date.getFullYear());

    if (periodMode.value === 'annual') {
        return !selectedYear.value || year === selectedYear.value;
    }

    if (periodMode.value === 'monthly') {
        if (selectedYear.value && year !== selectedYear.value) {
            return false;
        }

        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        return !selectedMonth.value || month === selectedMonth.value;
    }

    return true;
};

const periodFilteredExpenses = computed(() => {
    return expenses.value.filter(expense => matchesPeriod(expense));
});

const visibleExpenses = computed(() => {
    return periodFilteredExpenses.value.filter(expense => {
        const matchesPaymentMethod = selectedPaymentMethod.value
            ? String(expense.payment_method_id) === selectedPaymentMethod.value
            : true;

        const matchesCategory = selectedCategory.value
            ? String(expense.expense_category_id) === selectedCategory.value
            : true;

        const matchesRecurring = showRecurringOnly.value ? Boolean(expense.is_recurring) : true;

        return matchesPaymentMethod && matchesCategory && matchesDate(expense) && matchesRecurring;
    });
});

const periodContextLabel = computed(() => {
    if (periodMode.value === 'annual' && selectedYear.value) {
        return `Año ${selectedYear.value}`;
    }

    if (periodMode.value === 'monthly' && selectedYear.value && selectedMonth.value) {
        const monthName = monthOptions.find(month => month.value === selectedMonth.value)?.label ?? 'Mes';
        return `${monthName} ${selectedYear.value}`;
    }

    if (periodMode.value === 'monthly' && selectedYear.value) {
        return `Año ${selectedYear.value}`;
    }

    return 'Todos los periodos';
});

const filteredCountMessage = computed(() => {
    if (periodMode.value === 'general') {
        if (visibleExpenses.value.length === expenses.value.length) {
            return `${expenses.value.length} registros · ${periodContextLabel.value}`;
        }

        return `${visibleExpenses.value.length} de ${expenses.value.length} registros · ${periodContextLabel.value}`;
    }

    const periodCount = periodFilteredExpenses.value.length;

    if (!periodCount) {
        return `Sin registros en ${periodContextLabel.value.toLowerCase()}`;
    }

    if (visibleExpenses.value.length === periodCount) {
        return `${visibleExpenses.value.length} registros · ${periodContextLabel.value}`;
    }

    return `${visibleExpenses.value.length} de ${periodCount} registros · ${periodContextLabel.value}`;
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

const totalGrossHelper = computed(() => {
    return `${visibleExpenses.value.length} registros visibles · ${periodContextLabel.value}`;
});

const totalNetHelper = computed(() => {
    return `IVA acumulado ${formatCurrency(totalTaxAmount.value)} · ${periodContextLabel.value}`;
});

const averageHelper = computed(() => {
    if (!visibleExpenses.value.length) {
        return `Sin registros · ${periodContextLabel.value}`;
    }

    return `Rango ${formatCurrency(lowestExpenseGross.value)} – ${formatCurrency(highestExpenseGross.value)} · ${periodContextLabel.value}`;
});

const highestExpenseHelper = computed(() => {
    if (!visibleExpenses.value.length) {
        return `Sin registros · ${periodContextLabel.value}`;
    }

    return `${highestExpenseDescriptor.value} · ${periodContextLabel.value}`;
});

const recurringStatusLabel = (status) => {
    switch (status) {
        case 'paused':
            return 'en pausa';
        case 'inactive':
            return 'inactiva';
        case 'active':
        default:
            return 'activa';
    }
};

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
                label: `Total mensual · ${periodContextLabel.value}`,
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
    showRecurringOnly.value = false;
    periodMode.value = 'general';
    selectedYear.value = '';
    selectedMonth.value = '';
};

const printPage = () => {
    // La vista impresa reutiliza los filtros activos, por lo que no es necesario propagar el modo de periodo.
    window.print();
};

const deleteExpense = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este gasto?')) {
        Inertia.delete(route('expenses.destroy', id));
    }
};
</script>
