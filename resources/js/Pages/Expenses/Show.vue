<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-100/80 py-12 print:bg-white">
            <div class="mx-auto flex max-w-4xl flex-col gap-10 px-6 print:px-0">
                <CrudPageHeader
                    :title="expense.name || 'Gasto'"
                    :description="`Consulta la trazabilidad completa del gasto y su impacto en los indicadores del panel contable.`"
                >
                    <template #actions>
                        <NavLink
                            :href="route('expenses.edit', expense.id)"
                            class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                        >
                            <EditIcon class="h-4 w-4" />
                            <span>Editar</span>
                        </NavLink>
                        <button
                            type="button"
                            @click="confirmDelete"
                            class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"
                        >
                            Eliminar
                        </button>
                        <button
                            type="button"
                            @click="printPage"
                            class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"
                        >
                            Imprimir ficha
                        </button>
                    </template>
                </CrudPageHeader>

                <div class="grid gap-6 md:grid-cols-3">
                    <CrudStatCard label="Base imponible" :value="formatCurrency(netAmount)" icon-background="bg-sky-500/10 text-sky-600">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ formatDate(expense.date) }}</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="IVA" :value="formatCurrency(taxAmount)" icon-background="bg-amber-500/10 text-amber-600">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ `${expense.iva ?? 0}% aplicado` }}</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Total" :value="formatCurrency(grossAmount)" icon-background="bg-emerald-500/10 text-emerald-600">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ `${paymentMethodName} · ${categoryName}` }}</p>
                        </template>
                    </CrudStatCard>
                </div>

                <div class="space-y-10 print:space-y-6">
                    <Panel title="Información principal" description="Datos esenciales del gasto armonizados con la tipografía y estilo del dashboard.">
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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
                    </Panel>

                    <Panel title="Desglose económico" description="Visualiza la proporción entre base imponible e impuestos.">
                        <DoughnutChart :data="breakdownChart" />
                    </Panel>

                    <Panel title="Documentación" description="Archivos listos para auditoría o consulta por terceros.">
                        <div class="mt-6 text-sm text-slate-600">
                            <template v-if="expense.file">
                                <a :href="`/storage/${expense.file}`" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2 font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                    Ver archivo adjunto
                                </a>
                            </template>
                            <p v-else class="text-slate-400">No hay archivo adjunto.</p>
                        </div>
                    </Panel>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import Panel from '@/Components/UI/Panel.vue';
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
