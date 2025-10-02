<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 print:bg-white">
            <FinancePageHeader
                eyebrow="Detalle de gasto"
                :title="expense.name || 'Gasto'"
                :description="`Consulta la trazabilidad completa del gasto y su impacto en los indicadores del panel contable.`"
                :metrics-columns="3"
            >
                <template #actions>
                    <NavLink
                        :href="route('expenses.edit', expense.id)"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        <EditIcon class="h-4 w-4" />
                        <span>Editar</span>
                    </NavLink>
                    <button
                        type="button"
                        @click="confirmDelete"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        Eliminar
                    </button>
                    <button
                        type="button"
                        @click="printPage"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        Imprimir ficha
                    </button>
                </template>

                <template #metrics>
                    <FinanceSummaryCard label="Base imponible" :value="formatCurrency(netAmount)" :helper="formatDate(expense.date)" />
                    <FinanceSummaryCard label="IVA" :value="formatCurrency(taxAmount)" :helper="`${expense.iva ?? 0}% aplicado`" />
                    <FinanceSummaryCard label="Total" :value="formatCurrency(grossAmount)" :helper="`${paymentMethodName} · ${categoryName}`" />
                </template>
            </FinancePageHeader>

            <main class="max-w-4xl mx-auto px-6 -mt-16 pb-16 space-y-10 print:mt-0 print:space-y-6 print:px-0">
                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Información principal</h2>
                        <p class="text-sm text-slate-500">Datos esenciales del gasto armonizados con la tipografía y estilo del dashboard.</p>
                    </header>
                    <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Descripción</dt>
                            <dd class="mt-2 whitespace-pre-line">{{ expense.description || 'Sin descripción' }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Fecha</dt>
                            <dd class="mt-2">{{ formatDate(expense.date) }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Método de pago</dt>
                            <dd class="mt-2">{{ paymentMethodName }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Categoría</dt>
                            <dd class="mt-2">{{ categoryName }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Desglose económico</h2>
                        <p class="text-sm text-slate-500">Visualiza la proporción entre base imponible e impuestos.</p>
                    </header>
                    <div class="mt-6">
                        <DoughnutChart :data="breakdownChart" />
                    </div>
                </section>

                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Documentación</h2>
                        <p class="text-sm text-slate-500">Archivos listos para auditoría o consulta por terceros.</p>
                    </header>
                    <div class="mt-6 text-sm text-slate-600">
                        <template v-if="expense.file">
                            <a :href="`/storage/${expense.file}`" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2 font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                Ver archivo adjunto
                            </a>
                        </template>
                        <p v-else class="text-slate-400">No hay archivo adjunto.</p>
                    </div>
                </section>
            </main>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import FinancePageHeader from '@/Components/Finance/FinancePageHeader.vue';
import FinanceSummaryCard from '@/Components/Finance/FinanceSummaryCard.vue';
import DoughnutChart from '@/Components/DoughnutChart.vue';
import NavLink from '@/Components/NavLink.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';

const props = defineProps({
    expense: {
        type: Object,
        required: true,
    },
    paymentMethods: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const expense = computed(() => props.expense ?? {});
const paymentMethods = computed(() => props.paymentMethods ?? []);
const categories = computed(() => props.categories ?? []);

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

const netAmount = computed(() => Number(expense.value.amount ?? 0));
const taxAmount = computed(() => netAmount.value * Number(expense.value.iva ?? 0) / 100);
const grossAmount = computed(() => netAmount.value + taxAmount.value);

const paymentMethodName = computed(() => {
    const method = paymentMethods.value.find(item => item.id === expense.value.payment_method_id);
    return method?.name || 'Sin método';
});

const categoryName = computed(() => {
    const category = categories.value.find(item => item.id === expense.value.expense_category_id);
    return category?.name || 'Sin categoría';
});

const breakdownChart = computed(() => ({
    labels: ['Base imponible', 'IVA'],
    datasets: [
        {
            data: [netAmount.value, taxAmount.value],
            backgroundColor: ['rgba(16, 185, 129, 0.45)', 'rgba(244, 63, 94, 0.45)'],
            borderColor: ['rgba(16, 185, 129, 1)', 'rgba(244, 63, 94, 1)'],
        },
    ],
}));

const printPage = () => {
    window.print();
};

const confirmDelete = () => {
    if (confirm('¿Estás seguro de que quieres eliminar este gasto?')) {
        Inertia.delete(route('expenses.destroy', expense.value.id));
    }
};
</script>
