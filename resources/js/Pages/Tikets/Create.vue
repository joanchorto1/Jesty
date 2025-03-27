<template>
    <AppLayout>
        <div class="flex w-full h-screen">
            <!-- Lado Izquierdo -->
            <div class="w-3/4 p-5 border-r border-gray-300">
                <!-- Botones para cambiar entre Venta y Artículos -->
                <div class="flex justify-end space-x-2 mb-4">
                    <button
                        @click="setVenta(true)"
                        :class="{
            'bg-blue-500 text-white': isVenta,
            'bg-gray-500 text-white': !isVenta
        }"
                        class="py-2 px-4 rounded"
                    >
                        Venta
                    </button>
                    <button
                        @click="setVenta(false)"
                        :class="{
            'bg-gray-500 text-white': isVenta,
            'bg-blue-500 text-white': !isVenta
        }"
                        class="py-2 px-4 rounded"
                    >
                        Artículos
                    </button>
                </div>


                <!-- Vista de Venta (Categorías y Productos) -->
                <div v-if="isVenta">

<!--                    Seccion de buscadores-->

                    <div class="grid grid-cols-3 gap-4 mb-6 pb-5 border-b-4">
                        <div>
                            <label for="codebar" class="block mb-2">Buscar por código de barras</label>
                            <input id="codebar" v-model="codebar" type="text" class="w-full p-2 border rounded" />
                        </div>
                        <div>
                            <label for="name" class="block mb-2">Buscar por nombre</label>
                            <input id="name" v-model="name" type="text" class="w-full p-2 border rounded" />
                        </div>

<!--                        Limpiar filtros-->
                        <div class="mt-6">
                            <button @click="clearFilters" class="items-center justify-center text-white p-4 rounded-lg" title="Limpiar filtros">
