<script setup>
import { computed, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudFilterBar from '@/Components/Crud/CrudFilterBar.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import CrudTable from '@/Components/Crud/CrudTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import MenuProductIcon from '@/Components/Icons/MenuProductIcon.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import InfoIcon from '@/Components/Icons/InfoIcon.vue';

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

const descriptionBadgeClass = (category) =>
    category.description
        ? 'inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700'
        : 'inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500';
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Gestió de categories"
                    description="Organitza les línies de producte per facilitar la cerca i l'anàlisi de vendes."
                    :icon="MenuProductIcon"
                >
                    <template #actions>
                        <Link
                            :href="route('categories.create')"
                            class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                        >
                            <AddIcon class="h-5 w-5" />
                            Nova categoria
                        </Link>
                    </template>
                </CrudPageHeader>

                <div class="grid gap-6 md:grid-cols-3">
                    <CrudStatCard label="Total categories" :value="totalCategories" :icon="MenuProductIcon" />
                    <CrudStatCard label="Amb descripció" :value="withDescription" :icon="InfoIcon" icon-background="bg-emerald-500/10 text-emerald-600" />
                    <CrudStatCard label="Sense descripció" :value="withoutDescription" :icon="DeleteIcon" icon-background="bg-amber-500/10 text-amber-600">
                        <template #description>
                            <p class="text-xs text-slate-500">Completa la fitxa per millorar el catàleg</p>
                        </template>
                    </CrudStatCard>
                </div>

                <CrudFilterBar>
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
                </CrudFilterBar>

                <CrudTable>
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
                            <span :class="descriptionBadgeClass(category)">
                                {{ category.description ? 'Informació completa' : 'Pendents detalls' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="route('categories.edit', category.id)"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-amber-200 text-amber-600 transition hover:bg-amber-50"
                                >
                                    <EditIcon class="h-5 w-5" />
                                </Link>
                                <button
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-rose-200 text-rose-600 transition hover:bg-rose-50"
                                    @click="deleteCategory(category.id)"
                                >
                                    <DeleteIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="!filteredCategories.length">
                        <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-400">
                            Cap categoria coincideix amb la cerca actual.
                        </td>
                    </tr>
                </CrudTable>
            </div>
        </div>
    </AppLayout>
</template>
