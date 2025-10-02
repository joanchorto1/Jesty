<template>
    <AppLayout title="Editar factura">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-6xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Editar factura"
                    description="Actualiza los datos comerciales, revisa el estado de cobro y ajusta los conceptos facturados antes de reenviar la documentación."
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
                            <p class="text-xs text-slate-500">Selecciona rápidamente el destinatario adecuado.</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard
                        label="Productos activos"
                        :value="totalProducts"
                        :icon="MenuProductIcon"
                        icon-background="bg-indigo-500/10 text-indigo-600"
                    >
                        <template #description>
                            <p class="text-xs text-slate-500">Consulta precios actualizados antes de confirmar cambios.</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard
                        label="Categorías sincronizadas"
                        :value="totalCategories"
                        :icon="MenuCategoryIcon"
                        icon-background="bg-emerald-500/10 text-emerald-600"
                    >
                        <template #description>
                            <p class="text-xs text-slate-500">Utiliza los filtros para localizar servicios complementarios.</p>
                        </template>
                    </CrudStatCard>
                </div>

                <form @submit.prevent="submitForm" class="space-y-10">
                    <section class="space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800">Datos generales</h2>
                                <p class="text-sm text-slate-500">Gestiona la fecha de emisión, el estado de la factura y la asignación al cliente.</p>
                            </div>
                            <div class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm">
                                <span>Total actual</span>
                                <span>{{ formatCurrency(form.total) }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <InputLabel for="invoice-date" value="Fecha de emisión" />
                                <TextInput id="invoice-date" v-model="form.date" type="date" class="mt-2 block w-full" />
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="invoice-name" value="Nombre interno" />
                                <TextInput
                                    id="invoice-name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-2 block w-full"
                                    placeholder="Factura trimestral"
                                />
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="client-search" value="Buscar cliente" />
                                <TextInput
                                    id="client-search"
                                    v-model="clientSearchTerm"
                                    type="text"
                                    class="mt-2 block w-full"
                                    placeholder="Filtra por nombre"
                                />
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="client" value="Cliente" />
                                <SelectInput id="client" v-model="form.client_id" class="mt-2 block w-full">
                                    <option v-for="client in filteredClients" :key="client.id" :value="client.id">{{ client.name }}</option>
                                </SelectInput>
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="invoice-state" value="Estado" />
                                <SelectInput id="invoice-state" v-model="form.state" class="mt-2 block w-full">
                                    <option value="pending">Pendiente</option>
                                    <option value="paid">Pagada</option>
                                    <option value="cancelled">Cancelada</option>
                                </SelectInput>
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="invoice-iva" value="IVA (%)" />
                                <TextInput
                                    id="invoice-iva"
                                    v-model="form.iva"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="mt-2 block w-full"
                                    @input="updateInvoiceTotal"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="space-y-2">
                                <InputLabel value="Base imponible" />
                                <TextInput :value="formatCurrency(form.base_imponible)" disabled class="mt-2 block w-full text-slate-600" />
                            </div>
                            <div class="space-y-2">
                                <InputLabel value="IVA calculado" />
                                <TextInput :value="formatCurrency(form.monto_iva)" disabled class="mt-2 block w-full text-slate-600" />
                            </div>
                            <div class="space-y-2">
                                <InputLabel value="Total" />
                                <TextInput :value="formatCurrency(form.total)" disabled class="mt-2 block w-full text-slate-600" />
                            </div>
                        </div>
                    </section>

                    <section class="space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800">Conceptos facturados</h2>
                                <p class="text-sm text-slate-500">Revisa cada línea, ajusta descuentos y controla el stock disponible.</p>
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

                        <div v-if="form.items.length === 0" class="rounded-3xl border border-dashed border-slate-300 bg-slate-50/60 p-10 text-center">
                            <p class="text-sm font-medium text-slate-600">No hay conceptos asociados a esta factura.</p>
                            <p class="mt-2 text-sm text-slate-500">Agrega productos desde el catálogo para reconstruir el detalle de la operación.</p>
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
                                v-for="(item, index) in form.items"
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
                                    <TextInput :id="`total-${index}`" :value="formatCurrency(item.total)" disabled class="mt-2 block w-full text-slate-600" />
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
                            Actualizar factura
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showProductModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 px-4">
            <div class="w-full max-w-2xl rounded-3xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800">Añadir producto</h3>
                        <p class="mt-1 text-sm text-slate-500">Filtra por categoría o nombre para incorporar una nueva línea.</p>
                    </div>
                    <button type="button" class="text-slate-400 transition hover:text-slate-600" @click="showProductModal = false">&times;</button>
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
                            <option v-for="category in props.categories" :key="category.id" :value="category.name">{{ category.name }}</option>
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
    items: props.invoiceItems.map((item) => ({
        product_id: item.product_id,
        quantity: item.quantity,
        discount: item.discount,
        unit_price: item.unit_price,
        total: item.total,
    })),
});

const showProductModal = ref(false);
const searchTerm = ref('');
const selectedCategory = ref('');
const clientSearchTerm = ref('');

const totalClients = computed(() => props.clients.length);
const totalProducts = computed(() => props.products.length);
const totalCategories = computed(() => props.categories.length);

const filteredClients = computed(() => {
    const term = clientSearchTerm.value.toLowerCase();
    if (!term) {
        return props.clients;
    }
    return props.clients.filter((client) => client.name.toLowerCase().includes(term));
});

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

const updateInvoiceTotal = () => {
    const baseImponible = form.value.items.reduce((acc, item) => acc + sanitizeNumber(item.total), 0);
    form.value.base_imponible = baseImponible;
    const iva = sanitizeNumber(form.value.iva);
    form.value.monto_iva = (baseImponible * iva) / 100;
    form.value.total = baseImponible + form.value.monto_iva;
};

const updateItemTotal = (index) => {
    const item = form.value.items[index];
    const quantity = sanitizeNumber(item.quantity);
    const unitPrice = sanitizeNumber(item.unit_price);
    const discount = sanitizeNumber(item.discount);

    item.total = quantity * unitPrice;
    if (discount > 0) {
        item.total -= (item.total * discount) / 100;
    }

    updateInvoiceTotal();
};

const ensureStock = (index) => {
    const item = form.value.items[index];
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
    const item = form.value.items[index];
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

    form.value.items.push({
        product_id: product.id,
        quantity: 1,
        discount: 0,
        unit_price: product.price,
        total: product.price,
    });

    updateInvoiceTotal();
    showProductModal.value = false;
};

const removeItem = (index) => {
    form.value.items.splice(index, 1);
    updateInvoiceTotal();
};

const submitForm = () => {
    Inertia.put(route('invoices.update', props.invoice.id), form.value);
};

updateInvoiceTotal();
</script>
