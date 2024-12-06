<template>
    <AppLayout>
        <div class="p-6 w-full mx-auto bg-gray-50 min-h-screen  shadow-md">
            <h1 class="text-3xl font-bold mb-8">Editar Presupuesto</h1>
            <form @submit.prevent="submitForm">
                <!-- Información del Presupuesto -->
                <div class="mb-4">
                    <label for="date" class="block text-gray-700">Fecha</label>
                    <input v-model="form.date" id="date" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="client_id" class="block text-gray-700">Cliente</label>
                    <select v-model="form.client_id" id="client_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Nombre del Presupuesto</label>
                    <input v-model="form.name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Nombre del Presupuesto">
                </div>
                <div class="mb-4">
                    <label for="state" class="block text-gray-700">Estado</label>
                    <select v-model="form.state" id="state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="in_process">En Proceso</option>
                        <option value="accepted">Aceptado</option>
                        <option value="rejected">Rechazado</option>
                    </select>
                </div>

                <!-- Nuevos Inputs: base imponible, IVA, monto IVA -->
                <div class="mb-4">
                    <label for="base_imponible" class="block text-gray-700">Base Imponible</label>
                    <input v-model="form.base_imponible" id="base_imponible" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="iva" class="block text-gray-700">IVA (%)</label>
                    <input v-model="form.iva" id="iva" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="monto_iva" class="block text-gray-700">Monto IVA</label>
                    <input v-model="form.monto_iva" id="monto_iva" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                </div>
                <div class="mb-4">
                    <label for="total" class="block text-gray-700">Total</label>
                    <input v-model="form.total" id="total" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                </div>

                <!-- Ítems del Presupuesto -->
                <template v-if="form.items.length > 0">
                    <h2 class="text-xl font-bold mb-4">Ítems del Presupuesto</h2>
                    <div class="grid grid-cols-6 gap-4 mb-2 font-semibold text-gray-600">
                        <div>Producto</div>
                        <div>Cantidad</div>
                        <div>Precio Unitario</div>
                        <div>Descuento (%)</div>
                        <div>Total</div>
                    </div>

                    <div v-for="(item, index) in form.items" :key="index" class="mb-4 grid grid-cols-6 gap-3 items-center">
                        <select v-model="item.product_id" @change="updateProductDetails(index)" class="border-gray-300 rounded-md shadow-sm">
                            <option value="" disabled>Seleccione un producto</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                        </select>
                        <input v-model="item.quantity" @input="updateItemTotal(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="1" placeholder="Cantidad">
                        <input v-model="item.unit_price" @input="updateItemTotal(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Precio Unitario">
                        <input v-model="item.discount" @input="updateItemTotal(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Descuento (%)">
                        <input v-model="item.total" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Total" disabled>
                        <button @click.prevent="removeItem(index)" class="w-2/12 items-center flex-col justify-center bg-gray-300 p-2 rounded-full">
                            <DeleteIcon class="w-5 h-5 stroke-gray-900 fill-gray-300"/>
                        </button>
                    </div>
                </template>

                <!-- Botón para abrir modal de productos -->
                <button @click.prevent="showProductModal = true" title="Agregar producto" class="bg-blue-400 p-2 rounded-full">
                    <AddProductIcon class="h-6 w-6 stroke-gray-50"/>
                </button>

                <div class="mt-8">
                    <button type="submit" class="bg-blue-900 p-2 rounded-full" title="Actualizar Presupuesto">
                        <SaveIcon class="w-6 h-6 fill-blue-50 stroke-gray-300"/>
                    </button>
                </div>
            </form>

            <!-- Modal de productos -->
            <div v-if="showProductModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
                <div class="bg-white w-1/2 p-6 rounded-lg shadow-lg">
                    <div class="flex justify-between mb-4">
                        <h2 class="text-xl font-bold">Seleccionar Producto</h2>
                        <button @click="showProductModal = false" class="text-gray-600">&times;</button>
                    </div>

                    <!-- Cuadro de búsqueda y filtros -->
                    <input v-model="searchTerm" type="text" placeholder="Buscar producto" class="border-gray-300 rounded-md shadow-sm w-full mb-4">
                    <select v-model="selectedCategory" class="border-gray-300 rounded-md shadow-sm w-full mb-4">
                        <option value="">Todas las categorías</option>
                        <option v-for="category in categories" :key="category.id" :value="category.name">{{ category.name }}</option>
                    </select>

                    <!-- Lista de productos filtrados -->
                    <ul class="max-h-64 overflow-y-auto">
                        <li v-for="product in filteredProducts" :key="product.id" class="flex justify-between py-2 border-b">
                            <span>{{ product.name }}</span>
                            <button @click="selectProduct(product)" class="bg-blue-900 p-2 rounded-full">
                                <AddProductIcon class="w-4 h-4 stroke-gray-50 "/>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from "@/Components/Icons/SaveIcon.vue";
