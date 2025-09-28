<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen shadow-md">
            <h1 class="text-3xl font-bold mb-8">Crear Factura</h1>
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Información de la Factura -->
                <div class="mb-4">
                    <label for="date" class="block text-gray-700">Fecha</label>
                    <input v-model="invoice.date" id="date" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Campo de Nombre -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Nombre de la Factura</label>
                    <input v-model="invoice.name" id="name" type="text" placeholder="Nombre de la Factura" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Campo de Estado -->
                <div class="mb-4">
                    <label for="state" class="block text-gray-700">Estado</label>
                    <select v-model="invoice.state" id="state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="pending">Pendiente</option>
                        <option value="paid">Pagada</option>
                        <option value="cancelled">Cancelada</option>
                    </select>
                </div>

                    <div class="mb-4">
                        <label for="client_id" class="block text-gray-700">Cliente</label>
                        <input v-model="clientSearchTerm" type="text" placeholder="Buscar cliente"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm mb-2">
                        <select v-model="invoice.client_id" id="client_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option v-for="client in filteredClients" :key="client.id" :value="client.id">{{
                                    client.name
                                }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="project_id" class="block text-gray-700">Proyecto</label>
                        <select v-model="invoice.project_id" id="project_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option :value="null">Sin proyecto</option>
                            <option v-for="project in filteredProjects" :key="project.id" :value="project.id">
                                {{ project.name }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                    <label for="total" class="block text-gray-700">Total</label>
                    <input v-model="invoice.total" id="total" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                </div>

                <!-- Campos adicionales -->
                <div class="mb-4">
                    <label for="base_imponible" class="block text-gray-700">Base Imponible</label>
                    <input v-model="baseImponible" id="base_imponible" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                </div>

                <div class="mb-4">
                    <label for="iva" class="block text-gray-700">IVA (%)</label>
                    <input v-model="ivaPercentage" id="iva" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" @input="calculateTotals">
                </div>

                <div class="mb-4">
                    <label for="monto_iva" class="block text-gray-700">Monto de IVA</label>
                    <input v-model="montoIva" id="monto_iva" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                </div>
                </div>

                <!-- Mostrar ítems de la factura solo si se ha agregado un producto -->
                <template v-if="invoiceItems.length > 0">
                    <h2 class="text-xl font-bold mb-4">Ítems de la Factura</h2>

                    <!-- Títulos de los inputs de los ítems -->
                    <div class="grid grid-cols-6 gap-4 mb-2 font-semibold text-gray-600">
                        <div>Producto</div>
                        <div>Cantidad</div>
                        <div>Precio Unitario</div>
                        <div>Descuento (%)</div>
                        <div>Total</div>
                    </div>

                    <div v-for="(item, index) in invoiceItems" :key="index" class="mb-4 grid grid-cols-6 gap-3 items-center">
                        <select v-model="item.product_id" @change="validateStock(index)" class="border-gray-300 rounded-md shadow-sm">
                            <option value="" disabled>Seleccione un producto</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                        </select>
                        <input v-model="item.quantity" @input="validateStock(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="1" placeholder="Cantidad">
                        <input v-model="item.unit_price" @input="validateStock(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Precio Unitario">
                        <input v-model="item.discount" @input="updateItemTotal(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Descuento (%)">
                        <input v-model="item.total" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Total" disabled>
                        <button @click.prevent="removeItem(index)" class="w-2/12 items-center flex-col justify-center bg-gray-300 p-2 rounded-full">
                            <DeleteIcon class="w-5 h-5 stroke-gray-900 fill-gray-300"/>
                        </button>
                    </div>
                </template>

                <!-- Botón para agregar nuevo producto -->
                <button @click.prevent="showProductModal = true" title="Nuevo producto" class="bg-blue-500 p-2 rounded-full">
                    <AddProductIcon class="h-6 w-6 fill-gray-200 stroke-gray-50"/>
                </button>

                <div class="mt-8 flex justify-between items-center">
                    <div>
                        <button type="submit" class="bg-blue-900 p-2 rounded-full" title="Guardar Factura">
                            <SaveIcon class="w-6 h-6 stroke-gray-50" />
                        </button>
                    </div>
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
                            <button @click="selectProduct(product)" class="bg-blue-900 p-1 rounded-full">
                                <AddProductIcon class="w-4 h-4 stroke-gray-50" />
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
    clients: Array,
    products: Array,
    categories: Array,
    projects: { type: Array, default: () => [] },
});

