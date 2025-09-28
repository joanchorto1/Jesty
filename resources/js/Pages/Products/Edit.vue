<template>
    <AppLayout>
        <div class="p-5 w-3/4 bg-white min-h-screen mx-auto">
            <h1 class="text-3xl font-bold mb-8">Editar servei</h1>
            <form @submit.prevent="submitForm">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nom del servei</label>
                    <input v-model="form.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" placeholder="Nom del servei">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Descripció</label>
                    <textarea v-model="form.description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" placeholder="Descriu el servei"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Categoria</label>
                    <select v-model="form.category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category">
                        <option value="">Selecciona una categoria</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="price">Preu</label>
                    <input v-model="form.price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="price" type="number" step="0.01" placeholder="Introdueix el preu">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="periodicity">Periodicitat</label>
                    <input v-model="form.periodicity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="periodicity" type="text" placeholder="Mensual, trimestral...">
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Actualitzar servei
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    categories: Array,
    product: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    company_id: props.product.company_id,
    name: props.product.name,
    category_id: props.product.category_id,
    description: props.product.description,
    price: props.product.price,
    periodicity: props.product.periodicity,
    is_stackable: Boolean(props.product.is_stackable),
});

const submitForm = () => {
    Inertia.put(route('products.update', props.product.id), form);
};
</script>

<style scoped>
/* Tailwind CSS is used, no additional styles needed */
</style>
