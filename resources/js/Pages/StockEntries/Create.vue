<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen shadow-md">
            <h1 class="text-3xl font-bold mb-8">Crear Entrada de Stock</h1>
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Información de la Entrada de Stock -->
                    <div class="mb-4">
                        <label for="entry_date" class="block text-gray-700">Fecha de Entrada</label>
                        <input v-model="stockEntry.entry_date" id="entry_date" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="reference" class="block text-gray-700">Referencia</label>
                        <input v-model="stockEntry.reference" id="reference" type="text" placeholder="Referencia" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-gray-700">Estado</label>
                        <select v-model="stockEntry.status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="pending">Pendiente</option>
                            <option value="completed">Completada</option>
                            <option value="cancelled">Cancelada</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="block text-gray-700">Notas</label>
                        <textarea v-model="stockEntry.notes" id="notes" placeholder="Notas adicionales" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                </div>

                <!-- Mostrar ítems de la entrada -->
                <template v-if="stockEntryItems.length > 0">
                    <h2 class="text-xl font-bold mb-4">Ítems de la Entrada</h2>

                    <div class="grid grid-cols-4 gap-4 mb-2 font-semibold text-gray-600">
                        <div>Producto</div>
                        <div>Cantidad</div>
                        <div>Precio Unitario</div>
                        <div>Total</div>
                    </div>

                    <div v-for="(item, index) in stockEntryItems" :key="index" class="mb-4 grid grid-cols-5 gap-3 items-center">
                        <select v-model="item.product_id" @change="updateProductDetails(item)" class="border-gray-300 rounded-md shadow-sm">
                            <option value="" disabled>Seleccione un producto</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                        </select>
                        <input v-model="item.quantity" @input="updateItemTotal(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="1" placeholder="Cantidad">
                        <input v-model="item.unit_price" @input="updateItemTotal(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Precio Unitario">
                        <input v-model="item.total" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Total" disabled>
                        <button @click.prevent="removeItem(index)" class="w-2/12 items-center flex-col justify-center bg-gray-300 p-2 rounded-full">
                            <DeleteIcon class="w-5 h-5 stroke-gray-900 fill-gray-300"/>
                        </button>
                    </div>
                </template>

                <!-- Botón para agregar nuevo ítem -->
                <button @click.prevent="showProductModal = true" title="Agregar producto" class="bg-blue-500 p-2 rounded-full">
                    <AddProductIcon class="h-6 w-6 fill-gray-200 stroke-gray-50"/>
                </button>

                <!-- Resumen de la entrada -->
                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-4">Resumen</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="base_amount" class="block text-gray-700">Base Imponible</label>
                            <input v-model="baseAmount" id="base_amount" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                        </div>
                        <div>
                            <label for="tax_rate" class="block text-gray-700">Impuesto (%)</label>
                            <input v-model="taxRate" id="tax_rate" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" @input="calculateTotals">
                        </div>
                        <div>
                            <label for="tax_amount" class="block text-gray-700">Monto de Impuesto</label>
                            <input v-model="taxAmount" id="tax_amount" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                        </div>
                        <div>
                            <label for="total" class="block text-gray-700">Total</label>
                            <input v-model="stockEntry.total" id="total" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <button type="submit" class="bg-blue-900 p-2 rounded-full" title="Guardar Entrada">
                        <SaveIcon class="w-6 h-6 stroke-gray-50"/>
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

                    <input v-model="searchTerm" type="text" placeholder="Buscar producto" class="border-gray-300 rounded-md shadow-sm w-full mb-4">
                    <select v-model="selectedCategory" class="border-gray-300 rounded-md shadow-sm w-full mb-4">
                        <option value="">Todas las categorías</option>
                        <option v-for="category in categories" :key="category.id" :value="category.name">{{ category.name }}</option>
                    </select>

                    <ul class="max-h-64 overflow-y-auto">
                        <li v-for="product in filteredProducts" :key="product.id" class="flex justify-between py-2 border-b">
                            <span>{{ product.name }}</span>
                            <button @click="selectProduct(product)" class="bg-blue-900 p-1 rounded-full">
                                <AddProductIcon class="w-4 h-4 stroke-gray-50"/>
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
import SaveIcon from '@/Components/Icons/SaveIcon.vue';
import AddProductIcon from '@/Components/Icons/AddProductIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import {route} from "ziggy-js";

const props = defineProps({
    supplier: Object,
    products: Array,
    categories: Array,
});

const stockEntry = ref({
    entry_date: new Date().toISOString().split('T')[0],
    reference: '',
    status: 'pending',
    notes: '',
    total: 0,
});

const stockEntryItems = ref([]);
const baseAmount = ref(0);
const taxRate = ref(21);
const taxAmount = ref(0);

const addItem = () => {
    stockEntryItems.value.push({
        product_id: '',
        quantity: 1,
        unit_price: 0,
        total: 0,
    });
};

const removeItem = (index) => {
    stockEntryItems.value.splice(index, 1);
    calculateTotals();
};

const updateItemTotal = (index) => {
    const item = stockEntryItems.value[index];
    item.total = item.quantity * item.unit_price;
    calculateTotals();
};

const calculateTotals = () => {
    baseAmount.value = stockEntryItems.value.reduce((sum, item) => sum + item.total, 0);
    taxAmount.value = baseAmount.value * (taxRate.value / 100);
    stockEntry.value.total = baseAmount.value + taxAmount.value;
};

const showProductModal = ref(false);
const searchTerm = ref('');
const selectedCategory = ref('');

const filteredProducts = computed(() => {
    return props.products.filter(
        (product) =>
            product.name.toLowerCase().includes(searchTerm.value.toLowerCase()) &&
            (selectedCategory.value ? product.category === selectedCategory.value : true)
    );
});

const selectProduct = (product) => {
    stockEntryItems.value.push({
        product_id: product.id,
        quantity: 1,
        unit_price: product.cost_price,
        total: product.cost_price,
    });
    showProductModal.value = false;
    calculateTotals();
};

const submitForm = () => {
    Inertia.post(route('stockEntries.store'), {
        ...stockEntry.value,
        supplier_id: props.supplier.id,
        stock_entry_items: stockEntryItems.value,
        base_amount: baseAmount.value,
        tax_rate: taxRate.value,
        tax_amount: taxAmount.value,

    });
};
</script>
