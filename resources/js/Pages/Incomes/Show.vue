<template>
    <AppLayout>
        <div class="container mx-auto max-w-4xl bg-gray-50 min-h-screen p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Detalle del ingreso</h1>
                    <p class="text-sm text-gray-500">Revisa la información registrada de este movimiento.</p>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('incomes.edit', income.id)" class="rounded-full bg-blue-100 p-2 text-blue-700 hover:bg-blue-200" title="Editar ingreso">
                        <EditIcon class="h-5 w-5" />
                    </Link>
                    <button @click="destroy" class="rounded-full bg-red-100 p-2 text-red-600 hover:bg-red-200" title="Eliminar ingreso">
                        <DeleteIcon class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">Información general</h2>
                        <dl class="space-y-3 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500">Nombre</dt>
                                <dd class="text-gray-800">{{ income.name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500">Origen</dt>
                                <dd class="text-gray-800">{{ income.source }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500">Fecha</dt>
                                <dd class="text-gray-800">{{ income.date }}</dd>
                            </div>
                            <div class="flex justify-between" v-if="income.company">
                                <dt class="font-medium text-gray-500">Empresa</dt>
                                <dd class="text-gray-800">{{ income.company.name }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">Resumen económico</h2>
                        <dl class="space-y-3 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500">Base imponible</dt>
                                <dd class="text-gray-800">{{ Number(income.tax_base).toFixed(2) }} €</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500">IVA aplicado</dt>
                                <dd class="text-gray-800">{{ income.tax_rate }} %</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500">Monto IVA</dt>
                                <dd class="text-gray-800">{{ taxAmount.toFixed(2) }} €</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="font-medium text-gray-500">Total con impuestos</dt>
                                <dd class="text-gray-800 font-semibold">{{ totalAmount.toFixed(2) }} €</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="flex justify-start">
                    <Link :href="route('incomes.index')" class="text-sm font-medium text-blue-700 hover:underline">Volver al listado</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';

const props = defineProps({
    income: Object,
});

const income = props.income;

const taxAmount = computed(() => {
    const base = Number(income.tax_base) || 0;
    const rate = Number(income.tax_rate) || 0;
    return base * rate / 100;
});

const totalAmount = computed(() => Number(income.tax_base || 0) + taxAmount.value);

const destroy = () => {
    if (confirm('¿Quieres eliminar este ingreso?')) {
        Inertia.delete(route('incomes.destroy', income.id));
    }
};
</script>
