<script setup>
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from "@/Components/Icons/SaveIcon.vue";

const props = defineProps({
    categories: Array,
    suppliers: Array,
    // Lista de proveedores
});

const data = reactive({
    form: {
        name: '',
        category_id: '',
        description: '',
        price: '',
        cost_price: '', // Nuevo campo
        stock: '',
        supplier_id: '', // Nuevo campo
        is_stackable: false, // Nuevo campo
    },
});

const submitForm = () => {
    Inertia.post(route('products.store'), data.form, {
        onSuccess: () => {
            Inertia.visit(route('dashboard.products'));
        },
    });
};
</script>

<template>
    <AppLayout>
        <div class="p-5 bg-gray-50 min-h-screen mx-auto">
            <h1 class="text-3xl text-blue-700 font-bold mb-8">Create Product</h1>
            <form @submit.prevent="submitForm">
                <!-- Campos del formulario -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Product Name</label>
                    <input v-model="data.form.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" placeholder="Enter product name">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
                    <textarea v-model="data.form.description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" placeholder="Enter product description"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Category</label>
                    <select v-model="data.form.category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category">
                        <option value="">Select a category</option>
                        <template v-for="category in categories">
                            <option :value="category.id">{{ category.name }}</option>
                        </template>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="supplier">Supplier</label>
                    <select v-model="data.form.supplier_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="supplier">
                        <option value="">Select a supplier</option>
                        <template v-for="supplier in suppliers">
                            <option :value="supplier.id">{{ supplier.name }}</option>
                        </template>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="price">Price</label>
                    <input v-model="data.form.price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="price" type="number" step="0.01" placeholder="Enter price">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="cost_price">Base Price</label>
                    <input v-model="data.form.cost_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="cost_price" type="number" step="0.01" placeholder="Enter base price">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="cost_price">Es stockable?</label>
                    <input type="checkbox" v-model="data.form.is_stackable" class="shadow appearance-none border rounded  py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="is_stackable" step="0.01" placeholder="Enter if is stackable">
                </div>

                <div class="mb-4" v-if="data.form.is_stackable">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="stock">Stock</label>
                    <input v-model="data.form.stock" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="stock" type="number" placeholder="Enter stock quantity">
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
