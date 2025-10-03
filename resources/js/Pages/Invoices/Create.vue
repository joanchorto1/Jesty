<template>
    <AppLayout title="Nueva factura">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-6xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Nueva factura"
                    description="Configura los datos comerciales, selecciona los productos a facturar y controla los importes fiscales antes de confirmar el envío."
                    :icon="MenuInvoiceIcon"
                />

                <div class="grid gap-6 md:grid-cols-3">
                    <CrudStatCard
                        label="Clientes disponibles"
                        :value="totalClients"
                        :icon="MenuClientsIcon"
                        icon-background="bg-sky-500/10 text-sky-600"
                    >
                        <template #description>
                            <p class="text-xs text-slate-500">Filtra y elige el destinatario desde el directorio comercial.</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard
                        label="Productos activos"
                        :value="totalProducts"
                        :icon="MenuProductIcon"
                        icon-background="bg-indigo-500/10 text-indigo-600"
                    >
                        <template #description>
                            <p class="text-xs text-slate-500">Incluye artículos con tarifas actualizadas y stock disponible.</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard
                        label="Categorías sincronizadas"
                        :value="totalCategories"
                        :icon="MenuCategoryIcon"
                        icon-background="bg-emerald-500/10 text-emerald-600"
                    >
                        <template #description>
                            <p class="text-xs text-slate-500">Utiliza filtros rápidos para encontrar el producto adecuado.</p>
                        </template>
                    </CrudStatCard>
                </div>

                <form @submit.prevent="submitForm" class="space-y-10">
                    <section class="space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800">Datos de la factura</h2>
                                <p class="text-sm text-slate-500">Define el cliente, el estado de cobro y los parámetros fiscales.</p>
                            </div>
                            <div class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm">
                                <span>Total estimado</span>
                                <span>{{ formatCurrency(invoice.total) }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <InputLabel for="invoice-date" value="Fecha de emisión" />
                                <TextInput
                                    id="invoice-date"
                                    v-model="invoice.date"
                                    type="date"
                                    class="mt-2 block w-full"
                                />
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="invoice-name" value="Nombre interno" />
                                <TextInput
                                    id="invoice-name"
                                    v-model="invoice.name"
                                    type="text"
                                    class="mt-2 block w-full"
                                    placeholder="Factura proforma abril"
                                    autocomplete="off"
                                />
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="client-search" value="Buscar cliente" />
                                <TextInput
                                    id="client-search"
                                    v-model="clientSearchTerm"
                                    type="text"
                                    class="mt-2 block w-full"
                                    placeholder="Escribe para filtrar"
                                />
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="client" value="Cliente" />
                                <SelectInput id="client" v-model="invoice.client_id" class="mt-2 block w-full">
                                    <option value="">Selecciona un cliente</option>
                                    <option v-for="client in filteredClients" :key="client.id" :value="client.id">
                                        {{ client.name }}
                                    </option>
                                </SelectInput>
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="invoice-state" value="Estado" />
                                <SelectInput id="invoice-state" v-model="invoice.state" class="mt-2 block w-full">
                                    <option value="pending">Pendiente</option>
                                    <option value="paid">Pagada</option>
                                    <option value="cancelled">Cancelada</option>
                                </SelectInput>
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="invoice-iva" value="IVA (%)" />
                                <TextInput
                                    id="invoice-iva"
                                    v-model="ivaPercentage"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="mt-2 block w-full"
                                    @input="calculateTotals"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="space-y-2">
                                <InputLabel value="Base imponible" />
                                <TextInput
                                    :value="formatCurrency(baseImponible)"
                                    disabled
                                    class="mt-2 block w-full text-slate-600"
                                />
                            </div>
                            <div class="space-y-2">
                                <InputLabel value="IVA calculado" />
                                <TextInput
                                    :value="formatCurrency(montoIva)"
                                    disabled
                                    class="mt-2 block w-full text-slate-600"
                                />
                            </div>
                            <div class="space-y-2">
                                <InputLabel value="Total de la factura" />
                                <TextInput
                                    :value="formatCurrency(invoice.total)"
                                    disabled
                                    class="mt-2 block w-full text-slate-600"
                                />
                            </div>
                        </div>
                    </section>

                    <section class="space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800">Conceptos facturables</h2>
                                <p class="text-sm text-slate-500">Selecciona productos, ajusta cantidades y aplica descuentos personalizados.</p>
                            </div>
                            <button
                                type="button"
                                @click="showProductModal = true"
                                class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                            >
                                <AddProductIcon class="h-5 w-5" />
                                Añadir producto
                            </button>
                        </div>

                        <div v-if="invoiceItems.length === 0" class="rounded-3xl border border-dashed border-slate-300 bg-slate-50/60 p-10 text-center">
                            <p class="text-sm font-medium text-slate-600">Todavía no has añadido productos a la factura.</p>
                            <p class="mt-2 text-sm text-slate-500">Utiliza el buscador para seleccionar artículos del catálogo y comenzar a construir el detalle.</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div class="hidden grid-cols-12 gap-3 rounded-2xl border border-slate-200 bg-slate-100/60 px-4 py-3 text-sm font-semibold text-slate-500 md:grid">
                                <span class="md:col-span-4">Producto</span>
                                <span class="md:col-span-2">Cantidad</span>
                                <span class="md:col-span-2">Precio unitario</span>
                                <span class="md:col-span-2">Descuento (%)</span>
                                <span class="md:col-span-2">Importe</span>
                            </div>

                            <div
                                v-for="(item, index) in invoiceItems"
                                :key="index"
                                class="grid grid-cols-1 gap-4 rounded-3xl border border-slate-200/80 bg-white/80 p-4 shadow-sm md:grid-cols-12 md:items-end"
                            >
                                <div class="space-y-2 md:col-span-4">
                                    <InputLabel :for="`product-${index}`" value="Producto" />
                                    <SelectInput
                                        :id="`product-${index}`"
                                        v-model="item.product_id"
                                        class="mt-2 block w-full"
                                        @change="onProductSelected(index)"
                                    >
                                        <option value="">Selecciona un producto</option>
                                        <option v-for="product in props.products" :key="product.id" :value="product.id">
                                            {{ product.name }}
                                        </option>
                                    </SelectInput>
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <InputLabel :for="`quantity-${index}`" value="Cantidad" />
                                    <TextInput
                                        :id="`quantity-${index}`"
                                        v-model="item.quantity"
                                        type="number"
                                        min="1"
                                        step="1"
                                        class="mt-2 block w-full"
                                        @input="ensureStock(index)"
                                    />
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <InputLabel :for="`unit-price-${index}`" value="Precio unitario" />
                                    <TextInput
                                        :id="`unit-price-${index}`"
                                        v-model="item.unit_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="mt-2 block w-full"
                                        @input="updateItemTotal(index)"
                                    />
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <InputLabel :for="`discount-${index}`" value="Descuento (%)" />
                                    <TextInput
                                        :id="`discount-${index}`"
                                        v-model="item.discount"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="mt-2 block w-full"
                                        @input="updateItemTotal(index)"
                                    />
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <InputLabel :for="`total-${index}`" value="Importe" />
                                    <TextInput
                                        :id="`total-${index}`"
                                        :value="formatCurrency(item.total)"
                                        disabled
                                        class="mt-2 block w-full text-slate-600"
                                    />
                                </div>
                                <div class="flex items-center justify-end md:col-span-12">
                                    <button
                                        type="button"
                                        @click="removeItem(index)"
                                        class="inline-flex items-center gap-2 rounded-xl border border-rose-100 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-100"
                                    >
                                        <DeleteIcon class="h-4 w-4" />
                                        Eliminar línea
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="flex items-center justify-end">
                        <PrimaryButton type="submit" class="inline-flex items-center gap-2">
                            <SaveIcon class="h-4 w-4" />
                            Guardar factura
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

        <div
            v-if="showProductModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 px-4"
        >
            <div class="w-full max-w-2xl rounded-3xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800">Seleccionar producto</h3>
                        <p class="mt-1 text-sm text-slate-500">Filtra por categoría o nombre y añade el artículo a la factura.</p>
                    </div>
                    <button
                        type="button"
                        class="text-slate-400 transition hover:text-slate-600"
                        @click="showProductModal = false"
                    >
                        &times;
                    </button>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <InputLabel for="product-search" value="Buscar" />
                        <TextInput
                            id="product-search"
                            v-model="searchTerm"
                            type="text"
                            class="mt-2 block w-full"
                            placeholder="Nombre del producto"
                        />
                    </div>
                    <div class="space-y-2">
                        <InputLabel for="product-category" value="Categoría" />
                        <SelectInput id="product-category" v-model="selectedCategory" class="mt-2 block w-full">
                            <option value="">Todas las categorías</option>
                            <option v-for="category in props.categories" :key="category.id" :value="category.name">
                                {{ category.name }}
                            </option>
                        </SelectInput>
                    </div>
                </div>

                <ul class="mt-6 max-h-72 space-y-2 overflow-y-auto pr-1">
                    <li
                        v-for="product in filteredProducts"
                        :key="product.id"
                        class="flex items-center justify-between rounded-2xl border border-slate-200/70 bg-white px-4 py-3 shadow-sm"
                    >
                        <div>
                            <p class="text-sm font-semibold text-slate-700">{{ product.name }}</p>
                            <p class="text-xs text-slate-500">
                                {{ formatCurrency(product.price) }} ·
                                <span v-if="product.is_stackable">Stock: {{ product.stock }}</span>
                                <span v-else>Servicio sin control de stock</span>
                            </p>
                        </div>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-sky-700"
                            @click="selectProduct(product)"
                        >
                            <AddProductIcon class="h-4 w-4" />
                            Añadir
                        </button>
                    </li>
                    <li v-if="filteredProducts.length === 0" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                        No hay productos que coincidan con la búsqueda.
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SaveIcon from '@/Components/Icons/SaveIcon.vue';
import AddProductIcon from '@/Components/Icons/AddProductIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import MenuInvoiceIcon from '@/Components/Icons/MenuInvoiceIcon.vue';
import MenuClientsIcon from '@/Components/Icons/MenuClientsIcon.vue';
import MenuProductIcon from '@/Components/Icons/MenuProductIcon.vue';
import MenuCategoryIcon from '@/Components/Icons/MenuCategoryIcon.vue';

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const invoice = ref({
    date: new Date().toISOString().split('T')[0],
    name: '',
    state: 'pending',
    client_id: '',
    total: 0,
});

const invoiceItems = ref([]);
const showProductModal = ref(false);
const searchTerm = ref('');
const selectedCategory = ref('');
const ivaPercentage = ref(21);
const baseImponible = ref(0);
const montoIva = ref(0);
const clientSearchTerm = ref('');

const totalClients = computed(() => props.clients.length);
const totalProducts = computed(() => props.products.length);
const totalCategories = computed(() => props.categories.length);

const filteredClients = computed(() =>
    props.clients.filter((client) =>
        client.name.toLowerCase().includes(clientSearchTerm.value.toLowerCase())
    )
);

const filteredProducts = computed(() => {
    const term = searchTerm.value.toLowerCase();
    return props.products.filter((product) => {
        const matchesName = product.name.toLowerCase().includes(term);
        const matchesCategory = selectedCategory.value
            ? product.category?.name === selectedCategory.value
            : true;
        return matchesName && matchesCategory;
    });
});

const formatCurrency = (value) =>
    new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
    }).format(Number(value) || 0);

