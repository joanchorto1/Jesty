<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen shadow-md">
            <h1 class="text-3xl font-bold mb-8">Crear Presupuesto</h1>
            <form @submit.prevent="submitForm">
                <!-- Información del Presupuesto -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="mb-4">
                        <label for="date" class="block text-gray-700">Fecha</label>
                        <input v-model="budget.date" id="date" type="date"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700">Nombre del Presupuesto</label>
                        <input v-model="budget.name" id="name" type="text" placeholder="Nombre del Presupuesto"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="state" class="block text-gray-700">Estado</label>
                        <select v-model="budget.state" id="state"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="in_process">En Proceso</option>
                            <option value="accepted">Aceptado</option>
                            <option value="rejected">Rechazado</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="client_id" class="block text-gray-700">Cliente</label>
                        <input v-model="clientSearchTerm" type="text" placeholder="Buscar cliente"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm mb-2">
                        <select v-model="budget.client_id" id="client_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option v-for="client in filteredClients" :key="client.id" :value="client.id">{{
                                    client.name
                                }}</option>
                        </select>
                    </div>


                    <div class="mb-4">
                        <label for="total" class="block text-gray-700">Total</label>
                        <input v-model="budget.total" id="total" type="number" step="0.01"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                    </div>

                    <!-- Base Imponible y Monto de IVA -->
                    <div class="mb-4">
                        <label for="base_imponible" class="block text-gray-700">Base Imponible</label>
                        <input v-model="baseImponible" id="base_imponible" type="number" step="0.01"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                    </div>

                    <div class="mb-4">
                        <label for="iva" class="block text-gray-700">IVA (%)</label>
                        <input v-model="ivaPercentage" id="iva" type="number" step="0.01"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" @input="calculateTotals">
                    </div>

                    <div class="mb-4">
                        <label for="monto_iva" class="block text-gray-700">Monto de IVA</label>
                        <input v-model="montoIva" id="monto_iva" type="number" step="0.01"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" disabled>
                    </div>
                </div>

                <!-- Ítems del Presupuesto -->
                <template v-if="budgetItems.length > 0">
                    <h2 class="text-xl font-bold mb-4">Ítems del Presupuesto</h2>
                    <div class="grid grid-cols-6 gap-4 mb-2 font-semibold text-gray-600">
                        <div>Producto</div>
                        <div>Cantidad</div>
                        <div>Precio Unitario</div>
                        <div>Descuento (%)</div>
                        <div>Total</div>
                    </div>

                    <div v-for="(item, index) in budgetItems" :key="index"
                         class="mb-4 grid grid-cols-6 gap-3 items-center">
                        <select v-model="item.product_id" @change="validateStock(index)"
                                class="border-gray-300 rounded-md shadow-sm">
                            <option value="" disabled>Seleccione un producto</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">{{
                                    product.name
                                }}
                            </option>
                        </select>
                        <input v-model="item.quantity" @input="validateStock(index)"
                               class="border-gray-300 rounded-md shadow-sm" type="number" step="1"
                               placeholder="Cantidad">
                        <input v-model="item.unit_price" @input="validateStock(index)"
                               class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01"
                               placeholder="Precio Unitario">

                        <input v-model="item.discount" @input="updateItemTotal(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Descuento (%)">
                        <input v-model="item.total" class="border-gray-300 rounded-md shadow-sm" type="number"
                               step="0.01" placeholder="Total" disabled>
                        <button @click.prevent="removeItem(index)"
                                class="w-2/12 items-center flex-col justify-center  p-2 rounded-full">
                            <DeleteIcon class="w-5 h-5 "/>
                        </button>
                    </div>
                </template>

                <!-- Botón para agregar nuevo producto -->
                <button @click.prevent="showProductModal = true" title="Nuevo producto"
                        class="bg-blue-500 p-2 rounded-full">
                    <AddProductIcon class="h-6 w-6 fill-gray-200 stroke-gray-50"/>
                </button>

                <div class="mt-8 flex justify-between items-center">
                    <div>
                        <button type="submit" class="bg-blue-900 p-2 rounded-full" title="Guardar Presupuesto">
                            <SaveIcon class="w-6 h-6 stroke-gray-50 "/>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Modal de productos -->
            <div v-if="showProductModal"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
                <div class="bg-white w-1/2 p-6 rounded-lg shadow-lg">
                    <div class="flex justify-between mb-4">
                        <h2 class="text-xl font-bold">Seleccionar Producto</h2>
                        <button @click="showProductModal = false" class="text-gray-600">&times;</button>
                    </div>

                    <!-- Cuadro de búsqueda y filtros -->
                    <input v-model="searchTerm" type="text" placeholder="Buscar producto"
                           class="border-gray-300 rounded-md shadow-sm w-full mb-4">
                    <select v-model="selectedCategory" class="border-gray-300 rounded-md shadow-sm w-full mb-4">
                        <option value="">Todas las categorías</option>
                        <option v-for="category in categories" :key="category.id" :value="category.name">
                            {{ category.name }}
                        </option>
                    </select>

                    <!-- Lista de productos filtrados -->
                    <ul class="max-h-64 overflow-y-auto">
                        <li v-for="product in filteredProducts" :key="product.id"
                            class="flex justify-between py-2 border-b">
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
import {computed, ref} from 'vue';
import {Inertia} from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from "@/Components/Icons/SaveIcon.vue";
import AddProductIcon from "@/Components/Icons/AddProductIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";

