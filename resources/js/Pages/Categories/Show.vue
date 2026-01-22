<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-100/80 py-12 print:bg-white">
            <div class="mx-auto flex max-w-4xl flex-col gap-10 px-6 print:px-0">
                <CrudPageHeader
                    :title="category.name"
                    description="Consulta la información clave de la categoría y su uso dentro del catálogo contable."
                >
                    <template #actions>
                        <NavLink
                            :href="route('categories.edit', category.id)"
                            class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                        >
                            Editar
                        </NavLink>
                        <NavLink
                            :href="route('categories.index')"
                            class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"
                        >
                            Volver al listado
                        </NavLink>
                    </template>
                </CrudPageHeader>

                <div class="grid gap-6 md:grid-cols-3">
                    <CrudStatCard label="Productos asociados" :value="productsCount" icon-background="bg-sky-500/10 text-sky-600">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ productsCount === 1 ? 'Producto' : 'Productos' }}</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Descripción" :value="`${descriptionLength} car.`" icon-background="bg-indigo-500/10 text-indigo-600">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ descriptionLength ? 'Contenido disponible' : 'Sin descripción' }}</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Creación" :value="formatDate(category.created_at)" icon-background="bg-emerald-500/10 text-emerald-600">
                        <template #description>
                            <p class="text-xs text-slate-500">Actualizada: {{ formatDate(category.updated_at) }}</p>
                        </template>
                    </CrudStatCard>
                </div>

                <div class="space-y-10 print:space-y-6">
                    <Panel title="Descripción" description="Texto utilizado en informes y paneles para explicar el alcance de la categoría." :header-border="true">
                        <p class="whitespace-pre-line text-slate-700">{{ category.description || 'Esta categoría todavía no tiene descripción.' }}</p>
                    </Panel>

                    <Panel title="Metadatos" description="Información adicional sobre la empresa asociada y los identificadores internos." :header-border="true">
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                                <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Empresa</dt>
                                <dd class="mt-2">{{ companyName }}</dd>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 text-slate-700">
                                <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Identificador</dt>
                                <dd class="mt-2">{{ category.id }}</dd>
                            </div>
                        </dl>
                    </Panel>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import Panel from '@/Components/UI/Panel.vue';
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
