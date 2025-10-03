<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen">
            <div class="mx-auto max-w-4xl">
                <h1 class="text-3xl font-semibold mb-6 text-gray-800">Registrar método de pago</h1>

                <form @submit.prevent="submitForm" class="space-y-6 bg-white p-6 rounded-lg shadow">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre del método</label>
                        <input
                            v-model="form.name"
                            id="name"
                            type="text"
                            placeholder="Ej. Transferencia bancaria"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea
                            v-model="form.description"
                            id="description"
                            rows="4"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Describe cómo se utiliza este método de pago"
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <Link :href="route('paymentMethods.index')" class="text-sm font-medium text-gray-600 hover:text-gray-800">Cancelar y volver</Link>
                        <button type="submit" class="inline-flex items-center rounded-full bg-blue-900 px-4 py-2 text-white shadow hover:bg-blue-700">
                            <SaveIcon class="mr-2 h-5 w-5 stroke-gray-100" />
                            Guardar método
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from '@/Components/Icons/SaveIcon.vue';

const form = useForm({
    name: '',
    description: '',
});

const submitForm = () => {
    form.post(route('paymentMethods.store'));
};
</script>
