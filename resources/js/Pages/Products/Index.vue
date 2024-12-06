<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">

            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Productos</h2>
                        <p class="text-blue-300 text-2xl">{{ totalProducts }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Productos en Stock</h2>
                        <p class="text-blue-300 text-2xl">{{ inStock }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Productos Sin Stock</h2>
                        <p class="text-blue-300 text-2xl">{{ outOfStock }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
                <div class="flex flex-wrap gap-4">
                    <!-- Filtro por categoría -->
                    <div>
                        <label class="text-blue-500 text-sm font-semibold">Categoría:</label>
                        <select v-model="filters.category" class="w-full p-2 text-gray-500 border rounded-md">
                            <option value="">Todas</option>
                            <option v-for="category in categories" :key="category" :value="category">{{ category.name }}</option>
                        </select>
                    </div>
                    <!-- Filtro por estado -->
                    <div>
                        <label class="text-blue-500 text-sm font-semibold">Estado:</label>
                        <select v-model="filters.state" class="w-full p-2 text-gray-500 border rounded-md">
                            <option value="">Todos</option>
                            <option value="in_stock">En stock</option>
                            <option value="out_of_stock">Sin stock</option>
                        </select>
                    </div>
                    <!-- Botón Limpiar Filtros -->
                    <div class="mt-6">
                        <button @click="clearFilters" class="bg-red-500 w-full hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                            Limpiar Filtros
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla de productos -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <!-- Botón para crear un nuevo producto -->
                <div class="mb-6 w-1/6">
                    <NavLink :href="route('products.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center space-x-2">
                        <AddIcon class="w-5 h-5"/>
                        <span>Crear Nuevo Producto</span>
                    </NavLink>
                </div>
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Categoría</th>
                        <th class="px-4 py-2 text-left">Precio</th>
                        <th class="px-4 py-2 text-left">Stock</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="product in filteredProducts" :key="product.id" class="border-t">
                        <td class="px-4 py-2">{{ product.name }}</td>
                        <template v-for="category in categories">
                            <td v-if="category.id === product.category_id" class="px-4 py-2">{{ category.name }}</td>
                        </template>
                        <td class="px-4 py-2">{{ product.price  }}</td>
                        <td class="px-4 py-2">{{ product.stock }}</td>
                        <td class="px-4 py-2 flex space-x-2">
                            <NavLink :href="route('products.edit', product.id)" class="text-yellow-500">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteProduct(product.id)" class="text-red-500">
                                <DeleteIcon class="w-5 h-5"/>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    products: Array,
    categories: Array,
});

const filters = reactive({
    category: '',
    state: ''
});

const filteredProducts = computed(() => {
    return props.products.filter(product => {
        const matchesCategory = !filters.category || product.category_id === filters.category.id;
        const matchesState = !filters.state || (filters.state === 'in_stock' ? product.stock > 0 : product.stock === 0);
        return matchesCategory && matchesState;
    });
});

const totalProducts = computed(() => filteredProducts.value.length);
const inStock = computed(() => filteredProducts.value.filter(product => product.stock > 0).length);
const outOfStock = computed(() => totalProducts.value - inStock.value);

const clearFilters = () => {
    filters.category = '';
    filters.state = '';
};

const deleteProduct = (id) => {
    if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
        Inertia.delete(route('products.destroy', id));
    }
};
</script>

<style scoped>
/* Estilos personalizados aquí */
</style>
