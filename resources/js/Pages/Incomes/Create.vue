<template>
    <AppLayout>
        <div class="container mx-auto max-w-4xl bg-gray-50 min-h-screen p-6">
            <h1 class="text-3xl font-semibold text-gray-800 mb-6">Registrar nuevo ingreso</h1>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-6 rounded-lg shadow">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Identificador del ingreso"
                        />
                        <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label for="source" class="block text-sm font-medium text-gray-700">Origen</label>
                        <input
                            id="source"
                            v-model="form.source"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Cliente o canal de venta"
                        />
                        <p v-if="form.errors.source" class="mt-2 text-sm text-red-600">{{ form.errors.source }}</p>
                    </div>

                    <div>
                        <label for="tax_base" class="block text-sm font-medium text-gray-700">Base imponible</label>
                        <input
                            id="tax_base"
                            v-model.number="form.tax_base"
                            type="number"
                            step="0.01"
                            min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="0.00"
                        />
                        <p v-if="form.errors.tax_base" class="mt-2 text-sm text-red-600">{{ form.errors.tax_base }}</p>
                    </div>

                    <div>
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700">IVA (%)</label>
                        <input
                            id="tax_rate"
                            v-model.number="form.tax_rate"
                            type="number"
                            step="0.01"
                            min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="21"
                        />
                        <p v-if="form.errors.tax_rate" class="mt-2 text-sm text-red-600">{{ form.errors.tax_rate }}</p>
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Fecha</label>
                        <input
                            id="date"
                            v-model="form.date"
                            type="date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <p v-if="form.errors.date" class="mt-2 text-sm text-red-600">{{ form.errors.date }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow">
                        <p class="text-sm text-gray-500">Monto de IVA</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ taxAmount.toFixed(2) }} €</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow">
                        <p class="text-sm text-gray-500">Total con impuestos</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ totalAmount.toFixed(2) }} €</p>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <Link :href="route('incomes.index')" class="text-sm font-medium text-gray-600 hover:text-gray-800">
                        Cancelar y volver
                    </Link>
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-full bg-blue-900 px-4 py-2 text-white shadow hover:bg-blue-700 focus:outline-none"
                    >
                        <SaveIcon class="mr-2 h-5 w-5 stroke-gray-100" />
                        Guardar ingreso
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from '@/Components/Icons/SaveIcon.vue';

const form = useForm({
    name: '',
    source: '',
    tax_base: 0,
    tax_rate: 21,
    date: new Date().toISOString().split('T')[0],
});

const taxAmount = computed(() => {
    const base = Number(form.tax_base) || 0;
    const rate = Number(form.tax_rate) || 0;
    return base * rate / 100;
});

const totalAmount = computed(() => {
    const base = Number(form.tax_base) || 0;
    return base + taxAmount.value;
});

const submit = () => {
    form.post(route('incomes.store'));
};
</script>
