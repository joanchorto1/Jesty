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
                                <InputLabel for="part-reference" value="Referència" />
                                <TextInput
                                    id="part-reference"
                                    v-model="form.reference"
                                    type="text"
                                    class="mt-2 block w-full"
                                    placeholder="Parte instal·lació març"
                                />
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
                                @click="addItem"
                                class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                            >
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

const props = defineProps({
    clients: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
});

const form = useForm({
    reference: '',
    date: new Date().toISOString().split('T')[0],
    client_id: '',
    notes: '',
});

const items = ref([
    { product_id: '', quantity: 1, unit_price: 0, total: 0 },
]);

const formatCurrency = (value) =>
    new Intl.NumberFormat('ca-ES', {
        style: 'currency',
        currency: 'EUR',
    }).format(Number(value) || 0);

const partTotal = computed(() => items.value.reduce((total, item) => total + Number(item.total || 0), 0));

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

const addItem = () => {
    items.value.push({ product_id: '', quantity: 1, unit_price: 0, total: 0 });
};

const removeItem = (index) => {
    items.value.splice(index, 1);
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
            items.value = [{ product_id: '', quantity: 1, unit_price: 0, total: 0 }];
        },
    });
};
</script>
