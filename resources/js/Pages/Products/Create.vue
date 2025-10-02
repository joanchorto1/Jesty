<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
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
    name: '',
    category_id: '',
    supplier_id: '',
    description: '',
    price: '',
    cost_price: '',
    stock: '',
    is_stackable: false,
});

const submit = () => {
    form.post(route('products.store'), {
        onSuccess: () => {
            form.reset();
            Inertia.visit(route('dashboard.products'));
        },
    });
};

const isInventoryManaged = computed(() => Boolean(form.is_stackable));
</script>

<template>
    <AppLayout title="Nou producte">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-6xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Nou producte"
                    description="Defineix els atributs comercials i logístics perquè el producte estigui disponible a tots els canals."
                    :icon="MenuProductIcon"
                />

                <div class="grid gap-6 md:grid-cols-3">
                    <CrudStatCard
                        label="Categories disponibles"
                        :value="categories.length"
                        :icon="MenuProductIcon"
                        icon-background="bg-indigo-500/10 text-indigo-600"
                    />
                    <CrudStatCard
                        label="Proveïdors actius"
                        :value="suppliers.length"
                        :icon="AddProductIcon"
                        icon-background="bg-sky-500/10 text-sky-600"
                    >
                        <template #description>
                            <p class="text-xs text-slate-500">Selecciona un proveïdor per agilitzar les comandes.</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard
                        label="Gestió d'estoc"
                        :value="isInventoryManaged ? 'Activada' : 'Opcional'"
                        :icon="MenuProductIcon"
                        icon-background="bg-emerald-500/10 text-emerald-600"
                    >
                        <template #description>
                            <p class="text-xs text-slate-500">Marca l'opció «Controlar estoc» si cal fer seguiment.</p>
                        </template>
                    </CrudStatCard>
                </div>

                <FormSection @submitted="submit">
                    <template #title>
                        Fitxa del producte
                    </template>
                    <template #description>
                        Completa la informació comercial i d'inventari per sincronitzar el catàleg.
                    </template>

                    <template #form>
                        <div class="col-span-6 space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Informació general</h3>
                                <p class="mt-1 text-sm text-slate-500">Nom, categorització i descripció visibles a tots els portals.</p>
                            </div>
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="name" value="Nom" />
                                    <TextInput id="name" v-model="form.name" type="text" class="mt-2 block w-full" autocomplete="off" />
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
                                    <TextareaInput
                                        id="description"
                                        v-model="form.description"
                                        rows="4"
                                        class="mt-2 block w-full"
                                        placeholder="Explica els beneficis i el posicionament del producte"
                                    />
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
                                    <p class="mt-1 text-sm text-slate-500">Controla marges i disponibilitat al magatzem.</p>
                                </div>
                                <label class="flex items-center gap-3 text-sm font-medium text-slate-600">
                                    <Checkbox v-model:checked="form.is_stackable" />
                                    <span>Controlar estoc manualment</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="cost_price" value="Cost" />
                                    <TextInput
                                        id="cost_price"
                                        v-model="form.cost_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="mt-2 block w-full"
                                    />
                                    <InputError :message="form.errors.cost_price" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="price" value="Preu de venda" />
                                    <TextInput
                                        id="price"
                                        v-model="form.price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="mt-2 block w-full"
                                    />
                                    <InputError :message="form.errors.price" class="mt-2" />
                                </div>

                                <div v-if="isInventoryManaged" class="col-span-6 sm:col-span-3">
                                    <InputLabel for="stock" value="Unitats disponibles" />
                                    <TextInput
                                        id="stock"
                                        v-model="form.stock"
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="mt-2 block w-full"
                                    />
                                    <InputError :message="form.errors.stock" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </template>

                    <template #actions>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Desar producte
                        </PrimaryButton>
                    </template>
                </FormSection>
            </div>
        </div>
    </AppLayout>
</template>
