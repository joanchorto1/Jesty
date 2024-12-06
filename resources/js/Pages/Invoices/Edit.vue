<template>
    <AppLayout>
        <div class="p-6 w-full mx-auto bg-gray-50 min-h-screen shadow-md">
            <h1 class="text-3xl font-bold mb-8">Editar Factura</h1>
            <form @submit.prevent="submitForm">
                <!-- Información de la Factura -->
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
                    <label for="name" class="block text-gray-700">Nombre de la Factura</label>
                    <input v-model="form.name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Nombre de la Factura">
                </div>
                <div class="mb-4">
                    <label for="state" class="block text-gray-700">Estado</label>
                    <select v-model="form.state" id="state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="pending">Pendiente</option>
                        <option value="paid">Pagada</option>
                        <option value="cancelled">Cancelada</option>
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

                <!-- Ítems de la Factura -->
                <template v-if="form.items.length > 0">
                    <h2 class="text-xl font-bold mb-4">Ítems de la Factura</h2>
                    <div class="grid grid-cols-6 gap-4 mb-2 font-semibold text-gray-600">
                        <div>Producto</div>
                        <div>Cantidad</div>
                        <div>Precio Unitario</div>
                        <div>Descuento(%)</div>
                        <div>Total</div>
                    </div>

                    <div v-for="(item, index) in form.items" :key="index" class="mb-4 grid grid-cols-6 gap-3 items-center">
                        <select v-model="item.product_id" @change="updateProductDetails(index)" class="border-gray-300 rounded-md shadow-sm">
                            <option value="" disabled>Seleccione un producto</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                        </select>
                        <input v-model="item.quantity" @input="updateItemTotal(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="1" placeholder="Cantidad">
                        <input v-model="item.unit_price" @input="updateItemTotal(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Precio Unitario">
                        <input v-model="item.discount" @input="updateItemTotal(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Descuento">
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
                    <button type="submit" class="bg-blue-900 p-2 rounded-full" title="Actualizar Factura">
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
import { ref, computed, onMounted } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from "@/Components/Icons/SaveIcon.vue";
import AddProductIcon from "@/Components/Icons/AddProductIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";

const props = defineProps({
    invoice: Object,
    invoiceItems: Array,
    products: Array,
    clients: Array,
    categories: Array,
});

const form = ref({
    client_id: props.invoice.client_id,
    date: props.invoice.date,
    name: props.invoice.name || '',
    state: props.invoice.state || 'pending',
    base_imponible: props.invoice.base_imponible || 0,
    iva: props.invoice.iva || 0,
    monto_iva: props.invoice.monto_iva || 0,
    total: props.invoice.total || 0,
    items: props.invoiceItems.map(item => ({
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
        updateInvoiceTotal(); // Recalcular el total de la factura
    }
};

// Actualiza el total de un ítem cuando cambian cantidad o precio
const updateItemTotal = (index) => {
    const item = form.value.items[index];
    item.total = item.quantity * item.unit_price;
    if (item.discount!==0) {
        item.total -= (item.total * item.discount) / 100;
    }
    updateInvoiceTotal();
};

// Calcula el total de la factura
const updateInvoiceTotal = () => {
    const baseImponible = form.value.items.reduce((acc, item) => acc + item.total, 0);
    const iva = form.value.iva;
    form.value.base_imponible = baseImponible;
    form.value.monto_iva = (baseImponible * iva) / 100;
    form.value.total = baseImponible + form.value.monto_iva;
};

// Filtra productos basados en búsqueda y categoría
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

// Selecciona un producto del modal
const selectProduct = (product) => {
    form.value.items.push({
        product_id: product.id,
        quantity: 1,
        unit_price: product.price,
        total: product.price
    });
    updateInvoiceTotal();
    showProductModal.value = false; // Cierra el modal
};

// Elimina un ítem de la factura
const removeItem = (index) => {
    form.value.items.splice(index, 1);
    updateInvoiceTotal();
};

// Enviar el formulario para actualizar la factura
const submitForm = () => {
    Inertia.put(route('invoices.update',props.invoice.id), form.value);
};
</script>