import AddProductIcon from "@/Components/Icons/AddProductIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";

const props = defineProps({
    budget: Object,
    budgetItems: Array,
    products: Array,
    clients: Array,
    categories: Array,
});

const form = ref({
    client_id: props.budget.client_id,
    date: props.budget.date,
    name: props.budget.name || '',
    state: props.budget.state || 'in_process',
    base_imponible: props.budget.base_imponible || 0,
    iva: props.budget.iva || 0,
    monto_iva: props.budget.monto_iva || 0,
    total: props.budget.total || 0,
    items: props.budgetItems.map(item => ({
        product_id: item.product_id,
        quantity: item.quantity,
        discount: item.discount,
        unit_price: item.unit_price,
        total: item.total
    }))
});

const showProductModal = ref(false);
const searchTerm = ref('');
const selectedCategory = ref('');

// Actualiza detalles del producto seleccionado
const updateProductDetails = (index) => {
    const selectedProduct = props.products.find(product => product.id === form.value.items[index].product_id);
    if (selectedProduct) {
        form.value.items[index].unit_price = selectedProduct.price;
        form.value.items[index].total = selectedProduct.price * form.value.items[index].quantity;
        updateBudgetTotal(); // Recalcular el total del presupuesto
    }
};

// Actualiza el total de un ítem y el presupuesto
const updateItemTotal = (index) => {
    form.value.items[index].total = form.value.items[index].unit_price * form.value.items[index].quantity;
    if (form.value.items[index].discount!==0)
    {
        form.value.items[index].total = form.value.items[index].total - (form.value.items[index].total * form.value.items[index].discount / 100);
    }
    updateBudgetTotal();
};

// Recalcula el total del presupuesto
const updateBudgetTotal = () => {
    const baseImponible = form.value.items.reduce((acc, item) => acc + item.total, 0);
    form.value.base_imponible = baseImponible;
    form.value.monto_iva = (baseImponible * form.value.iva) / 100;
    form.value.total = baseImponible + form.value.monto_iva;
};

// Filtra productos por término de búsqueda y categoría
const filteredProducts = computed(() => {
    let filtered = props.products;
    if (searchTerm.value) {
        filtered = filtered.filter(product => product.name.toLowerCase().includes(searchTerm.value.toLowerCase()));
    }
    if (selectedCategory.value) {
        filtered = filtered.filter(product => product.category.name === selectedCategory.value);
    }
    return filtered;
});

// Seleccionar un producto y agregarlo al presupuesto
const selectProduct = (product) => {
    form.value.items.push({
        product_id: product.id,
        quantity: 1,
        unit_price: product.price,
        total: product.price
    });
    updateBudgetTotal();
    showProductModal.value = false;
};

// Remover ítem del presupuesto
const removeItem = (index) => {
    form.value.items.splice(index, 1);
    updateBudgetTotal();
};

// Enviar el formulario para actualizar el presupuesto
const submitForm = () => {
    Inertia.put(`/budgets/${props.budget.id}`, form.value);
};
</script>

<style scoped>
</style>
