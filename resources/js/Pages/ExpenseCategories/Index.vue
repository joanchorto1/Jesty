<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 print:bg-white">
            <FinancePageHeader
                eyebrow="Estructura de gasto"
                title="Categorías contables"
                description="Organiza y actualiza la jerarquía de categorías con métricas visuales y un estilo coherente con el dashboard principal."
                :metrics-columns="3"
            >
                <template #actions>
                    <NavLink
                        :href="route('expenseCategories.create')"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        <AddIcon class="h-4 w-4" />
                        <span>Nueva categoría</span>
                    </NavLink>
                    <button
                        type="button"
                        @click="printPage"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        Imprimir resumen
                    </button>
                </template>

                <template #metrics>
                    <FinanceSummaryCard label="Total categorías" :value="categories.length" :helper="`Actualizado: ${new Date().toLocaleDateString('es-ES')}`" />
                    <FinanceSummaryCard label="Con descripción" :value="categoriesWithDescription" :helper="`${descriptionCoverage}% cobertura`" />
                    <FinanceSummaryCard label="Promedio de longitud" :value="`${averageDescriptionLength} car.`" :helper="`Más extensa: ${longestCategoryName}`" />
                </template>
            </FinancePageHeader>

            <main class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10 print:mt-0 print:space-y-6 print:px-0">
                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Cobertura de documentación</h2>
                        <p class="text-sm text-slate-500">Identifica el nivel de detalle de cada categoría para garantizar trazabilidad en auditorías.</p>
                    </header>
                    <div class="mt-6">
                        <DoughnutChart :data="descriptionDistributionData" />
                    </div>
                </section>

                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-0">
                    <header class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Listado de categorías</h2>
                            <p class="text-sm text-slate-500">Formato preparado para lectura ágil y exportación en papel.</p>
                        </div>
                        <div class="text-right text-sm text-slate-500">
                            Total: {{ categories.length }}
                        </div>
                    </header>
                    <div class="mt-6 overflow-x-auto print:overflow-visible">
                        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600">
                            <thead class="bg-slate-50/80 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Identificador</th>
                                    <th scope="col" class="px-4 py-3">Nombre</th>
                                    <th scope="col" class="px-4 py-3">Descripción</th>
                                    <th scope="col" class="px-4 py-3 text-center print:hidden">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr
                                    v-for="category in categories"
                                    :key="category.id"
                                    class="bg-white/60 transition hover:bg-emerald-50/60"
                                >
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ category.id }}</td>
                                    <td class="px-4 py-3">{{ category.name }}</td>
                                    <td class="px-4 py-3 max-w-xl">
                                        <p v-if="category.description" class="whitespace-pre-line">{{ category.description }}</p>
                                        <span v-else class="text-slate-400">Sin descripción</span>
                                    </td>
                                    <td class="px-4 py-3 text-center print:hidden">
                                        <div class="flex justify-center gap-3">
                                            <NavLink :href="route('expenseCategories.show', category.id)" class="text-slate-500 hover:text-slate-700" title="Ver detalle">
                                                <InfoIcon class="w-5 h-5" />
                                            </NavLink>
                                            <NavLink :href="route('expenseCategories.edit', category.id)" class="text-emerald-600 hover:text-emerald-800" title="Editar">
                                                <EditIcon class="w-5 h-5" />
                                            </NavLink>
                                            <button
                                                type="button"
                                                @click="deleteCategory(category.id)"
                                                class="text-rose-500 hover:text-rose-700"
                                                title="Eliminar"
                                            >
                                                <DeleteIcon class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
import InfoIcon from '@/Components/Icons/InfoIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

const categories = computed(() => props.categories ?? []);

const categoriesWithDescription = computed(() => categories.value.filter(category => Boolean(category?.description)).length);

const descriptionCoverage = computed(() => {
    if (!categories.value.length) {
        return 0;
    }
    return Math.round((categoriesWithDescription.value / categories.value.length) * 100);
});

const averageDescriptionLength = computed(() => {
    if (!categoriesWithDescription.value) {
        return 0;
    }
    const totalCharacters = categories.value.reduce((acc, category) => {
        if (!category?.description) {
            return acc;
        }
        return acc + category.description.length;
    }, 0);

    return Math.round(totalCharacters / categoriesWithDescription.value);
});

const longestCategoryName = computed(() => {
    if (!categories.value.length) {
        return 'Sin datos';
    }
    const longest = categories.value.reduce((acc, category) => {
        const length = category?.description?.length ?? 0;
        if (!acc || length > acc.length) {
            return { length, name: category.name };
        }
        return acc;
    }, null);

    return longest ? longest.name : 'Sin datos';
});

const descriptionDistributionData = computed(() => {
    if (!categories.value.length) {
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

    const withDescription = categoriesWithDescription.value;
    const withoutDescription = categories.value.length - withDescription;

    return {
        labels: ['Con descripción', 'Sin descripción'],
        datasets: [
            {
                data: [withDescription, withoutDescription],
                backgroundColor: ['rgba(16, 185, 129, 0.45)', 'rgba(148, 163, 184, 0.45)'],
                borderColor: ['rgba(16, 185, 129, 1)', 'rgba(148, 163, 184, 1)'],
            },
        ],
    };
});

const printPage = () => {
    window.print();
};

const deleteCategory = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
        Inertia.delete(route('expenseCategories.destroy', id));
    }
};
</script>
