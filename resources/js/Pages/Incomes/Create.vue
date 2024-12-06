<template>
    <AppLayout>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-6">Crear Ingreso</h1>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="name" class="block font-medium text-gray-700">Nombre</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                    />
                    <span v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</span>
                </div>

                <div>
                    <label for="source" class="block font-medium text-gray-700">Origen</label>
                    <input
                        id="source"
                        v-model="form.source"
                        type="text"
                        class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                    />
                    <span v-if="form.errors.source" class="text-sm text-red-500">{{ form.errors.source }}</span>
                </div>

                <div>
                    <label for="tax_base" class="block font-medium text-gray-700">Base Imponible</label>
                    <input
                        id="tax_base"
                        v-model="form.tax_base"
                        type="number"
                        step="0.01"
                        class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                    />
                    <span v-if="form.errors.tax_base" class="text-sm text-red-500">{{ form.errors.tax_base }}</span>
                </div>

                <div>
                    <label for="tax_rate" class="block font-medium text-gray-700">IVA (%)</label>
                    <input
                        id="tax_rate"
                        v-model="form.tax_rate"
                        type="number"
                        step="0.01"
                        class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                    />
                    <span v-if="form.errors.tax_rate" class="text-sm text-red-500">{{ form.errors.tax_rate }}</span>
                </div>

                <div>
                    <label for="date" class="block font-medium text-gray-700">Fecha</label>
                    <input
                        id="date"
                        v-model="form.date"
                        type="date"
                        class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                    />
                    <span v-if="form.errors.date" class="text-sm text-red-500">{{ form.errors.date }}</span>
                </div>

            </div>

            <button
                type="submit"
                class="mt-6 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600"
            >
                Guardar
            </button>
        </form>
    </div>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
const props = defineProps({
    companies: Array,
});

const form = useForm({
    name: '',
    source: '',
    tax_base: '',
    tax_rate: '',
    date: '',
});

const submit = () => {
    form.post('/incomes');
};
</script>
