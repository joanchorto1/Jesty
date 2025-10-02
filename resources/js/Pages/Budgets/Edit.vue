<template>
    <AppLayout title="Editar presupuesto">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-6xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Editar presupuesto"
                    description="Revisa la propuesta económica, ajusta las líneas presupuestadas y actualiza el estado de aceptación."
                    :icon="MenuBudgetIcon"
                />

                <div class="grid gap-6 md:grid-cols-3">
                    <CrudStatCard
                        label="Clientes disponibles"
                        :value="totalClients"
                        :icon="MenuClientsIcon"
                        icon-background="bg-sky-500/10 text-sky-600"
                    >
                        <template #description>
                            <p class="text-xs text-slate-500">Selecciona la cuenta adecuada antes de reenviar la propuesta.</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard
                        label="Productos activos"
                        :value="totalProducts"
                        :icon="MenuProductIcon"
                        icon-background="bg-indigo-500/10 text-indigo-600"
                    >
                        <template #description>
                            <p class="text-xs text-slate-500">Comprueba tarifas y disponibilidad en cada modificación.</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard
                        label="Categorías sincronizadas"
                        :value="totalCategories"
                        :icon="MenuCategoryIcon"
                        icon-background="bg-emerald-500/10 text-emerald-600"
                    >
                        <template #description>
                            <p class="text-xs text-slate-500">Aplica filtros rápidos para localizar servicios complementarios.</p>
                        </template>
                    </CrudStatCard>
                </div>

                <form @submit.prevent="submitForm" class="space-y-10">
                    <section class="space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800">Datos generales</h2>
                                <p class="text-sm text-slate-500">Actualiza la información básica y gestiona el estado del seguimiento comercial.</p>
                            </div>
                            <div class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm">
                                <span>Total actual</span>
                                <span>{{ formatCurrency(form.total) }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <InputLabel for="budget-date" value="Fecha de emisión" />
                                <TextInput id="budget-date" v-model="form.date" type="date" class="mt-2 block w-full" />
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="budget-name" value="Nombre interno" />
                                <TextInput
                                    id="budget-name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-2 block w-full"
                                    placeholder="Oferta comercial"
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
                                <InputLabel for="budget-client" value="Cliente" />
                                <SelectInput id="budget-client" v-model="form.client_id" class="mt-2 block w-full">
                                    <option v-for="client in filteredClients" :key="client.id" :value="client.id">{{ client.name }}</option>
                                </SelectInput>
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="budget-state" value="Estado" />
                                <SelectInput id="budget-state" v-model="form.state" class="mt-2 block w-full">
                                    <option value="in_process">En proceso</option>
                                    <option value="accepted">Aceptado</option>
                                    <option value="rejected">Rechazado</option>
                                </SelectInput>
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="budget-iva" value="IVA (%)" />
                                <TextInput
                                    id="budget-iva"
                                    v-model="form.iva"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="mt-2 block w-full"
                                    @input="updateBudgetTotal"
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
                                <h2 class="text-lg font-semibold text-slate-800">Conceptos presupuestados</h2>
                                <p class="text-sm text-slate-500">Ajusta cantidades, descuentos y controla el stock antes de compartir el documento.</p>
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
                            <p class="text-sm font-medium text-slate-600">No hay conceptos vinculados a este presupuesto.</p>
                            <p class="mt-2 text-sm text-slate-500">Busca productos del catálogo para reconstruir la propuesta.</p>
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
                            Actualizar presupuesto
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
                        <p class="mt-1 text-sm text-slate-500">Filtra por categoría o nombre para incorporar una nueva línea presupuestaria.</p>
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
import MenuBudgetIcon from '@/Components/Icons/MenuBudgetIcon.vue';
import MenuClientsIcon from '@/Components/Icons/MenuClientsIcon.vue';
import MenuProductIcon from '@/Components/Icons/MenuProductIcon.vue';
import MenuCategoryIcon from '@/Components/Icons/MenuCategoryIcon.vue';

const props = defineProps({
    budget: Object,
    budgetItems: Array,
    products: Array,
    clients: Array,
    categories: Array,
});

const form = ref({
    client_id: props.budget.client_id,
    date: props.budget.date,
    name: props.budget.name || '',
    state: props.budget.state || 'in_process',
    base_imponible: props.budget.base_imponible || 0,
    iva: props.budget.iva || 0,
    monto_iva: props.budget.monto_iva || 0,
    total: props.budget.total || 0,
    items: props.budgetItems.map((item) => ({
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

const updateBudgetTotal = () => {
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

    updateBudgetTotal();
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

    updateBudgetTotal();
    showProductModal.value = false;
};

const removeItem = (index) => {
    form.value.items.splice(index, 1);
    updateBudgetTotal();
};

const submitForm = () => {
    Inertia.put(`/budgets/${props.budget.id}`, form.value);
};

updateBudgetTotal();
</script>
