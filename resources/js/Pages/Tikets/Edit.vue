<template>
    <AppLayout>
        <div class="flex w-full h-screen">
            <!-- Lado Izquierdo -->
            <div class="w-3/4 p-5 border-r border-gray-300">
                <!-- Botones para cambiar entre Venta y Artículos -->
                <div class="flex justify-end space-x-2 mb-4">
                    <button @click="setVenta(true)" class="bg-blue-500 text-white py-2 px-4 rounded">
                        Venta
                    </button>
                    <button @click="setVenta(false)" class="bg-gray-500 text-white py-2 px-4 rounded">
                        Artículos
                    </button>
                </div>

                <!-- Vista de Venta (Categorías y Productos) -->
                <div v-if="isVenta">
                    <!-- Categorías (widgets) -->
                    <div class="grid grid-cols-3 gap-4 mb-6 pb-5 border-b-4">
                        <div
                            v-for="category in categories"
                            :key="category.id"
                            @click="selectCategory(category)"
                            class="bg-blue-100 p-4 rounded-lg shadow-lg cursor-pointer hover:bg-blue-200 transition"
                        >
                            {{ category.name }}
                        </div>
                    </div>

                    <!-- Productos según la categoría seleccionada -->
                    <div class="grid grid-cols-4 gap-4">
                        <div
                            v-for="(product, index) in filteredProducts"
                            :key="product.id"
                            class="bg-white p-4 rounded-lg shadow-lg cursor-pointer hover:bg-blue-100 transition"
                        >
                            <div @click="addItemToTicket(product)">
                                {{ product.name }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vista de Artículos -->
                <div v-else>
                    <h2 class="text-lg font-semibold mb-4">Listado de Productos del Ticket</h2>
                    <ul>
                        <li
                            v-for="(item, index) in ticketItems"
                            :key="item.id"
                            class="mb-2 flex justify-between items-center"
                        >
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
            </div>

            <!-- Lado Derecho -->
            <div class="w-1/4 p-5">
                <!-- Listado de los últimos 4 ítems del ticket -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Últimos ítems</h3>
                    <ul>
                        <li
                            v-for="(item, index) in latestItems"
                            :key="index"
                            class="flex justify-between mb-2"
                        >
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
                    <button @click="openCobrarModal" title="actualizar y cobrar" class="flex items-center justify-center text-white p-4 rounded-lg">
                        <PayIcon class="w-6 h-6" />
                    </button>
                    <button @click="eliminarTodo" title="eliminar todo" class="flex items-center justify-center text-white p-4 rounded-lg">
                        <DeleteIcon class="w-6 h-6" />
                    </button>
                </div>
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
                        <button @click="confirmUpdate" class="bg-blue-500 text-white py-2 px-4 rounded">Cobrar</button>
                    </div>
                </div>
                <div v-if="isImprimirModalOpen" @close="closeImprimirModal"
                     class="fixed inset-0 bg-gray-950 bg-opacity-50 flex items-center justify-center">
                    <div class="p-5 bg-white rounded-md">
                        <h2 class="text-xl font-bold mb-4">¿Deseas imprimir el ticket actualizado?</h2>
                        <div class="flex space-x-4">
                            <button @click="imprimir" class="bg-blue-500 text-white py-2 px-4 rounded">Sí, imprimir</button>
                            <button @click="closeImprimirModal" class="bg-gray-500 text-white py-2 px-4 rounded">No, gracias</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import PayIcon from "@/Components/Icons/PayIcon.vue";
import { Inertia } from '@inertiajs/inertia';

// Props
const props = defineProps({
    categories: Array,
    products: Array,
    ticket: Object,
    ticketItems: Array,
});

// Estado local
const isVenta = ref(true);
const selectedCategory = ref(null);
const ticketItems = reactive([...props.ticketItems]);  // Copia para evitar modificar directamente los props
const iva = ref(props.ticket.iva);
const receivedAmount = ref(0);
const change = ref(0);
const isCobrarModalOpen = ref(false);
const isImprimirModalOpen = ref(false);

// Estado para los datos del ticket
let ticketData = reactive({
    name: props.ticket.name,
    base_imponible: 0,
    iva: iva.value,
    monto_iva: 0,
    total: 0,
    ticketItems: ticketItems,
});

// Computed para filtrar los productos por categoría
const filteredProducts = computed(() => {
    if (!selectedCategory.value) return props.products;
    return props.products.filter(product => product.category_id === selectedCategory.value.id);
});

// Computed para obtener los últimos 4 ítems del ticket
const latestItems = computed(() => ticketItems.slice(-4));

// Computed para calcular el total, base imponible y el monto de IVA
const total = computed(() => {
    const subtotal = ticketItems.reduce((sum, item) => sum + parseFloat(item.total), 0);
    const montoIva = (subtotal * iva.value) / (100 + iva.value);  // Calculando el monto del IVA
    const baseImponible = subtotal - montoIva;  // Base imponible es el subtotal menos el IVA

    ticketData.base_imponible = baseImponible.toFixed(2);  // Asignando base imponible
    ticketData.monto_iva = montoIva.toFixed(2);  // Asignando monto de IVA
    ticketData.total = subtotal.toFixed(2);  // Total con IVA incluido

    return subtotal.toFixed(2);
});

// Métodos
const setVenta = (value) => {
    isVenta.value = value;
};

const selectCategory = (category) => {
    selectedCategory.value = category;
};

const addItemToTicket = (product) => {
    const existingItem = ticketItems.find(item => item.product_id === product.id);

    if (existingItem) {
        // Si el producto ya existe, actualizamos su cantidad y total
        existingItem.quantity += 1;
        existingItem.total = (existingItem.quantity * existingItem.unit_price).toFixed(2);
    } else {
        // Si el producto no existe, lo añadimos al array
        ticketItems.push({
            id: product.id,
            product_id: product.id,
            name: product.name,
            quantity: 1,
            unit_price: product.price,
            total: product.price.toFixed(2),  // Asegurando que el total se guarda con 2 decimales
        });
    }
};

const updateItemTotal = (item) => {
    item.total = (item.quantity * item.unit_price).toFixed(2);  // Asegurando que el total se actualiza con 2 decimales
};

const removeItem = (id) => {
    const index = ticketItems.findIndex(item => item.id === id);
    ticketItems.splice(index, 1);
};

// Método para actualizar el ticket
const updateTicket = () => {
    const subtotal = ticketItems.reduce((sum, item) => sum + parseFloat(item.total), 0);
    const monto_iva = (subtotal * iva.value) / (100 + iva.value);  // Calculamos el monto de IVA
    const base_imponible = subtotal - monto_iva;  // Base imponible es el subtotal menos el IVA
    const totalTicket = subtotal.toFixed(2);  // Total con IVA incluido

    const ticketData = {
        name: props.ticket.name,
        ticketItems: ticketItems,
        monto_iva: monto_iva.toFixed(2),  // Asegurando que el monto de IVA tenga 2 decimales
        base_imponible: base_imponible.toFixed(2),  // Asegurando que la base imponible tenga 2 decimales
        iva: iva.value,
        total: totalTicket,
    };

    // Actualización del ticket
    Inertia.put(route('tikets.update', props.ticket.id), ticketData);
};

const eliminarTodo = () => {
    ticketItems.length = 0;  // Limpiar todos los ítems del ticket
};

// Métodos para manejar los modales
const openCobrarModal = () => {
    isCobrarModalOpen.value = true;
};

const closeCobrarModal = () => {
    isCobrarModalOpen.value = false;
};

const calculateChange = () => {
    change.value = (receivedAmount.value - parseFloat(ticketData.total)).toFixed(2);  // Asegurando que el cambio tenga 2 decimales
};

const confirmUpdate = () => {
    // Preparar los datos actualizados para enviar al servidor
    updateTicket();

    // Cerrar el modal de cobro y abrir el de imprimir
    closeCobrarModal();
    isImprimirModalOpen.value = true;
};

const closeImprimirModal = () => {
    Inertia.visit(route('tikets.goToCreate'));
    isImprimirModalOpen.value = false;
};

const imprimir = () => {
    // Enviar una solicitud para imprimir
    Inertia.get(route('tikets.print', props.ticket.id));

    // Limpiar los datos después de imprimir
    isImprimirModalOpen.value = false;
};
</script>

