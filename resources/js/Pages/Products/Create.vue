<script setup>
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from "@/Components/Icons/SaveIcon.vue";

const props = defineProps({
    categories: Array,
});

const data = reactive({
    form: {
        name: '',
        category_id: '',
        description: '',
        price: '',
        periodicity: '',
        is_stackable: false,
    },
});

const submitForm = () => {
    Inertia.post(route('products.store'), data.form, {
        onSuccess: () => {
            Inertia.visit(route('dashboard.services'));
        },
    });
};
</script>

<template>
    <AppLayout>
        <div class="p-5 bg-gray-50 min-h-screen mx-auto">
            <h1 class="text-3xl text-blue-700 font-bold mb-8">Crear servei</h1>
            <form @submit.prevent="submitForm">
                <!-- Campos del formulario -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nom del servei</label>
                    <input v-model="data.form.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" placeholder="Introdueix el nom del servei">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Descripció</label>
                    <textarea v-model="data.form.description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" placeholder="Descriu el servei"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Categoria</label>
                    <select v-model="data.form.category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category">
                        <option value="">Selecciona una categoria</option>
                        <template v-for="category in categories">
                            <option :value="category.id">{{ category.name }}</option>
                        </template>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="price">Preu</label>
                    <input v-model="data.form.price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="price" type="number" step="0.01" placeholder="Introdueix el preu">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="periodicity">Periodicitat</label>
                    <input v-model="data.form.periodicity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="periodicity" type="text" placeholder="Mensual, trimestral...">
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-900 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <SaveIcon class="w-5 h-5 fill-gray-50" />
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
