<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen">
            <div class="mx-auto max-w-5xl">
                <h1 class="text-3xl font-semibold mb-6 text-gray-800">Actualizar gasto</h1>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-6 rounded-lg shadow">
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

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre del gasto</label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Categoría</label>
                            <select
                                id="category_id"
                                v-model="form.expense_category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.expense_category_id" class="mt-2 text-sm text-red-600">{{ form.errors.expense_category_id }}</p>
                        </div>

                        <div>
                            <label for="payment_method_id" class="block text-sm font-medium text-gray-700">Método de pago</label>
                            <select
                                id="payment_method_id"
                                v-model="form.payment_method_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option v-for="paymentMethod in paymentMethods" :key="paymentMethod.id" :value="paymentMethod.id">
                                    {{ paymentMethod.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.payment_method_id" class="mt-2 text-sm text-red-600">{{ form.errors.payment_method_id }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Importe sin IVA</label>
                            <input
                                id="amount"
                                v-model.number="form.amount"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.amount" class="mt-2 text-sm text-red-600">{{ form.errors.amount }}</p>
                        </div>

                        <div>
                            <label for="iva" class="block text-sm font-medium text-gray-700">IVA (%)</label>
                            <input
                                id="iva"
                                v-model.number="form.iva"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.iva" class="mt-2 text-sm text-red-600">{{ form.errors.iva }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="file" class="block text-sm font-medium text-gray-700">Archivo adjunto</label>
                            <div class="flex items-center gap-4">
                                <input
                                    id="file"
                                    type="file"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @change="handleFileChange"
                                />
                                <template v-if="currentFileUrl">
                                    <a :href="currentFileUrl" target="_blank" class="text-sm text-blue-700 hover:underline">Ver archivo actual</a>
                                </template>
                            </div>
                            <p v-if="form.errors.file" class="mt-2 text-sm text-red-600">{{ form.errors.file }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow">
                            <p class="text-sm text-gray-500">Importe base</p>
                            <p class="text-xl font-semibold text-gray-800">{{ Number(form.amount || 0).toFixed(2) }} €</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow">
                            <p class="text-sm text-gray-500">IVA estimado</p>
                            <p class="text-xl font-semibold text-gray-800">{{ taxAmount.toFixed(2) }} €</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow">
                            <p class="text-sm text-gray-500">Total previsto</p>
                            <p class="text-xl font-semibold text-gray-800">{{ totalAmount.toFixed(2) }} €</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <Link :href="route('expenses.show', expense.id)" class="text-sm font-medium text-gray-600 hover:text-gray-800">Cancelar y volver</Link>
                        <button type="submit" class="inline-flex items-center rounded-full bg-blue-900 px-4 py-2 text-white shadow hover:bg-blue-700">
                            <SaveIcon class="mr-2 h-5 w-5 stroke-gray-100" />
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from '@/Components/Icons/SaveIcon.vue';

const props = defineProps({
    categories: Array,
    paymentMethods: Array,
    expense: Object,
});

const form = useForm({
    name: props.expense?.name ?? '',
    description: props.expense?.description ?? '',
    amount: props.expense?.amount ?? 0,
    iva: props.expense?.iva ?? 0,
    date: props.expense?.date ?? new Date().toISOString().split('T')[0],
    payment_method_id: props.expense?.payment_method_id ?? '',
    expense_category_id: props.expense?.expense_category_id ?? '',
    file: null,
});

const currentFileUrl = computed(() => {
    if (!props.expense?.file) {
        return null;
    }
    return `/storage/${props.expense.file}`;
});

const taxAmount = computed(() => {
    const amount = Number(form.amount) || 0;
    const iva = Number(form.iva) || 0;
    return amount * iva / 100;
});

const totalAmount = computed(() => (Number(form.amount) || 0) + taxAmount.value);

const handleFileChange = (event) => {
    form.file = event.target.files[0];
};

const submitForm = () => {
    form.post(route('expenses.update2', props.expense.id), {
        forceFormData: true,
    });
};
</script>
