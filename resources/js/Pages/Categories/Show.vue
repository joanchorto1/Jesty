<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 print:bg-white">
            <FinancePageHeader
                eyebrow="Categoría"
                :title="category.name"
                description="Consulta la información clave de la categoría y su uso dentro del catálogo contable."
                :metrics-columns="3"
            >
                <template #actions>
                    <NavLink
                        :href="route('categories.edit', category.id)"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        Editar
                    </NavLink>
                    <NavLink
                        :href="route('categories.index')"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        Volver al listado
                    </NavLink>
                </template>
                <template #metrics>
                    <FinanceSummaryCard label="Productos asociados" :value="productsCount" :helper="productsCount === 1 ? 'Producto' : 'Productos'" />
                    <FinanceSummaryCard label="Descripción" :value="`${descriptionLength} car.`" :helper="descriptionLength ? 'Contenido disponible' : 'Sin descripción'" />
                    <FinanceSummaryCard label="Creación" :value="formatDate(category.created_at)" :helper="`Actualizada: ${formatDate(category.updated_at)}`" />
                </template>
            </FinancePageHeader>

            <main class="max-w-4xl mx-auto px-6 -mt-16 pb-16 space-y-10 print:mt-0 print:space-y-6 print:px-0">
                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Descripción</h2>
                        <p class="text-sm text-slate-500">Texto utilizado en informes y paneles para explicar el alcance de la categoría.</p>
                    </header>
                    <p class="mt-4 whitespace-pre-line text-slate-700">{{ category.description || 'Esta categoría todavía no tiene descripción.' }}</p>
                </section>

                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Metadatos</h2>
                        <p class="text-sm text-slate-500">Información adicional sobre la empresa asociada y los identificadores internos.</p>
                    </header>
                    <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Empresa</dt>
                            <dd class="mt-2">{{ companyName }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Identificador</dt>
                            <dd class="mt-2">{{ category.id }}</dd>
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
    category: {
        type: Object,
        required: true,
    },
});

const category = computed(() => props.category ?? {});

const descriptionLength = computed(() => category.value.description?.length ?? 0);
const productsCount = computed(() => category.value.products_count ?? 0);
const companyName = computed(() => category.value.company?.name || 'Sin empresa asociada');

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
