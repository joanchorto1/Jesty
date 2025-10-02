<script setup>
import { computed, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import MenuProductIcon from '@/Components/Icons/MenuProductIcon.vue';
import AddProductIcon from '@/Components/Icons/AddProductIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import MenuExpenseIcon from '@/Components/Icons/MenuExpenseIcon.vue';
import InventoryFilterBar from '@/Components/Inventory/InventoryFilterBar.vue';
import InventoryResponsiveTable from '@/Components/Inventory/InventoryResponsiveTable.vue';
import InventoryStatusBadge from '@/Components/Inventory/InventoryStatusBadge.vue';
import InventorySummaryCard from '@/Components/Inventory/InventorySummaryCard.vue';
import { inventoryPalette, inventoryTypography } from '@/Components/Inventory/inventoryTheme';

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const filters = reactive({
    search: '',
    category_id: '',
    status: '',
});

const filteredProducts = computed(() => {
    const searchTerm = filters.search.trim().toLowerCase();

    return props.products.filter((product) => {
        const matchesSearch =
            !searchTerm ||
            product.name?.toLowerCase().includes(searchTerm) ||
            product.description?.toLowerCase().includes(searchTerm);
        const matchesCategory = !filters.category_id || Number(filters.category_id) === Number(product.category_id);
        const matchesStatus =
            !filters.status ||
            (filters.status === 'in_stock' ? Number(product.stock) > 0 : Number(product.stock) === 0);

        return matchesSearch && matchesCategory && matchesStatus;
    });
});

const totalProducts = computed(() => filteredProducts.value.length);
const inStock = computed(() => filteredProducts.value.filter((product) => Number(product.stock) > 0).length);
const outOfStock = computed(() => filteredProducts.value.filter((product) => Number(product.stock) === 0).length);

const clearFilters = () => {
    filters.search = '';
    filters.category_id = '';
    filters.status = '';
};

const deleteProduct = (id) => {
    if (confirm('Segur que vols eliminar aquest producte?')) {
        Inertia.delete(route('products.destroy', id));
    }
};

const downloadLabel = (labelPath) => {
    if (labelPath) {
        window.open(`/storage/${labelPath}`, '_blank');
    }
};

const categoryName = (categoryId) =>
    props.categories.find((category) => Number(category.id) === Number(categoryId))?.name ?? '—';

const palette = inventoryPalette;
const typography = inventoryTypography;

const stockStatus = (product) => (Number(product.stock) > 0 ? 'in_stock' : 'out_of_stock');
</script>

<template>
    <AppLayout title="Catàleg de productes">
        <div :class="['min-h-screen pb-16', palette.background]">
            <div :class="['bg-gradient-to-r', palette.gradient, 'pb-24']">
                <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6 pt-10">
                    <div class="flex flex-col gap-6 text-white md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-white/10">
                                <MenuProductIcon class="h-8 w-8 text-white" />
                            </div>
                            <div class="space-y-2">
                                <p :class="typography.heroKicker">Catàleg de productes</p>
                                <h1 :class="typography.heroTitle">Control integral del portfoli</h1>
                                <p :class="typography.heroSubtitle">
                                    Consulta, filtra i gestiona els productes disponibles per a la venda i el control d'estoc.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <Link
                                :href="route('products.create')"
                                class="inline-flex items-center gap-2 rounded-full bg-white/10 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-900/10 transition hover:bg-white/20"
                            >
                                <AddProductIcon class="h-5 w-5" />
                                Nou producte
                            </Link>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-3">
                        <InventorySummaryCard label="Total de productes" :value="totalProducts">
                            <template #icon>
                                <MenuProductIcon class="h-6 w-6" />
                            </template>
                        </InventorySummaryCard>
                        <InventorySummaryCard label="En estoc" :value="inStock">
                            <template #icon>
                                <AddProductIcon class="h-6 w-6" />
                            </template>
                            <template #description>
                                Disponibles immediatament
                            </template>
                        </InventorySummaryCard>
                        <InventorySummaryCard label="Sense estoc" :value="outOfStock" description="Revisa les reposicions pendents">
                            <template #icon>
                                <MenuExpenseIcon class="h-6 w-6" />
                            </template>
                        </InventorySummaryCard>
                    </div>
                </div>
            </div>

            <div class="mx-auto flex max-w-7xl flex-col gap-8 px-6 -mt-16">
                <InventoryFilterBar>
                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="search" value="Cerca" />
                        <TextInput
                            id="search"
                            v-model="filters.search"
                            type="search"
                            class="block w-full"
                            placeholder="Nom o descripció"
                        />
                    </div>

                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="category" value="Categoria" />
                        <SelectInput id="category" v-model="filters.category_id" class="block w-full">
                            <option value="">Totes</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </SelectInput>
                    </div>

                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="status" value="Disponibilitat" />
                        <SelectInput id="status" v-model="filters.status" class="block w-full">
                            <option value="">Tots</option>
                            <option value="in_stock">En estoc</option>
                            <option value="out_of_stock">Sense estoc</option>
                        </SelectInput>
                    </div>

                    <template #actions>
                        <SecondaryButton type="button" @click="clearFilters">
                            Netejar filtres
                        </SecondaryButton>
                    </template>
                </InventoryFilterBar>

                <InventoryResponsiveTable :is-empty="!filteredProducts.length">
                    <template #head>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nom</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Categoria</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Preu (€)</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Estoc</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Accions</th>
                        </tr>
                    </template>

                    <tr v-for="product in filteredProducts" :key="product.id" class="hover:bg-slate-50/60">
                        <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ product.name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ categoryName(product.category_id) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ Number(product.price).toFixed(2) }}</td>
                        <td class="px-6 py-4 text-sm font-semibold">
                            <InventoryStatusBadge :status="stockStatus(product)">
                                {{ Number(product.stock ?? 0) }}
                            </InventoryStatusBadge>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="route('products.edit', product.id)"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-amber-500/10 text-amber-600 transition hover:bg-amber-500/20"
                                >
                                    <EditIcon class="h-5 w-5" />
                                </Link>
                                <button
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-500/10 text-sky-600 transition hover:bg-sky-500/20"
                                    @click="downloadLabel(product.label_path)"
                                    :title="product.label_path ? 'Descarregar etiqueta' : 'Sense etiqueta disponible'"
                                >
                                    <MenuExpenseIcon class="h-5 w-5" />
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-500/10 text-rose-600 transition hover:bg-rose-500/20"
                                    @click="deleteProduct(product.id)"
                                >
                                    <DeleteIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </td>
                    </tr>

                    <template #empty>
                        Cap resultat coincideix amb els filtres actuals.
                    </template>
                </InventoryResponsiveTable>
            </div>
        </div>
    </AppLayout>
</template>