const sanitizeNumber = (value) => Number(value) || 0;

const calculateTotals = () => {
    baseImponible.value = invoiceItems.value.reduce((sum, item) => sum + sanitizeNumber(item.total), 0);
    const iva = sanitizeNumber(ivaPercentage.value);
    montoIva.value = baseImponible.value * (iva / 100);
    invoice.value.total = baseImponible.value + montoIva.value;
};

const addItem = () => {
    invoiceItems.value.push({
        product_id: '',
        quantity: 1,
        discount: 0,
        unit_price: 0,
        total: 0,
    });
};

const removeItem = (index) => {
    invoiceItems.value.splice(index, 1);
    calculateTotals();
};

const updateItemTotal = (index) => {
    const item = invoiceItems.value[index];
    const quantity = sanitizeNumber(item.quantity);
    const unitPrice = sanitizeNumber(item.unit_price);
    const discount = sanitizeNumber(item.discount);

    item.total = quantity * unitPrice;
    if (discount > 0) {
        item.total -= (item.total * discount) / 100;
    }
    calculateTotals();
};

const ensureStock = (index) => {
    const item = invoiceItems.value[index];
    const product = props.products.find((p) => p.id === item.product_id);
    if (!product) {
        updateItemTotal(index);
        return;
    }

    if (product.is_stackable) {
        const quantity = sanitizeNumber(item.quantity);
        if (quantity > product.stock) {
            item.quantity = product.stock || 0;
            alert(`Stock insuficiente para el producto ${product.name}`);
        } else if (quantity < 1) {
            item.quantity = 1;
        }
    } else if (!item.quantity || sanitizeNumber(item.quantity) < 1) {
        item.quantity = 1;
    }

    updateItemTotal(index);
};