const props = defineProps({
    clients: Array,
    products: Array,
    categories: Array,
});

// Inicializar el presupuesto
const budget = ref({
    date: new Date().toISOString().split('T')[0], // Fecha actual
    name: '',
    state: 'in_process',
    client_id: '',
    total: 0,
});

// Inicializar items del presupuesto
const budgetItems = ref([]);
const showProductModal = ref(false);
const searchTerm = ref('');
const selectedCategory = ref('');
const ivaPercentage = ref(21);  // IVA inicializado en 21
const baseImponible = ref(0);
const montoIva = ref(0);

const addItem = () => {
    budgetItems.value.push({
        product_id: '',
        quantity: 1,
        discount: 0,
        unit_price: 0,
        total: 0,
    });
};

const removeItem = (index) => {
    budgetItems.value.splice(index, 1);
    calculateTotals();
};

const updateItemTotal = (index) => {
    const item = budgetItems.value[index];
    item.total = item.quantity * item.unit_price;
    item.total = item.total - (item.total * item.discount) / 100;
    calculateTotals();
};

const calculateTotals = () => {

    baseImponible.value= budgetItems.value.reduce((sum, item) => sum + item.total, 0);
    montoIva.value = (baseImponible.value * ivaPercentage.value) / 100;
    budget.value.total = baseImponible.value + montoIva.value;
};

// Filtrar productos
const filteredProducts = computed(() => {
    return props.products.filter(product => {
        const matchesCategory = selectedCategory.value ? product.category === selectedCategory.value : true;
        const matchesSearch = product.name.toLowerCase().includes(searchTerm.value.toLowerCase());
        return matchesCategory && matchesSearch;
    });
});

// Seleccionar un producto
const selectProduct = (product) => {
    if ( product.stock > 0) {
        addItem();
        const lastIndex = budgetItems.value.length - 1;
        budgetItems.value[lastIndex].product_id = product.id;
        budgetItems.value[lastIndex].unit_price = product.price; // Asignar el precio unitario del producto
        updateItemTotal(lastIndex); // Actualizar total del item
        showProductModal.value = false; // Cerrar modal
    }else {
        alert('Stock insuficiente');
        showProductModal.value = false; // Cerrar modal
    }

};
const clientSearchTerm = ref('');

const filteredClients = computed(() => {
    return props.clients.filter(client => {
        return client.name.toLowerCase().includes(clientSearchTerm.value.toLowerCase());
    });
});


// Submit del formulario
const submitForm = () => {
    // Lógica para enviar el formulario
    Inertia.post(route('budgets.storeWithItems'), {
        ...budget.value,
        budgetItems: budgetItems.value,
        iva: ivaPercentage.value,
        monto_iva: montoIva.value,
        base_imponible: baseImponible.value,
    });

};

const validateStock = (index) => {
    const item = budgetItems.value[index];
    const selectedProduct = props.products.find(product => product.id === item.product_id);
    if (item.quantity > selectedProduct.stock) {
        alert(`Stock insuficiente para el producto ${selectedProduct.name}`);
        item.quantity = selectedProduct.stock; // Ajusta al máximo disponible
    }
    updateItemTotal(index);
};

</script>

<style scoped>
/* Estilos personalizados si son necesarios */
</style>
