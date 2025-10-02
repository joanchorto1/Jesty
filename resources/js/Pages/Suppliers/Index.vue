<script setup>
import { computed, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import MenuBillingIcon from '@/Components/Icons/MenuBillingIcon.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import InfoIcon from '@/Components/Icons/InfoIcon.vue';
import InventoryFilterBar from '@/Components/Inventory/InventoryFilterBar.vue';
import InventoryResponsiveTable from '@/Components/Inventory/InventoryResponsiveTable.vue';
import InventoryStatusBadge from '@/Components/Inventory/InventoryStatusBadge.vue';
import InventorySummaryCard from '@/Components/Inventory/InventorySummaryCard.vue';
import { inventoryPalette, inventoryTypography } from '@/Components/Inventory/inventoryTheme';

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

const palette = inventoryPalette;
const typography = inventoryTypography;
</script>

<template>
    <AppLayout title="Proveïdors">
        <div :class="['min-h-screen pb-16', palette.background]">
            <div :class="['bg-gradient-to-r', palette.gradient, 'pb-24']">
                <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6 pt-10">
                    <div class="flex flex-col gap-6 text-white md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-white/10">
                                <MenuBillingIcon class="h-8 w-8 text-white" />
                            </div>
                            <div class="space-y-2">
                                <p :class="typography.heroKicker">Xarxa de proveïdors</p>
                                <h1 :class="typography.heroTitle">Sincronitza relacions clau</h1>
                                <p :class="typography.heroSubtitle">
                                    Mantén la relació comercial alineada amb la resta de departaments i detecta ràpidament punts de millora.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <Link
                                :href="route('suppliers.create')"
                                class="inline-flex items-center gap-2 rounded-full bg-white/10 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-900/10 transition hover:bg-white/20"
                            >
                                <AddIcon class="h-5 w-5" />
                                Nou proveïdor
                            </Link>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-3">
                        <InventorySummaryCard label="Total proveïdors" :value="totalSuppliers">
                            <template #icon>
                                <MenuBillingIcon class="h-6 w-6" />
                            </template>
                        </InventorySummaryCard>
                        <InventorySummaryCard label="Amb correu" :value="suppliersWithEmail" description="Contacte immediat">
                            <template #icon>
                                <AddIcon class="h-6 w-6" />
                            </template>
                        </InventorySummaryCard>
                        <InventorySummaryCard label="Sense telèfon" :value="suppliersWithoutPhone" description="Requereix seguiment">
                            <template #icon>
                                <InfoIcon class="h-6 w-6" />
                            </template>
                        </InventorySummaryCard>
                    </div>
                </div>
            </div>

            <div class="mx-auto flex max-w-7xl flex-col gap-8 px-6 -mt-16">
                <InventoryFilterBar :columns="2">
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
                </InventoryFilterBar>

                <InventoryResponsiveTable :is-empty="!filteredSuppliers.length">
                    <template #head>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nom</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Correu</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Telèfon</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Contacte</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Accions</th>
                        </tr>
                    </template>

                    <tr v-for="supplier in filteredSuppliers" :key="supplier.id" class="hover:bg-slate-50/60">
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ supplier.name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ supplier.email || '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ supplier.phone || '—' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <InventoryStatusBadge :status="supplier.email ? 'active' : 'inactive'">
                                {{ supplier.email ? 'Contacte directe' : 'Sense correu' }}
                            </InventoryStatusBadge>
                        </td>
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

                    <template #empty>
                        Cap proveïdor compleix els filtres actuals.
                    </template>
                </InventoryResponsiveTable>
            </div>
        </div>
    </AppLayout>
</template>
