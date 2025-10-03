<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 print:bg-white">
            <FinancePageHeader
                eyebrow="Método de pago"
                :title="paymentMethod.name"
                description="Comprueba el uso del método y su descripción para mantener coherentes los registros contables."
                :metrics-columns="3"
            >
                <template #actions>
                    <NavLink
                        :href="route('paymentMethods.edit', paymentMethod.id)"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        Editar
                    </NavLink>
                    <NavLink
                        :href="route('paymentMethods.index')"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        Volver al listado
                    </NavLink>
                </template>
                <template #metrics>
                    <FinanceSummaryCard label="Movimientos" :value="paymentsCount" :helper="paymentsCount === 1 ? 'Gasto asociado' : 'Gastos asociados'" />
                    <FinanceSummaryCard label="Descripción" :value="`${descriptionLength} car.`" :helper="descriptionLength ? 'Texto disponible' : 'Sin descripción'" />
                    <FinanceSummaryCard label="Creación" :value="formatDate(paymentMethod.created_at)" :helper="`Actualizado: ${formatDate(paymentMethod.updated_at)}`" />
                </template>
            </FinancePageHeader>

            <main class="max-w-4xl mx-auto px-6 -mt-16 pb-16 space-y-10 print:mt-0 print:space-y-6 print:px-0">
                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Descripción</h2>
                        <p class="text-sm text-slate-500">Utiliza este texto para orientar a los equipos sobre cuándo aplicar el método.</p>
                    </header>
                    <p class="mt-4 whitespace-pre-line text-slate-700">{{ paymentMethod.description || 'Todavía no se ha añadido ninguna descripción.' }}</p>
                </section>

                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Metadatos</h2>
                        <p class="text-sm text-slate-500">Información adicional para auditorías: empresa, identificador y estado.</p>
                    </header>
                    <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Empresa</dt>
                            <dd class="mt-2">{{ companyName }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Identificador</dt>
                            <dd class="mt-2">{{ paymentMethod.id }}</dd>
                        </div>
                    </dl>
                </section>
            </main>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import FinancePageHeader from '@/Components/Finance/FinancePageHeader.vue';
import FinanceSummaryCard from '@/Components/Finance/FinanceSummaryCard.vue';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
    paymentMethod: {
        type: Object,
        required: true,
    },
});

const paymentMethod = computed(() => props.paymentMethod ?? {});
const paymentsCount = computed(() => paymentMethod.value.expenses_count ?? 0);
const descriptionLength = computed(() => paymentMethod.value.description?.length ?? 0);
const companyName = computed(() => paymentMethod.value.company?.name || 'Sin empresa asociada');

const formatDate = (value) => {
    if (!value) {
        return '—';
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
</script>
