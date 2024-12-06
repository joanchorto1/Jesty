<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen shadow-md">
            <h1 class="text-3xl font-bold mb-8">Editar Entrada de Stock</h1>
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
import { route } from "ziggy-js";

const props = defineProps({
    stockEntry: Object,
    stockEntryItems: Array,
    products: Array,
    supplier: Object,
});

const stockEntry = ref({ ...props.stockEntry });
const stockEntryItems = ref([...props.stockEntryItems]);
const baseAmount = ref(0);
const taxRate = ref(21);
const taxAmount = ref(0);

const calculateTotals = () => {
    baseAmount.value = stockEntryItems.value.reduce((sum, item) => sum + item.total, 0);
    taxAmount.value = baseAmount.value * (taxRate.value / 100);
    stockEntry.value.total = baseAmount.value + taxAmount.value;
};

const updateItemTotal = (index) => {
    const item = stockEntryItems.value[index];
    item.total = item.quantity * item.unit_price;
    calculateTotals();
};

const removeItem = (index) => {
    stockEntryItems.value.splice(index, 1);
    calculateTotals();
};

const submitForm = () => {
    Inertia.put(route('stockEntries.update', stockEntry.value.id), {
        ...stockEntry.value,
        stock_entry_items: stockEntryItems.value,
        base_amount: baseAmount.value,
        tax_rate: taxRate.value,
        tax_amount: taxAmount.value,
    });
};
</script>
