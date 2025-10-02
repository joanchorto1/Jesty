<script setup>
import { computed, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import CrudFilterBar from '@/Components/Crud/CrudFilterBar.vue';
import CrudTable from '@/Components/Crud/CrudTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import MenuBillingIcon from '@/Components/Icons/MenuBillingIcon.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import InfoIcon from '@/Components/Icons/InfoIcon.vue';

const props = defineProps({
    suppliers: {
        type: Array,
        default: () => [],
    },
});

const filters = reactive({
    search: '',
    contact: '',
});

const filteredSuppliers = computed(() => {
    const searchTerm = filters.search.trim().toLowerCase();

    return props.suppliers.filter((supplier) => {
        const matchesSearch =
            !searchTerm ||
            supplier.name?.toLowerCase().includes(searchTerm) ||
            supplier.email?.toLowerCase().includes(searchTerm);

        const matchesContact =
            !filters.contact ||
            (filters.contact === 'with_email' && Boolean(supplier.email)) ||
            (filters.contact === 'without_email' && !supplier.email);

        return matchesSearch && matchesContact;
    });
});

const totalSuppliers = computed(() => filteredSuppliers.value.length);
const suppliersWithEmail = computed(() => filteredSuppliers.value.filter((supplier) => Boolean(supplier.email)).length);
const suppliersWithoutPhone = computed(() => filteredSuppliers.value.filter((supplier) => !supplier.phone).length);

const clearFilters = () => {
    filters.search = '';
    filters.contact = '';
};

const deleteSupplier = (supplierId) => {
    if (confirm('Segur que vols eliminar aquest proveïdor?')) {
        Inertia.delete(route('suppliers.delete', supplierId));
    }
};
</script>

<template>
    <AppLayout title="Proveïdors">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Xarxa de proveïdors"
                    description="Mantén la relació comercial alineada amb la resta de departaments i detecta ràpidament punts de millora."
                    :icon="MenuBillingIcon"
                >
                    <template #actions>
                        <Link
                            :href="route('suppliers.create')"
                            class="inline-flex items-center gap-2 rounded-full bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200 transition hover:bg-sky-500"
                        >
                            <AddIcon class="h-5 w-5" />
                            Nou proveïdor
                        </Link>
                    </template>
                </CrudPageHeader>

                <div class="grid gap-6 md:grid-cols-3">
                    <CrudStatCard
                        label="Total proveïdors"
                        :value="totalSuppliers"
                        :icon="MenuBillingIcon"
                        icon-background="bg-indigo-500/10 text-indigo-600"
                    />
                    <CrudStatCard
                        label="Amb correu"
                        :value="suppliersWithEmail"
                        :icon="AddIcon"
                        icon-background="bg-emerald-500/10 text-emerald-600"
                    />
                    <CrudStatCard
                        label="Sense telèfon"
                        :value="suppliersWithoutPhone"
                        :icon="InfoIcon"
                        icon-background="bg-amber-500/10 text-amber-600"
                    />
                </div>

                <CrudFilterBar>
                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="supplier-search" value="Cerca" />
                        <TextInput
                            id="supplier-search"
                            v-model="filters.search"
                            type="search"
                            class="block w-full"
                            placeholder="Nom o correu"
                        />
                    </div>

                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="supplier-contact" value="Disponibilitat de contacte" />
                        <SelectInput id="supplier-contact" v-model="filters.contact" class="block w-full">
                            <option value="">Tots</option>
                            <option value="with_email">Amb correu</option>
                            <option value="without_email">Sense correu</option>
                        </SelectInput>
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
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nom</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Correu</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Telèfon</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Accions</th>
                        </tr>
                    </template>

                    <tr v-for="supplier in filteredSuppliers" :key="supplier.id" class="hover:bg-slate-50/60">
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ supplier.name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ supplier.email || '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ supplier.phone || '—' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="route('suppliers.show', supplier.id)"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-500/10 text-sky-600 transition hover:bg-sky-500/20"
                                >
                                    <InfoIcon class="h-5 w-5" />
                                </Link>
                                <Link
                                    :href="route('suppliers.edit', supplier.id)"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-amber-500/10 text-amber-600 transition hover:bg-amber-500/20"
                                >
                                    <EditIcon class="h-5 w-5" />
                                </Link>
                                <button
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-500/10 text-rose-600 transition hover:bg-rose-500/20"
                                    @click="deleteSupplier(supplier.id)"
                                >
                                    <DeleteIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </td>
                    </tr>

                    <template v-if="!filteredSuppliers.length">
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-500">
                                Cap proveïdor compleix els filtres actuals.
                            </td>
                        </tr>
                    </template>
                </CrudTable>
            </div>
        </div>
    </AppLayout>
</template>
