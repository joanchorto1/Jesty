<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import MenuProductIcon from '@/Components/Icons/MenuProductIcon.vue';
import AddProductIcon from '@/Components/Icons/AddProductIcon.vue';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    suppliers: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: props.product.name ?? '',
    category_id: props.product.category_id ?? '',
    supplier_id: props.product.supplier_id ?? '',
    description: props.product.description ?? '',
    price: props.product.price ?? '',
    cost_price: props.product.cost_price ?? '',
    stock: props.product.stock ?? '',
    is_stackable: Boolean(props.product.is_stackable),
});

const submit = () => {
    form.put(route('products.update', props.product.id));
};

const isInventoryManaged = computed(() => Boolean(form.is_stackable));
const formattedPrice = computed(() => (form.price ? Number(form.price).toFixed(2) : '—'));
const formattedStock = computed(() => (isInventoryManaged.value ? form.stock || 0 : 'No controlat'));
</script>

<template>
    <AppLayout title="Edita producte">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-6xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Actualitza el producte"
                    description="Revisa els detalls comercials, ajusta marges i sincronitza l'inventari amb tota la companyia."
                    :icon="MenuProductIcon"
                >
                    <template #actions>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submit">
                            Actualitzar
                        </PrimaryButton>
                    </template>
                </CrudPageHeader>

                <div class="grid gap-6 md:grid-cols-3">
                    <CrudStatCard
                        label="Categoria assignada"
                        :value="categories.find((category) => category.id === form.category_id)?.name ?? 'Sense categoria'"
                        :icon="MenuProductIcon"
                        icon-background="bg-indigo-500/10 text-indigo-600"
                    />
                    <CrudStatCard
                        label="Preu actual"
                        :value="formattedPrice"
                        :icon="AddProductIcon"
                        icon-background="bg-amber-500/10 text-amber-600"
                    />
                    <CrudStatCard
                        label="Unitats disponibles"
                        :value="formattedStock"
                        :icon="MenuProductIcon"
                        icon-background="bg-emerald-500/10 text-emerald-600"
                    />
                </div>

                <FormSection @submitted="submit">
                    <template #title>
                        Dades del producte
                    </template>
                    <template #description>
                        Mantén actualitzada la informació essencial perquè tots els equips treballin amb la mateixa versió.
                    </template>

                    <template #form>
                        <div class="col-span-6 space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Informació comercial</h3>
                                <p class="mt-1 text-sm text-slate-500">Nom, categoria, proveïdor i descripció pública.</p>
                            </div>
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="name" value="Nom" />
                                    <TextInput id="name" v-model="form.name" type="text" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="category" value="Categoria" />
                                    <SelectInput id="category" v-model="form.category_id" class="mt-2 block w-full">
                                        <option value="">Selecciona una categoria</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError :message="form.errors.category_id" class="mt-2" />
                                </div>

                                <div class="col-span-6">
                                    <InputLabel for="description" value="Descripció" />
                                    <TextareaInput id="description" v-model="form.description" rows="4" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.description" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="supplier" value="Proveïdor" />
                                    <SelectInput id="supplier" v-model="form.supplier_id" class="mt-2 block w-full">
                                        <option value="">Selecciona un proveïdor</option>
                                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                            {{ supplier.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError :message="form.errors.supplier_id" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="col-span-6 space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                            <div class="flex items-start justify-between gap-6">
                                <div>
                                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Preus i inventari</h3>
                                    <p class="mt-1 text-sm text-slate-500">Actualitza el marge i la disponibilitat d'estoc.</p>
                                </div>
                                <label class="flex items-center gap-3 text-sm font-medium text-slate-600">
                                    <Checkbox v-model:checked="form.is_stackable" />
                                    <span>Controlar estoc manualment</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="cost_price" value="Cost" />
                                    <TextInput id="cost_price" v-model="form.cost_price" type="number" min="0" step="0.01" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.cost_price" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="price" value="Preu de venda" />
                                    <TextInput id="price" v-model="form.price" type="number" min="0" step="0.01" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.price" class="mt-2" />
                                </div>

                                <div v-if="isInventoryManaged" class="col-span-6 sm:col-span-3">
                                    <InputLabel for="stock" value="Unitats disponibles" />
                                    <TextInput id="stock" v-model="form.stock" type="number" min="0" step="1" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.stock" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </template>

                    <template #actions>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Guardar canvis
                        </PrimaryButton>
                    </template>
                </FormSection>
            </div>
        </div>
    </AppLayout>
</template>
