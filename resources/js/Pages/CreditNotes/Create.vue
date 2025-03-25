<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen shadow-md">
            <h1 class="text-3xl font-bold mb-8">Crear Abonament</h1>
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 md:grid-cols-1 gap-4">

<!--                    Informacio del Abonament-->

                    <!-- Total sense IVA -->
                   <div class="w-full">
                       <p><strong>IVA:</strong>{{invoice.iva}}%</p>
                       <p><strong>Base Imponible:</strong>{{creditNote.total_without_tax}}</p>
                       <p><strong>Monto IVA:</strong>{{creditNote.tax_amount}}</p>
                       <p><strong>Total:</strong>{{creditNote.total_with_tax}}</p>
                   </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="expense_category_id" class="block text-gray-700">Categoria d'Expense</label>
                            <select v-model="creditNote.expense_category_id" id="expense_category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option v-for="category in expenseCategories" :key="category.id" :value="category.id">{{ category.name }}</option>
                            </select>
                        </div>

                        <!-- Mètode de Pagament -->
                        <div class="mb-4">
                            <label for="payment_method_id" class="block text-gray-700">Mètode de Pagament</label>
                            <select v-model="creditNote.payment_method_id" id="payment_method_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option v-for="method in paymentMethods" :key="method.id" :value="method.id">{{ method.name }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Categoria d'expenses -->

                </div>

                <!-- Ítems del Abonament -->
                <template v-if="creditNoteItems.length > 0">
                    <h2 class="text-xl font-bold mb-4">Ítems de l'Abonament</h2>
                    <div class="grid grid-cols-6 gap-4 mb-2 font-semibold text-gray-600">
                        <div>Producte</div>
                        <div>Cantitat</div>
                        <div>Preu Unitari</div>
                        <div>Descompte (%)</div>
                        <div>Total</div>
                    </div>

                    <div v-for="(item, index) in creditNoteItems" :key="index" class="mb-4 grid grid-cols-6 gap-3 items-center">
                        <select v-model="item.product_id" @change="validateStock(index)" class="border-gray-300 rounded-md shadow-sm">
                            <option value="" disabled>Seleccioneu un producte</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">
                                {{ product.name }}
                            </option>
                        </select>

                        <input v-model="item.quantity" @input="validateStock(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="1" placeholder="Quantitat">
                        <input v-model="item.unit_price" @input="validateStock(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Preu Unitari">
                        <input v-model="item.discount" @input="updateItemTotal(index)" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Descompte (%)">
                        <input v-model="item.total" class="border-gray-300 rounded-md shadow-sm" type="number" step="0.01" placeholder="Total" disabled>
                        <button @click.prevent="removeItem(index)" class="w-2/12 items-center flex-col justify-center bg-gray-300 p-2 rounded-full">
                            <DeleteIcon class="w-5 h-5 stroke-gray-900 fill-gray-300"/>
                        </button>
                    </div>
                </template>

                <!-- Botó per afegir producte -->
                <button @click.prevent="showProductModal = true" title="Nou producte" class="bg-blue-500 p-2 rounded-full">
                    <AddProductIcon class="h-6 w-6 fill-gray-200 stroke-gray-50"/>
                </button>

                <div class="mt-8 flex justify-between items-center">
                    <div>
                        <button type="submit" class="bg-blue-900 p-2 rounded-full" title="Guardar Abonament">
                            <SaveIcon class="w-6 h-6 stroke-gray-50" />
                        </button>
                    </div>
                </div>
            </form>

            <!-- Modal de productes -->
            <div v-if="showProductModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
                <div class="bg-white w-1/2 p-6 rounded-lg shadow-lg">
                    <div class="flex justify-between mb-4">
                        <h2 class="text-xl font-bold">Seleccionar Producte</h2>
                        <button @click="showProductModal = false" class="text-gray-600">&times;</button>
                    </div>

                    <!-- Quadre de cerca i filtres -->
                    <input v-model="searchTerm" type="text" placeholder="Cerca producte" class="border-gray-300 rounded-md shadow-sm w-full mb-4">
                    <select v-model="selectedCategory" class="border-gray-300 rounded-md shadow-sm w-full mb-4">
                        <option value="">Totes les categories</option>
                        <option v-for="category in expenseCategories" :key="category.id" :value="category.name">{{ category.name }}</option>
                    </select>

                    <!-- Llista de productes filtrats -->
                    <ul class="max-h-64 overflow-y-auto">
                        <li v-for="item in filteredProducts" :key="item.id" class="flex justify-between py-2 border-b">

                            <template v-for="product in products" :key="product.id">
                                <div v-if="item.product_id === product.id" class="w-1/2">{{ product.name }}</div>
                            </template>

                            <button @click="selectProduct(item)" class="bg-blue-900 p-1 rounded-full">
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
    invoice: Object,
    paymentMethods: Array,
    expenseCategories: Array,
    invoiceItems: Array,
    products:Array
});

// Inicialitzar abonament
const creditNote = ref({
    total_without_tax:'',
    tax_amount: '',
    total_with_tax: '',
    expense_category_id: '',
    payment_method_id: '',
});

// Inicialitzar ítems de l'Abonament
const creditNoteItems = ref([]);

const showProductModal = ref(false);
const searchTerm = ref('');
const selectedCategory = ref('');

const addItem = () => {
    creditNoteItems.value.push({
        product_id: '',
        quantity: 1,
        discount: 0,
        unit_price: 0,
        total: 0
    });
};

const removeItem = (index) => {
    creditNoteItems.value.splice(index, 1);
};

const updateItemTotal = (index) => {
    const item = creditNoteItems.value[index];
    item.total = item.quantity * item.unit_price * (1 - item.discount / 100);
    calculateTotal();
};

const calculateTotal = () => {
    let totalWithoutTax = 0;
    let totalTax = 0;
    creditNoteItems.value.forEach(item => {
        totalWithoutTax += item.total;
        totalTax += (item.total * props.invoice.iva)/100;  // Assumim un IVA del 21%
    });
    creditNote.value.total_without_tax = totalWithoutTax;
    creditNote.value.tax_amount = totalTax;
    creditNote.value.total_with_tax = totalWithoutTax + totalTax;
};

const validateStock = (index) => {
    const item = creditNoteItems.value[index];
    const selectedProduct = props.invoiceItems.find(p => p.product_id === item.product_id);
    if (item.quantity > selectedProduct.quantity) {
        item.quantity = selectedProduct.quantity;
    }
    updateItemTotal(index);
};

const submitForm = () => {
    Inertia.post(route('invoices.credit-notes.store', { invoice: props.invoice.id }), {
        ...creditNote.value,
        items: creditNoteItems.value,
    });
};


const filteredProducts = computed(() => {
    return props.invoiceItems
});

const selectProduct = (product) => {
    const product_real = props.products.find(p => p.id === product.product_id); // Usar `product.id` directamente
     creditNoteItems.value.push({
        product_id: product_real.id, // Aquí asignamos el ID del producto real
        quantity: 1,
        discount: 0,
        unit_price: product_real.price,
        total: product_real.price
    });
    console.log('CreditNoteItem', creditNoteItems.value);

    calculateTotal();
    showProductModal.value = false;
};

</script>
