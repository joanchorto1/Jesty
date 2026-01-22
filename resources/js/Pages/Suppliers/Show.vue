<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-6xl flex-col gap-10 px-6">
                <CrudPageHeader
                    :title="supplier.name"
                    description="Consulta la información principal del proveedor y las entradas de stock asociadas."
                >
                    <template #actions>
                        <NavLink :href="route('stockEntries.create', supplier.id)" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                            <AddIcon class="w-5 h-5"/>
                            Nueva entrada de stock
                        </NavLink>
                    </template>
                </CrudPageHeader>

                <Panel title="Detalles del proveedor" description="Información de contacto y datos administrativos del proveedor.">
                    <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 px-5 py-4">
                            <dt class="text-xs uppercase tracking-widest text-slate-400">Nombre</dt>
                            <dd class="mt-2 text-sm font-semibold text-slate-700">{{ supplier.name }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-5 py-4">
                            <dt class="text-xs uppercase tracking-widest text-slate-400">Email</dt>
                            <dd class="mt-2 text-sm font-semibold text-slate-700">{{ supplier.email || 'N/A' }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-5 py-4">
                            <dt class="text-xs uppercase tracking-widest text-slate-400">Teléfono</dt>
                            <dd class="mt-2 text-sm font-semibold text-slate-700">{{ supplier.phone || 'N/A' }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-5 py-4">
                            <dt class="text-xs uppercase tracking-widest text-slate-400">Dirección</dt>
                            <dd class="mt-2 text-sm font-semibold text-slate-700">{{ supplier.address || 'N/A' }}</dd>
                        </div>
                    </dl>
                </Panel>

                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Entradas de stock</h2>
                        <p class="text-sm text-slate-500">Filtra y consulta las entradas registradas para este proveedor.</p>
                    </div>
                    <CrudFilterBar>
                        <div class="flex flex-wrap gap-4">
                            <div>
                                <label for="entryStartDate" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">Fecha de inicio</label>
                                <input type="date" v-model="entryStartDate" id="entryStartDate" class="mt-2 block w-full rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-slate-500 focus:ring focus:ring-slate-200/60" />
                            </div>

                            <div>
                                <label for="entryEndDate" class="block text-xs font-semibold uppercase tracking-widest text-slate-400">Fecha de finalización</label>
                                <input type="date" v-model="entryEndDate" id="entryEndDate" class="mt-2 block w-full rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-slate-500 focus:ring focus:ring-slate-200/60" />
                            </div>
                        </div>
                        <template #actions>
                            <button class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50" @click="deleteFilters()">
                                Limpiar filtros
                            </button>
                        </template>
                    </CrudFilterBar>

                    <CrudTable>
                        <template #head>
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Referencia</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Fecha de entrada</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Acciones</th>
                            </tr>
                        </template>
                        <tr v-for="entry in filteredStockEntries" :key="entry.id" class="hover:bg-slate-50/60">
                            <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ entry.reference }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ entry.entry_date }}</td>
                            <td class="px-6 py-4 text-sm">
                                <NavLink :href="route('stockEntries.show', entry.id)" class="text-slate-500 hover:text-slate-700">
                                    <InfoIcon class="w-5 h-5" />
                                </NavLink>
                            </td>
                        </tr>
                        <tr v-if="!filteredStockEntries.length">
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-slate-400">
                                No hay entradas de stock para los filtros seleccionados.
                            </td>
                        </tr>
                    </CrudTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudFilterBar from '@/Components/Crud/CrudFilterBar.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudTable from '@/Components/Crud/CrudTable.vue';
import Panel from '@/Components/UI/Panel.vue';
import NavLink from "@/Components/NavLink.vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";

const props = defineProps({
    supplier: Object,
    stockEntries: Array,
});

const entryStartDate = ref('');
const entryEndDate = ref('');

// Filtrar entradas de stock
const filteredStockEntries = computed(() => {
    return props.stockEntries.filter(entry => {
        const startDateMatches = !entryStartDate.value || new Date(entry.entry_date) >= new Date(entryStartDate.value);
        const endDateMatches = !entryEndDate.value || new Date(entry.entry_date) <= new Date(entryEndDate.value);
        return startDateMatches && endDateMatches;
    });
});

const deleteFilters = () => {
    entryEndDate.value = '';
    entryStartDate.value = '';
};
</script>
