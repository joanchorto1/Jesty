<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 print:bg-white">
            <FinancePageHeader
                eyebrow="Detalle de ingreso"
                :title="income.name || 'Ingreso'"
                :description="`Consulta el desglose fiscal y la procedencia de este ingreso para auditarlo o compartirlo con otros equipos.`"
                :metrics-columns="3"
            >
                <template #actions>
                    <NavLink
                        :href="route('incomes.edit', income.id)"
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
                    <FinanceSummaryCard label="Base imponible" :value="formatCurrency(income.tax_base)" :helper="formatDate(income.date)" />
                    <FinanceSummaryCard label="IVA" :value="formatCurrency(taxAmount)" :helper="`${income.tax_rate ?? 0}% aplicado`" />
                    <FinanceSummaryCard label="Total" :value="formatCurrency(totalAmount)" :helper="companyName" />
                </template>
            </FinancePageHeader>

            <main class="max-w-4xl mx-auto px-6 -mt-16 pb-16 space-y-10 print:mt-0 print:space-y-6 print:px-0">
                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Información principal</h2>
                        <p class="text-sm text-slate-500">Datos esenciales del ingreso con un formato alineado al resto del módulo contable.</p>
                    </header>
                    <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Origen</dt>
                            <dd class="mt-2">{{ income.source || 'Sin especificar' }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Fecha</dt>
                            <dd class="mt-2">{{ formatDate(income.date) }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Empresa</dt>
                            <dd class="mt-2">{{ companyName }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Identificador interno</dt>
                            <dd class="mt-2">{{ income.id }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Desglose fiscal</h2>
                        <p class="text-sm text-slate-500">Visualiza rápidamente cómo se compone el importe final.</p>
                    </header>
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-emerald-700">
                            <h3 class="text-xs font-semibold uppercase tracking-widest">Base imponible</h3>
                            <p class="mt-3 text-2xl font-semibold">{{ formatCurrency(income.tax_base) }}</p>
                            <p class="mt-2 text-xs text-emerald-800/70">Importe sin impuestos.</p>
                        </div>
                        <div class="rounded-2xl border border-sky-100 bg-sky-50 p-4 text-sky-700">
                            <h3 class="text-xs font-semibold uppercase tracking-widest">IVA</h3>
                            <p class="mt-3 text-2xl font-semibold">{{ formatCurrency(taxAmount) }}</p>
                            <p class="mt-2 text-xs text-sky-800/70">Tipo aplicado: {{ income.tax_rate ?? 0 }}%.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-slate-700">
                            <h3 class="text-xs font-semibold uppercase tracking-widest">Total</h3>
                            <p class="mt-3 text-2xl font-semibold">{{ formatCurrency(totalAmount) }}</p>
                            <p class="mt-2 text-xs text-slate-500">Resultado final registrado.</p>
                        </div>
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
import NavLink from '@/Components/NavLink.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';

const props = defineProps({
    income: {
        type: Object,
        required: true,
    },
});

const income = computed(() => props.income ?? {});

const numeric = (value) => Number(value || 0);

const taxAmount = computed(() => numeric(income.value.tax_base) * numeric(income.value.tax_rate) / 100);
const totalAmount = computed(() => numeric(income.value.tax_base) + taxAmount.value);

const companyName = computed(() => income.value.company?.name || 'Sin empresa asociada');

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
    }).format(numeric(value));
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

const printPage = () => {
    window.print();
};

const confirmDelete = () => {
    if (confirm('¿Seguro que deseas eliminar este ingreso? Esta acción no se puede deshacer.')) {
        Inertia.delete(route('incomes.destroy', income.value.id));
    }
};
</script>