// Inicializar la factura
const invoice = ref({
    date: new Date().toISOString().split('T')[0], // Fecha actual
    name: '',
    state: 'pending',
    client_id: '',
    total: 0,
    iva: '',
    project_id: null,
});

// Inicializar items de la factura
const invoiceItems = ref([]);

const showProductModal = ref(false);
const searchTerm = ref('');
const selectedCategory = ref('');
const ivaPercentage = ref(21);  // IVA inicializado en 21
const baseImponible = ref(0);
const montoIva = ref(0);

const addItem = () => {
    invoiceItems.value.push({
        product_id: '',
        quantity: 1,  // Cantidad inicializada en 1
        discount: 0,
        unit_price: 0,
        total: 0,
    });
};

const removeItem = (index) => {
    invoiceItems.value.splice(index, 1);
    calculateTotals(); // Actualizar totales después de eliminar
};

const updateItemTotal = (index) => {
    const item = invoiceItems.value[index];
    item.total = item.quantity * item.unit_price;
    if(item.discount!==0){
        item.total = item.total - (item.total * item.discount / 100);
    }
    calculateTotals();
};

// Filtrar productos por nombre y categoría
const filteredProducts = computed(() => {
    let filtered = props.products.filter(product =>
        product.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    );

    if (selectedCategory.value) {
        filtered = filtered.filter(product => product.category.name === selectedCategory.value);
    }

    return filtered;
});

const selectProduct = (product) => {
    if ( product.stock > 0 && product.is_stackable) {
    addItem();
    const newItem = invoiceItems.value[invoiceItems.value.length - 1];
    newItem.product_id = product.id;
    newItem.unit_price = product.price;
    updateItemTotal(invoiceItems.value.length - 1);
    showProductModal.value = false; // Cerrar el modal después de seleccionar un producto
    } if (!product.is_stackable) {
        addItem();
        const newItem = invoiceItems.value[invoiceItems.value.length - 1];
        newItem.product_id = product.id;
        newItem.unit_price = product.price;
        updateItemTotal(invoiceItems.value.length - 1);
        showProductModal.value = false; // Cerrar el modal después de seleccionar un producto
    }    else {
        alert('Producto sin stock disponible');
    }
};

const calculateTotals = () => {
    baseImponible.value = invoiceItems.value.reduce((sum, item) => sum + item.total, 0);
    montoIva.value = baseImponible.value * (ivaPercentage.value / 100);
    invoice.value.total = baseImponible.value + montoIva.value;
};
const clientSearchTerm = ref('');

const filteredClients = computed(() => {
    return props.clients.filter(client => {
        return client.name.toLowerCase().includes(clientSearchTerm.value.toLowerCase());
    });
});

const filteredProjects = computed(() => {
    if (!invoice.value.client_id) {
        return props.projects;
    }

    return props.projects.filter(project => project.client_id === invoice.value.client_id);
});


const submitForm = () => {
    Inertia.post(route('invoices.storeWithItems'), {
        ...invoice.value,
        invoiceItems: invoiceItems.value,
        iva: ivaPercentage.value,
        monto_iva: montoIva.value,
        base_imponible: baseImponible.value,
    });
};

const validateStock = (index) => {
    const item = invoiceItems.value[index];
    const selectedProduct = props.products.find(product => product.id === item.product_id);
    if (!selectedProduct.is_stackable) {
        updateItemTotal(index) ;
    }else {
        if (item.quantity > selectedProduct.stock) {
            alert(`Stock insuficiente para el producto ${selectedProduct.name}`);
            item.quantity = selectedProduct.stock; // Ajusta al máximo disponible
        }
    }

    updateItemTotal(index);
};
</script>

<style scoped>
/* Agrega tus estilos personalizados aquí */
</style>
