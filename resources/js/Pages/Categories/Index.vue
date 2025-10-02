<script setup>
import { computed, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InventoryFilterBar from '@/Components/Inventory/InventoryFilterBar.vue';
import InventoryResponsiveTable from '@/Components/Inventory/InventoryResponsiveTable.vue';
import InventoryStatusBadge from '@/Components/Inventory/InventoryStatusBadge.vue';
import InventorySummaryCard from '@/Components/Inventory/InventorySummaryCard.vue';
import MenuProductIcon from '@/Components/Icons/MenuProductIcon.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import InfoIcon from '@/Components/Icons/InfoIcon.vue';
import { inventoryPalette, inventoryTypography } from '@/Components/Inventory/inventoryTheme';

const props = defineProps({
    categories: Array,
});

const filters = reactive({
    search: '',
});

const filteredCategories = computed(() => {
    const searchTerm = filters.search.trim().toLowerCase();

    return props.categories.filter((category) =>
        !searchTerm ||
        category.name?.toLowerCase().includes(searchTerm) ||
        category.description?.toLowerCase().includes(searchTerm),
    );
});

const totalCategories = computed(() => filteredCategories.value.length);
const withDescription = computed(() => filteredCategories.value.filter((category) => Boolean(category.description)).length);
const withoutDescription = computed(() => totalCategories.value - withDescription.value);

const clearFilters = () => {
    filters.search = '';
};

const deleteCategory = (id) => {
    if (confirm('¿Estàs segur que vols eliminar aquesta categoria?')) {
        Inertia.delete(route('categories.destroy', id));
    }
};

const palette = inventoryPalette;
const typography = inventoryTypography;
</script>

<template>
    <AppLayout>
        <div :class="['min-h-screen pb-16', palette.background]">
            <div :class="['bg-gradient-to-r', palette.gradient, 'pb-24']">
                <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6 pt-10">
                    <div class="flex flex-col gap-6 text-white md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-white/10">
                                <MenuProductIcon class="h-8 w-8 text-white" />
                            </div>
                            <div class="space-y-2">
                                <p :class="typography.heroKicker">Gestió de categories</p>
                                <h1 :class="typography.heroTitle">Classifica el catàleg amb coherència</h1>
                                <p :class="typography.heroSubtitle">
                                    Organitza les línies de producte per facilitar la cerca i l'anàlisi de vendes.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <Link
                                :href="route('categories.create')"
                                class="inline-flex items-center gap-2 rounded-full bg-white/10 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-900/10 transition hover:bg-white/20"
                            >
                                <AddIcon class="h-5 w-5" />
                                Nova categoria
                            </Link>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-3">
                        <InventorySummaryCard label="Total categories" :value="totalCategories">
                            <template #icon>
                                <MenuProductIcon class="h-6 w-6" />
                            </template>
                        </InventorySummaryCard>
                        <InventorySummaryCard label="Amb descripció" :value="withDescription">
                            <template #icon>
                                <InfoIcon class="h-6 w-6" />
                            </template>
                        </InventorySummaryCard>
                        <InventorySummaryCard label="Sense descripció" :value="withoutDescription" description="Completa la fitxa per millorar el catàleg">
                            <template #icon>
                                <DeleteIcon class="h-6 w-6" />
                            </template>
                        </InventorySummaryCard>
                    </div>
                </div>
            </div>

            <div class="mx-auto flex max-w-7xl flex-col gap-8 px-6 -mt-16">
                <InventoryFilterBar :columns="1">
                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="category-search" value="Cerca" />
                        <TextInput
                            id="category-search"
                            v-model="filters.search"
                            type="search"
                            class="block w-full"
                            placeholder="Nom o descripció"
                        />
                    </div>

                    <template #actions>
                        <SecondaryButton type="button" @click="clearFilters">
                            Netejar filtres
                        </SecondaryButton>
                    </template>
                </InventoryFilterBar>

                <InventoryResponsiveTable :is-empty="!filteredCategories.length">
                    <template #head>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nom</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Descripció</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Completat</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Accions</th>
                        </tr>
                    </template>

                    <tr v-for="category in filteredCategories" :key="category.id" class="hover:bg-slate-50/60">
                        <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ category.name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ category.description || '—' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <InventoryStatusBadge :status="category.description ? 'active' : 'inactive'">
                                {{ category.description ? 'Informació completa' : 'Pendents detalls' }}
                            </InventoryStatusBadge>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="route('categories.edit', category.id)"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-amber-500/10 text-amber-600 transition hover:bg-amber-500/20"
                                >
                                    <EditIcon class="h-5 w-5" />
                                </Link>
                                <button
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-500/10 text-rose-600 transition hover:bg-rose-500/20"
                                    @click="deleteCategory(category.id)"
                                >
                                    <DeleteIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </td>
                    </tr>

                    <template #empty>
                        Cap categoria coincideix amb la cerca actual.
                    </template>
                </InventoryResponsiveTable>
            </div>
        </div>
    </AppLayout>
</template>
