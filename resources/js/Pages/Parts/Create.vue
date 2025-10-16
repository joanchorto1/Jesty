<template>
    <AppLayout title="Nou parte">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-5xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Nou parte"
                    description="Registra el treball realitzat per associar-lo a un client i convertir-lo fàcilment en factura."
                    :icon="MenuPartIcon"
                />

                <form @submit.prevent="submit" class="space-y-10">
                    <section class="space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800">Dades del parte</h2>
                                <p class="text-sm text-slate-500">Defineix la referència interna, el client i la data del servei.</p>
                            </div>
                            <div class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm">
                                <span>Total estimat</span>
                                <span>{{ formatCurrency(partTotal) }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <InputLabel value="Número de parte" />
                                <div class="mt-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
                                    {{ partReferencePreview }}
                                </div>
                                <p class="text-xs text-slate-500">
                                    Assignem el codi automàticament seguint la seqüència correlativa anual.
                                </p>
                                <InputError :message="form.errors.reference" />
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="part-date" value="Data" />
                                <TextInput
                                    id="part-date"
                                    v-model="form.date"
                                    type="date"
                                    class="mt-2 block w-full"
                                />
                                <InputError :message="form.errors.date" />
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="part-client" value="Client" />
                                <SelectInput id="part-client" v-model="form.client_id" class="mt-2 block w-full">
                                    <option value="">Selecciona un client</option>
                                    <option v-for="client in props.clients" :key="client.id" :value="client.id">
                                        {{ client.name }}
                                    </option>
                                </SelectInput>
                                <InputError :message="form.errors.client_id" />
                            </div>
                            <div class="space-y-2">
                                <InputLabel for="part-notes" value="Notes internes" />
                                <TextareaInput
                                    id="part-notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="mt-2 block w-full rounded-md border border-slate-200"
                                    placeholder="Detalls addicionals del servei realitzat"
                                />
                            </div>
                        </div>
                    </section>

                    <section class="space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800">Línies del parte</h2>
                                <p class="text-sm text-slate-500">Selecciona el producte realitzat i la quantitat corresponent.</p>
                            </div>
                            <button
                                type="button"
                                @click="openProductModal"
                                class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                            >
                                <AddProductIcon class="h-5 w-5" />
                                Afegir línia
                            </button>
                        </div>

                        <div v-if="items.length === 0" class="rounded-3xl border border-dashed border-slate-300 bg-slate-50/60 p-10 text-center">
                            <p class="text-sm font-medium text-slate-600">Encara no has afegit cap producte al parte.</p>
                            <p class="mt-2 text-sm text-slate-500">Utilitza el botó d'afegir línia per seleccionar un producte.</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div class="hidden grid-cols-12 gap-3 rounded-2xl border border-slate-200 bg-slate-100/60 px-4 py-3 text-sm font-semibold text-slate-500 md:grid">
                                <span class="md:col-span-5">Producte</span>
                                <span class="md:col-span-2">Quantitat</span>
                                <span class="md:col-span-2">Preu unitari</span>
                                <span class="md:col-span-2">Import</span>
                                <span class="md:col-span-1 text-right">Accions</span>
                            </div>

                            <div
                                v-for="(item, index) in items"
                                :key="index"
                                class="grid grid-cols-1 gap-4 rounded-3xl border border-slate-200/80 bg-white/80 p-4 shadow-sm md:grid-cols-12 md:items-end"
                            >
                                <div class="space-y-2 md:col-span-5">
                                    <InputLabel :for="`product-${index}`" value="Producte" />
                                    <SelectInput
                                        :id="`product-${index}`"
                                        v-model="item.product_id"
                                        class="mt-2 block w-full"
                                        @change="onProductSelected(index)"
                                    >
                                        <option value="">Selecciona un producte</option>
                                        <option v-for="product in props.products" :key="product.id" :value="product.id">
                                            {{ product.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError :message="form.errors[`items.${index}.product_id`]" />
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <InputLabel :for="`quantity-${index}`" value="Quantitat" />
                                    <TextInput
                                        :id="`quantity-${index}`"
                                        v-model="item.quantity"
                                        type="number"
                                        min="1"
                                        class="mt-2 block w-full"
                                        @input="ensureQuantity(index)"
                                    />
                                    <InputError :message="form.errors[`items.${index}.quantity`]" />
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <InputLabel :for="`unit-price-${index}`" value="Preu unitari" />
                                    <TextInput
                                        :id="`unit-price-${index}`"
                                        v-model="item.unit_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="mt-2 block w-full"
                                        disabled
                                    />
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <InputLabel :for="`total-${index}`" value="Import" />
                                    <TextInput
                                        :id="`total-${index}`"
                                        :value="formatCurrency(item.total)"
                                        class="mt-2 block w-full text-slate-600"
                                        disabled
                                    />
                                </div>
                                <div class="flex justify-end md:col-span-1">
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-xl border border-transparent bg-rose-100 px-3 py-2 text-xs font-semibold text-rose-600 shadow-sm transition hover:bg-rose-200"
                                        @click="removeItem(index)"
                                    >
                                        <DeleteIcon class="h-4 w-4" />
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                            <InputError :message="form.errors['items']" />
                        </div>
                    </section>

                    <div class="flex justify-end">
                        <PrimaryButton type="submit" :disabled="form.processing">
                            Guardar parte
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showProductModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 px-4">
            <div class="w-full max-w-2xl rounded-3xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800">Seleccionar producte</h3>
                        <p class="mt-1 text-sm text-slate-500">Filtra pel nom o la categoria per afegir-lo al parte.</p>
                    </div>
                    <button type="button" class="text-slate-400 transition hover:text-slate-600" @click="closeProductModal">&times;</button>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <InputLabel for="product-search" value="Cercar" />
                        <TextInput
                            id="product-search"
                            v-model="searchTerm"
                            type="text"
                            class="mt-2 block w-full"
                            placeholder="Nom o codi de barres"
                        />
                    </div>
                    <div class="space-y-2">
                        <InputLabel for="product-category" value="Categoria" />
                        <SelectInput id="product-category" v-model="selectedCategory" class="mt-2 block w-full">
                            <option value="">Totes les categories</option>
                            <option v-for="category in availableCategories" :key="category" :value="category">
                                {{ category }}
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
                                {{ formatCurrency(product.price) }}
                                <span v-if="product.category" class="ml-1">· {{ product.category.name }}</span>
                            </p>
                        </div>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-sky-700"
                            @click="selectProduct(product)"
                        >
                            <AddProductIcon class="h-4 w-4" />
                            Afegir
                        </button>
                    </li>
                    <li v-if="filteredProducts.length === 0" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                        No hi ha productes que coincideixin amb la cerca.
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import MenuPartIcon from '@/Components/Icons/MenuPartIcon.vue';
import AddProductIcon from '@/Components/Icons/AddProductIcon.vue';

const props = defineProps({
    clients: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
    nextPartReference: { type: String, default: '' },
});

const form = useForm({
    reference: props.nextPartReference || '',
    date: new Date().toISOString().split('T')[0],
    client_id: '',
    notes: '',
});

const items = ref([]);
const showProductModal = ref(false);
const searchTerm = ref('');
const selectedCategory = ref('');

const formatCurrency = (value) =>
    new Intl.NumberFormat('ca-ES', {
        style: 'currency',
        currency: 'EUR',
    }).format(Number(value) || 0);

const partTotal = computed(() => items.value.reduce((total, item) => total + Number(item.total || 0), 0));

const availableCategories = computed(() => {
    const categories = props.products
        .map((product) => product.category?.name)
        .filter((category) => Boolean(category));

    return Array.from(new Set(categories)).sort((a, b) => a.localeCompare(b, 'ca')); // alphabetical order
});

const partReferencePreview = computed(() => {
    if (form.reference) {
        return form.reference;
    }

    if (props.nextPartReference) {
        return props.nextPartReference;
    }

    return 'Es generarà automàticament en guardar';
});

const filteredProducts = computed(() => {
    const term = searchTerm.value.trim().toLowerCase();

    return props.products
        .filter((product) => {
            const matchesCategory = selectedCategory.value
                ? product.category && product.category.name === selectedCategory.value
                : true;

            if (!term) {
                return matchesCategory;
            }

            const name = product.name?.toLowerCase() ?? '';
            const codebar = product.codebar?.toLowerCase() ?? '';

            return (
                matchesCategory &&
                (name.includes(term) || codebar.includes(term))
            );
        })
        .sort((a, b) => a.name.localeCompare(b.name, 'ca'));
});

const sanitizeNumber = (value) => Number(value) || 0;

const updateItemTotal = (index) => {
    const item = items.value[index];
    if (!item) {
        return;
    }

    const quantity = Math.max(1, sanitizeNumber(item.quantity));
    const unitPrice = sanitizeNumber(item.unit_price);

    item.quantity = quantity;
    item.total = Number((quantity * unitPrice).toFixed(2));
};

const ensureQuantity = (index) => {
    const item = items.value[index];
    if (!item) {
        return;
    }

    if (!item.quantity || sanitizeNumber(item.quantity) < 1) {
        item.quantity = 1;
    }

    updateItemTotal(index);
};

const onProductSelected = (index) => {
    const item = items.value[index];
    if (!item) {
        return;
    }

    const product = props.products.find((p) => p.id === item.product_id);
    if (!product) {
        item.unit_price = 0;
        item.total = 0;
        return;
    }

    item.unit_price = Number(product.price);
    if (!item.quantity || sanitizeNumber(item.quantity) < 1) {
        item.quantity = 1;
    }

    updateItemTotal(index);
};

const removeItem = (index) => {
    items.value.splice(index, 1);
};

const openProductModal = () => {
    searchTerm.value = '';
    selectedCategory.value = '';
    showProductModal.value = true;
};

const closeProductModal = () => {
    showProductModal.value = false;
};

const selectProduct = (product) => {
    items.value.push({
        product_id: product.id,
        quantity: 1,
        unit_price: Number(product.price) || 0,
        total: Number(product.price) || 0,
    });

    form.clearErrors('items');
    updateItemTotal(items.value.length - 1);
    closeProductModal();
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        items: items.value.map((item) => ({
            product_id: item.product_id,
            quantity: sanitizeNumber(item.quantity) || 1,
        })),
    })).post(route('parts.store'), {
        onSuccess: () => {
            form.reset();
            items.value = [];
        },
    });
};
</script>