<DeleteIcon class="fill-gray-950 w-5 h-5 "/>
                            </button>
                        </div>
                    </div>




                    <!-- Categorías (widgets) -->


                    <div class="grid grid-cols-3 gap-4 mb-6 pb-5 border-b-4">
                        <div v-for="category in categories" :key="category.id" @click="selectCategory(category)"
                             class="bg-blue-100 p-4 rounded-lg shadow-lg font-bold uppercase cursor-pointer hover:bg-blue-200 transition">
                            {{ category.name }}
                        </div>
                    </div>

                    <!-- Productos según la categoría seleccionada -->
                    <div class="grid grid-cols-4 gap-4">
                        <div v-for="(product, index) in filteredProducts" :key="product.id" @click="addItemToTiket(product)" class="bg-white font-bold p-4 rounded-lg shadow-lg cursor-pointer hover:bg-blue-100 transition">
                            <div >
                                {{ product.name }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vista de Artículos -->
                <div v-else>
                    <h2 class="text-lg font-semibold mb-4">Listado de Productos del Ticket</h2>
                    <ul>
                        <li v-for="(item, index) in tiketItems" :key="item.id" class="mb-2 flex justify-between items-center">
                            <input
                                v-model="item.quantity"
                                class="w-16 p-2 border rounded"
                                type="number"
                                min="1"
                                @input="updateItemTotal(item)"
                            />
                            <template v-for="product in products">
                                <div v-if="product.id === item.product_id">{{ product.name }}</div>
                            </template>
                            <input
                                v-model="item.unit_price"
                                class="w-24 p-2 border rounded"
                                type="number"
                                step="0.01"
                                @input="updateItemTotal(item)"
                            />
                            <div>{{ item.total }} €</div>
                            <!-- Botón de eliminar artículo -->
                            <button @click="removeItem(index)" class="text-red-500">
                                <DeleteIcon class="w-4 h-4" />
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Lado Derecho -->
            <div class="w-1/3 p-5">
                <!-- Listado de los últimos 4 ítems del ticket -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Últimos ítems</h3>
                    <ul>
                        <li v-for="(item, index) in latestItems" :key="index" class="flex justify-between mb-2">
                            <input
                                v-model="item.quantity"
                                class="w-16 p-2 border rounded"
                                type="number"
                                min="1"
                                @input="updateItemTotal(item)"
                            />
                            <template v-for="product in products">
                                <div v-if="product.id === item.product_id">{{ product.name }}</div>
                            </template>
                            <input
                                v-model="item.unit_price"
                                class="w-24 p-2 border rounded"
                                type="number"
                                step="0.01"
                                @input="updateItemTotal(item)"
                            />
                            <div>{{ item.total }} €</div>
                            <!-- Botón de eliminar artículo -->
                            <button @click="removeItem(item.id)" class="text-red-500">
                                <DeleteIcon class="w-4 h-4" />
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- IVA y Total -->
                <div class="mb-4">
                    <label for="iva" class="block mb-2">IVA (%)</label>
                    <input id="iva" v-model="iva" type="number" class="w-full p-2 border rounded" />
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold">Total: {{ total }} €</h3>
                </div>

                <!-- Botones de acción -->
                <div class="flex space-x-2">
                    <button @click="openCobrarModal" title="cobrar" class="flex items-center justify-center text-white p-4 rounded-lg">
                        <PayIcon class="w-6 h-6" />
                    </button>
                    <button @click="eliminarTodo" title="eliminar todo" class="flex items-center justify-center text-white p-4 rounded-lg">
                        <DeleteIcon class="w-6 h-6" />
                    </button>
                </div>
            </div>

            <!-- Modal para cobrar -->

            <!-- Modal para cobrar -->
            <div v-if="isCobrarModalOpen" @close="closeCobrarModal"
                 class="fixed inset-0 bg-gray-950 bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-5 rounded-lg">
                    <h2 class="text-xl font-bold mb-4">Cobrar</h2>
                    <div class="mb-4">
                        <label for="receivedAmount" class="block mb-2">Monto recibido</label>
                        <input v-model="receivedAmount" type="number" class="w-full p-2 border rounded" @input="calculateChange" />
                    </div>
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Cambio: {{ change }} €</h3>
                    </div>
                    <button @click="confirmCobro" class="bg-blue-500 hover:bg-blue-400 text-white py-2 px-4 rounded">Cobrar</button>
                    <button @click="closeCobrarModal" class=" text-gray-500 hover:border-b-2 ml-3 py-1 px-4">Cancelar</button>
                </div>
            </div>

            <!-- Modal para preguntar si desea imprimir -->
            <div v-if="isImprimirModalOpen" @close="closeImprimirModal"
                 class="fixed inset-0 bg-gray-950 bg-opacity-50 flex items-center justify-center">
                <div class="p-5 bg-white rounded-md">
                    <h2 class="text-xl font-bold mb-4">¿Deseas imprimir el ticket?</h2>
                    <div class="flex space-x-4">
                        <button @click="imprimir" class="bg-blue-500 text-white py-2 px-4 rounded">Sí, imprimir</button>
                        <button @click="closeImprimirModal" class="bg-gray-500 text-white py-2 px-4 rounded">No, gracias</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import PrintIcon from "@/Components/Icons/PrintIcon.vue";
import PayIcon from "@/Components/Icons/PayIcon.vue";
import { Inertia } from '@inertiajs/inertia';

// Recibiendo categorías y productos mediante props
const props = defineProps({
    categories: Array,
    products: Array,
});

// Estado para controlar los modales
const isCobrarModalOpen = ref(false);
const isImprimirModalOpen = ref(false);
const receivedAmount = ref(0);
const change = ref(0);

// Estado local
const isVenta = ref(true);
const selectedCategory = ref(null);
const tiketItems = reactive([]);  // Usamos reactive para hacer que las actualizaciones se reflejen de inmediato
const iva = ref(21); // Iniciamos IVA en 21%
let tiketData = reactive({
    name: '',
    base_imponible: 0,
    iva: 0,
    monto_iva: 0,
    total: 0,
    tiketItems: []
});

// Computed para filtrar los productos según la categoría seleccionada
const filteredProducts = computed(() => {

    if (codebar.value) {
        return props.products.filter(product => product.codebar===codebar.value);
    }else if (name.value) {
        return props.products.filter(product =>
            product.name.toLowerCase().includes(name.value.toLowerCase())
        );
    }else {
        if (!selectedCategory.value) return props.products;
        return props.products.filter(product => product.category_id === selectedCategory.value.id);
    }

});

//Funcion para filtrar los productos por codebar
const codebar = ref('');
const name = ref('');

const clearFilters = () => {
    codebar.value = '';
    name.value = '';
};

// Computed para obtener los 4 últimos ítems del ticket
const latestItems = computed(() => tiketItems.slice(-4));

// Computed para calcular el total y extraer el IVA
const total = computed(() => {
    const subtotal = tiketItems.reduce((sum, item) => sum + parseFloat(item.total), 0);
    // Calculamos el monto del IVA
    const montoIva = (subtotal * iva.value) / (100 + iva.value);
    // La base imponible es el total menos el monto del IVA
    const baseImponible = subtotal - montoIva;

    tiketData.base_imponible = baseImponible.toFixed(2); // Base imponible
    tiketData.monto_iva = montoIva.toFixed(2); // Monto del IVA
    tiketData.total = subtotal.toFixed(2); // Total con IVA incluido

    return subtotal.toFixed(2);
});

// Métodos
const setVenta = (value) => {
    isVenta.value = value;
};

const selectCategory = (category) => {
    selectedCategory.value = category;
};

const addItemToTiket = (product) => {
    const existingItem = tiketItems.find(item => item.product_id === product.id);

    if (existingItem) {
        // Si el producto ya existe, actualizamos su cantidad y total
        existingItem.quantity += 1;
        existingItem.total = (existingItem.quantity * existingItem.unit_price).toFixed(2);
    } else {
        // Si el producto no existe, lo añadimos al array
        tiketItems.push({
            id: product.id,
            product_id: product.id,
            name: product.name,
            quantity: 1,
            unit_price: product.price,
            total: product.price,
        });
    }

    codebar.value = '';
    name.value = '';
};

const updateItemTotal = (item) => {
    item.total = (item.quantity * item.unit_price).toFixed(2);
};

const removeItem = (id) => {
    const index = tiketItems.findIndex(item => item.id === id);
    tiketItems.splice(index, 1);
};

const openCobrarModal = () => {
    isCobrarModalOpen.value = true;
};

const closeCobrarModal = () => {
    isCobrarModalOpen.value = false;
};

const calculateChange = () => {
    change.value = (tiketData.base_imponible.value - total.value).toFixed(2);
};

const confirmCobro = () => {
    // Lógica de cobro
    closeCobrarModal();
    // Preparar datos para enviar al controlador
    tiketData = {
        name: 'Ticket-' + new Date().toLocaleString(),
        base_imponible: tiketData.base_imponible,
        iva: iva.value,
        monto_iva: tiketData.monto_iva,
        total: tiketData.total,
        tiketItems: tiketItems
    };

    // Enviar los datos al controlador
    Inertia.post(route('tikets.store'), tiketData);
    // Mostrar modal para imprimir
    isImprimirModalOpen.value = true;
};

const eliminarTodo = () => {
    tiketItems.splice(0, tiketItems.length);
};

const imprimir = () => {
    Inertia.get(route('tickets.printNoID', tiketData ));
    closeImprimirModal();
};

const closeImprimirModal = () => {
    tiketData = {
        name: '',
        base_imponible: 0,
        iva: 0,
        monto_iva: 0,
        total: 0,
        tiketItems: []
    };
    tiketItems.splice(0, tiketItems.length);
    isImprimirModalOpen.value = false;
};



</script>