const onProductSelected = (index) => {
    const item = invoiceItems.value[index];
    const product = props.products.find((p) => p.id === item.product_id);

    if (!product) {
        updateItemTotal(index);
        return;
    }

    if (product.is_stackable && (!product.stock || product.stock <= 0)) {
        alert(`El producto ${product.name} no tiene stock disponible.`);
        item.product_id = '';
        return;
    }

    item.unit_price = product.price;
    if (!item.quantity || sanitizeNumber(item.quantity) < 1) {
        item.quantity = 1;
    }

    updateItemTotal(index);
};

const selectProduct = (product) => {
    if (product.is_stackable && (!product.stock || product.stock <= 0)) {
        alert('Producto sin stock disponible');
        return;
    }

    addItem();
    const newItemIndex = invoiceItems.value.length - 1;
    const newItem = invoiceItems.value[newItemIndex];
    newItem.product_id = product.id;
    newItem.unit_price = product.price;
    newItem.quantity = 1;
    newItem.discount = 0;

    updateItemTotal(newItemIndex);
    showProductModal.value = false;
};

const submitForm = () => {
    Inertia.post(route('invoices.storeWithItems'), {
        ...invoice.value,
        invoiceItems: invoiceItems.value,
        iva: sanitizeNumber(ivaPercentage.value),
        monto_iva: montoIva.value,
        base_imponible: baseImponible.value,
    });
};
</script>
