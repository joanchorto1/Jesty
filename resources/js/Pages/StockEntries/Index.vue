<script setup>
import { computed, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InventoryFilterBar from '@/Components/Inventory/InventoryFilterBar.vue';
import InventoryResponsiveTable from '@/Components/Inventory/InventoryResponsiveTable.vue';
import InventoryStatusBadge from '@/Components/Inventory/InventoryStatusBadge.vue';
import InventorySummaryCard from '@/Components/Inventory/InventorySummaryCard.vue';
import MenuExpenseIcon from '@/Components/Icons/MenuExpenseIcon.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import InfoIcon from '@/Components/Icons/InfoIcon.vue';
import { inventoryPalette, inventoryTypography } from '@/Components/Inventory/inventoryTheme';

const props = defineProps({
    stockEntries: Array,
    suppliers: Array,
});

const selectedSupplier = ref('');
const selectedStatus = ref('');
const startDate = ref('');
const endDate = ref('');

const filteredStockEntries = computed(() =>
    props.stockEntries.filter((entry) => {
        const supplierMatch = selectedSupplier.value === '' || Number(entry.supplier_id) === Number(selectedSupplier.value);
        const statusMatch = selectedStatus.value === '' || entry.status === selectedStatus.value;

        const entryDate = new Date(entry.entry_date);
        const startDateMatch = !startDate.value || entryDate >= new Date(startDate.value);
        const endDateMatch = !endDate.value || entryDate <= new Date(endDate.value);

        return supplierMatch && statusMatch && startDateMatch && endDateMatch;
    }),
);

const totalEntries = computed(() => filteredStockEntries.value.length);
const pendingEntries = computed(() => filteredStockEntries.value.filter((entry) => entry.status === 'pending').length);
const completedEntries = computed(() => filteredStockEntries.value.filter((entry) => entry.status === 'completed').length);

const clearFilters = () => {
    selectedSupplier.value = '';
    selectedStatus.value = '';
    startDate.value = '';
    endDate.value = '';
};

const deleteStockEntry = (id) => {
    if (confirm('¿Estàs segur que vols eliminar aquesta entrada de stock?')) {
        Inertia.delete(route('stockEntries.destroy', id));
    }
};

const editStockEntry = (id) => {
    Inertia.visit(route('stockEntries.goToEdit', id));
};

const showStockEntryDetails = (id) => {
    Inertia.visit(route('stockEntries.goToShow', id));
};

const supplierName = (supplierId) =>
    props.suppliers.find((supplier) => Number(supplier.id) === Number(supplierId))?.name ?? '—';

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
                                <MenuExpenseIcon class="h-8 w-8 text-white" />
                            </div>
                            <div class="space-y-2">
                                <p :class="typography.heroKicker">Entrades d'estoc</p>
                                <h1 :class="typography.heroTitle">Flux de reposicions coordinat</h1>
                                <p :class="typography.heroSubtitle">
                                    Supervisa els moviments d'entrada, assigna prioritats i planifica els lliuraments amb informació unificada.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3">
                            <Link
                                :href="route('stockEntries.create')"
                                class="inline-flex items-center gap-2 rounded-full bg-white/10 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-900/10 transition hover:bg-white/20"
                            >
                                <AddIcon class="h-5 w-5" />
                                Nova entrada
                            </Link>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-3">
                        <InventorySummaryCard label="Total entrades" :value="totalEntries">
                            <template #icon>
                                <MenuExpenseIcon class="h-6 w-6" />
                            </template>
                        </InventorySummaryCard>
                        <InventorySummaryCard label="Pendents" :value="pendingEntries" description="Esperant recepció">
                            <template #icon>
                                <InfoIcon class="h-6 w-6" />
                            </template>
                        </InventorySummaryCard>
                        <InventorySummaryCard label="Completades" :value="completedEntries" description="Disponible al magatzem">
                            <template #icon>
                                <AddIcon class="h-6 w-6" />
                            </template>
                        </InventorySummaryCard>
                    </div>
                </div>
            </div>

            <div class="mx-auto flex max-w-7xl flex-col gap-8 px-6 -mt-16">
                <InventoryFilterBar :columns="4">
                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="supplierFilter" value="Proveïdor" />
                        <SelectInput id="supplierFilter" v-model="selectedSupplier" class="block w-full">
                            <option value="">Tots</option>
                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                        </SelectInput>
                    </div>

                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="statusFilter" value="Estat" />
                        <SelectInput id="statusFilter" v-model="selectedStatus" class="block w-full">
                            <option value="">Tots</option>
                            <option value="pending">Pendent</option>
                            <option value="completed">Completat</option>
                        </SelectInput>
                    </div>

                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="startDateFilter" value="Data d'inici" />
                        <TextInput id="startDateFilter" v-model="startDate" type="date" class="block w-full" />
                    </div>

                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="endDateFilter" value="Data de finalització" />
                        <TextInput id="endDateFilter" v-model="endDate" type="date" class="block w-full" />
                    </div>

                    <template #actions>
                        <SecondaryButton type="button" @click="clearFilters">
                            Netejar filtres
                        </SecondaryButton>
                    </template>
                </InventoryFilterBar>

                <InventoryResponsiveTable :is-empty="!filteredStockEntries.length">
                    <template #head>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Referència</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Proveïdor</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Data d'entrada</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Estat</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Accions</th>
                        </tr>
                    </template>

                    <tr v-for="entry in filteredStockEntries" :key="entry.id" class="hover:bg-slate-50/60">
                        <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ entry.reference }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ supplierName(entry.supplier_id) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ entry.entry_date }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ entry.total }}</td>
                        <td class="px-6 py-4 text-sm">
                            <InventoryStatusBadge :status="entry.status" />
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-500/10 text-rose-600 transition hover:bg-rose-500/20"
                                    @click="deleteStockEntry(entry.id)"
                                    title="Eliminar"
                                >
                                    <DeleteIcon class="h-5 w-5" />
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-amber-500/10 text-amber-600 transition hover:bg-amber-500/20"
                                    @click="editStockEntry(entry.id)"
                                    title="Editar"
                                >
                                    <EditIcon class="h-5 w-5" />
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-900/20 text-white transition hover:bg-slate-900/40"
                                    @click="showStockEntryDetails(entry.id)"
                                    title="Detalls"
                                >
                                    <InfoIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </td>
                    </tr>

                    <template #empty>
                        No hi ha entrades que coincideixin amb els filtres aplicats.
                    </template>
                </InventoryResponsiveTable>
            </div>
        </div>
    </AppLayout>
</template>
