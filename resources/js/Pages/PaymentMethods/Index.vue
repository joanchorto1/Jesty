<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 print:bg-white">
            <FinancePageHeader
                eyebrow="Estructura de cobro"
                title="Métodos de pago"
                description="Gestiona los canales de cobro con tarjetas coherentes, gráficas y formato imprimible inspirado en el dashboard contable."
                :metrics-columns="3"
            >
                <template #actions>
                    <NavLink
                        :href="route('paymentMethods.create')"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    >
                        <AddIcon class="h-4 w-4" />
                        <span>Nuevo método</span>
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
                    <FinanceSummaryCard label="Total métodos" :value="paymentMethods.length" :helper="`Actualizado: ${new Date().toLocaleDateString('es-ES')}`" />
                    <FinanceSummaryCard label="Con descripción" :value="methodsWithDescription" :helper="`${descriptionCoverage}% cobertura`" />
                    <FinanceSummaryCard label="Nombre medio" :value="`${averageNameLength} car.`" :helper="`Más extenso: ${longestMethodName}`" />
                </template>
            </FinancePageHeader>

            <main class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10 print:mt-0 print:space-y-6 print:px-0">
                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-4">
                    <header class="border-b border-slate-100 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Cobertura descriptiva</h2>
                        <p class="text-sm text-slate-500">Comprueba qué métodos disponen de documentación útil para equipos comerciales y financieros.</p>
                    </header>
                    <div class="mt-6">
                        <DoughnutChart :data="descriptionDistributionData" />
                    </div>
                </section>

                <section class="bg-white rounded-3xl shadow-xl p-6 print:shadow-none print:rounded-none print:border print:border-slate-200 print:p-0">
                    <header class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Listado de métodos</h2>
                            <p class="text-sm text-slate-500">Diseño preparado para grandes volúmenes y exportación en papel.</p>
                        </div>
                        <div class="text-right text-sm text-slate-500">
                            Total: {{ paymentMethods.length }}
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
                                    v-for="method in paymentMethods"
                                    :key="method.id"
                                    class="bg-white/60 transition hover:bg-emerald-50/60"
                                >
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ method.id }}</td>
                                    <td class="px-4 py-3">{{ method.name }}</td>
                                    <td class="px-4 py-3 max-w-xl">
                                        <p v-if="method.description" class="whitespace-pre-line">{{ method.description }}</p>
                                        <span v-else class="text-slate-400">Sin descripción</span>
                                    </td>
                                    <td class="px-4 py-3 text-center print:hidden">
                                        <div class="flex justify-center gap-3">
                                            <NavLink :href="route('paymentMethods.show', method.id)" class="text-slate-500 hover:text-slate-700" title="Ver detalle">
                                                <InfoIcon class="w-5 h-5" />
                                            </NavLink>
                                            <NavLink :href="route('paymentMethods.edit', method.id)" class="text-emerald-600 hover:text-emerald-800" title="Editar">
                                                <EditIcon class="w-5 h-5" />
                                            </NavLink>
                                            <button
                                                type="button"
                                                @click="deleteMethod(method.id)"
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
    paymentMethods: {
        type: Array,
        default: () => [],
    },
});

const paymentMethods = computed(() => props.paymentMethods ?? []);

const methodsWithDescription = computed(() => paymentMethods.value.filter(method => Boolean(method?.description)).length);

const descriptionCoverage = computed(() => {
    if (!paymentMethods.value.length) {
        return 0;
    }
    return Math.round((methodsWithDescription.value / paymentMethods.value.length) * 100);
});

const averageNameLength = computed(() => {
    if (!paymentMethods.value.length) {
        return 0;
    }
    const totalCharacters = paymentMethods.value.reduce((acc, method) => acc + (method?.name?.length ?? 0), 0);
    return Math.round(totalCharacters / paymentMethods.value.length);
});

const longestMethodName = computed(() => {
    if (!paymentMethods.value.length) {
        return 'Sin datos';
    }
    const longest = paymentMethods.value.reduce((acc, method) => {
        const length = method?.name?.length ?? 0;
        if (!acc || length > acc.length) {
            return { length, name: method.name };
        }
        return acc;
    }, null);

    return longest ? longest.name : 'Sin datos';
});

const descriptionDistributionData = computed(() => {
    if (!paymentMethods.value.length) {
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

    const withDescription = methodsWithDescription.value;
    const withoutDescription = paymentMethods.value.length - withDescription;

    return {
        labels: ['Con descripción', 'Sin descripción'],
        datasets: [
            {
                data: [withDescription, withoutDescription],
                backgroundColor: ['rgba(59, 130, 246, 0.45)', 'rgba(148, 163, 184, 0.45)'],
                borderColor: ['rgba(59, 130, 246, 1)', 'rgba(148, 163, 184, 1)'],
            },
        ],
    };
});

const printPage = () => {
    window.print();
};

const deleteMethod = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este método de pago?')) {
        Inertia.delete(route('paymentMethods.destroy', id));
    }
};
</script>
