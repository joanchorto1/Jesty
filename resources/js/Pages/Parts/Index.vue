<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-900">
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-slate-900 pb-20">
                <div class="max-w-7xl mx-auto px-6 pt-12">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <p class="text-sm uppercase tracking-widest text-blue-200">Facturación</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white mt-2">Gestión de partes</h1>
                            <p class="text-sm text-blue-100 mt-3 max-w-2xl">
                                Registra los partes de trabajo asociados a un cliente y consolídalos para generar facturas en segundos.
                            </p>
                        </div>
                        <NavLink
                            :href="route('parts.create')"
                            class="inline-flex items-center gap-2 rounded-2xl bg-white/15 px-5 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/25 transition"
                        >
                            <AddIcon class="w-5 h-5" />
                            Nou parte
                        </NavLink>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-widest text-blue-200">Partes pendents</p>
                            <p class="text-3xl font-semibold mt-2">{{ pendingParts }}</p>
                            <p class="text-sm text-blue-100 mt-3">{{ props.parts.length }} totals registrats</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-widest text-blue-200">Clients actius</p>
                            <p class="text-3xl font-semibold mt-2">{{ clients.length }}</p>
                            <p class="text-sm text-blue-100 mt-3">Amb parts registrats</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-widest text-blue-200">Import pendent</p>
                            <p class="text-3xl font-semibold mt-2">{{ formatCurrency(pendingAmount) }}</p>
                            <p class="text-sm text-blue-100 mt-3">Suma dels parts sense facturar</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-5 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-widest text-blue-200">Import filtrat</p>
                            <p class="text-3xl font-semibold mt-2">{{ formatCurrency(filteredTotal) }}</p>
                            <p class="text-sm text-blue-100 mt-3">Suma dels parts visibles</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 pb-5">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Filtres</h2>
                            <p class="text-sm text-slate-500 mt-1">Refina la informació per estat, client o periode.</p>
                        </div>
                        <button
                            @click="clearFilters"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-transparent bg-rose-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-600 transition"
                        >
                            Netejar filtres
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 pt-6">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Estat</label>
                            <select
                                v-model="filters.status"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40"
                            >
                                <option value="">Tots</option>
                                <option value="pending">Pendent</option>
                                <option value="invoiced">Facturat</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Client</label>
                            <select
                                v-model="filters.client_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40"
                            >
                                <option value="">Tots</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Data inici</label>
                            <input
                                v-model="filters.start_date"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40"
                            />
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Data fi</label>
                            <input
                                v-model="filters.end_date"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-blue-400 focus:ring focus:ring-blue-200/40"
                            />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between border-b border-slate-100 px-6 py-5">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Llistat de parts</h2>
                            <p class="text-sm text-slate-500 mt-1">Selecciona els parts pendents per generar una factura.</p>
                        </div>
                        <div class="flex flex-col items-stretch gap-2 sm:flex-row sm:items-center">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700"
                                :class="{ 'opacity-50 cursor-not-allowed': !canCreateInvoice }"
                                :disabled="!canCreateInvoice"
                                @click="openInvoiceModal"
                            >
                                Crear factura
                            </button>
                            <span v-if="selectionWarning" class="text-xs text-rose-500 text-center sm:text-left">{{ selectionWarning }}</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-left">
                            <thead>
                                <tr class="text-xs uppercase tracking-widest text-slate-400">
                                    <th class="px-6 py-3">
                                        <input type="checkbox" disabled />
                                    </th>
                                    <th class="px-6 py-3">Referència</th>
                                    <th class="px-6 py-3">Client</th>
                                    <th class="px-6 py-3">Data</th>
                                    <th class="px-6 py-3">Total</th>
                                    <th class="px-6 py-3">Estat</th>
                                    <th class="px-6 py-3">Factura</th>
                                    <th class="px-6 py-3 text-right">Accions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                                <tr
                                    v-for="part in filteredParts"
                                    :key="part.id"
                                    class="hover:bg-slate-50/70 transition"
                                >
                                    <td class="px-6 py-4">
                                        <input
                                            type="checkbox"
                                            :value="part.id"
                                            :checked="selectedParts.includes(part.id)"
                                            :disabled="!canSelectPart(part)"
                                            @change="toggleSelection(part.id)"
                                            class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                                        />
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-700">{{ part.reference }}</td>
                                    <td class="px-6 py-4">{{ part.client?.name || '—' }}</td>
                                    <td class="px-6 py-4">{{ formatDate(part.date) }}</td>
                                    <td class="px-6 py-4 font-semibold text-slate-700">{{ formatCurrency(part.total) }}</td>
                                    <td class="px-6 py-4">
                                        <span :class="statusBadgeClasses(part.status)">{{ statusCopy(part.status) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <NavLink
                                            v-if="part.invoice_id"
                                            :href="route('invoices.show', part.invoice_id)"
                                            class="text-sky-600 hover:text-sky-700 text-sm font-semibold"
                                        >
                                            Veure factura
                                        </NavLink>
                                        <span v-else class="text-slate-400">—</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-3 text-slate-400">
                                            <NavLink
                                                :href="route('parts.print', part.id)"
                                                class="inline-flex items-center justify-center rounded-full border border-transparent bg-slate-100 p-2 text-slate-500 transition hover:bg-slate-200 hover:text-slate-700"
                                                title="Imprimir part"
                                            >
                                                <PrintIcon class="h-5 w-5" />
                                            </NavLink>
                                            <button
                                                @click="deletePart(part.id)"
                                                class="hover:text-rose-500 transition"
                                                title="Eliminar"
                                            >
                                                <DeleteIcon class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredParts.length === 0">
                                    <td colspan="8" class="px-6 py-12 text-center text-sm text-slate-400">
                                        No hi ha parts que coincideixin amb els filtres seleccionats.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <DialogModal :show="invoiceModalOpen" @close="closeInvoiceModal">
            <template #title>Generar factura</template>
            <template #content>
                <div class="space-y-4">
                    <div class="rounded-2xl bg-slate-100 px-4 py-3 text-sm text-slate-600">
                        <p class="font-semibold text-slate-700">Client</p>
                        <p>{{ selectedClientName }}</p>
                        <p class="mt-2 text-xs text-slate-500">
                            S'inclouran {{ selectedParts.length }} parts amb un import base de {{ formatCurrency(selectedPartsBase) }}.
                        </p>
                        <p class="text-xs text-slate-500">
                            IVA estimat: {{ formatCurrency(selectedPartsTax) }} · Total: {{ formatCurrency(selectedPartsTotalWithTax) }}.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <InputLabel value="Número de factura" />
                            <div class="mt-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
                                {{ invoiceNumberPreview }}
                            </div>
                            <p class="text-xs text-slate-500">
                                El sistema generarà el codi correlatiu segons l'any de la data indicada.
                            </p>
                        </div>
                        <div class="space-y-2">
                            <InputLabel value="Data" />
                            <TextInput v-model="invoiceForm.date" type="date" class="mt-1 block w-full" />
                        </div>
                        <div class="space-y-2">
                            <InputLabel value="Estat" />
                            <SelectInput v-model="invoiceForm.state" class="mt-1 block w-full">
                                <option value="pending">Pendent</option>
                                <option value="paid">Pagada</option>
                                <option value="cancelled">Cancel·lada</option>
                            </SelectInput>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-xl border border-transparent bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-300 transition"
                        @click="closeInvoiceModal"
                    >
                        Cancel·lar
                    </button>
                    <PrimaryButton
                        :class="{ 'opacity-50 cursor-not-allowed': !canCreateInvoice }"
                        :disabled="!canCreateInvoice"
                        @click="createInvoice"
                    >
                        Confirmar
                    </PrimaryButton>
                </div>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import PrintIcon from '@/Components/Icons/PrintIcon.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    parts: { type: Array, default: () => [] },
    clients: { type: Array, default: () => [] },
    nextInvoiceNumber: { type: String, default: '' },
});

const filters = reactive({
    status: '',
    client_id: '',
    start_date: '',
    end_date: '',
});

const selectedParts = ref([]);
const invoiceModalOpen = ref(false);
const invoiceForm = reactive({
    name: props.nextInvoiceNumber || '',
    date: new Date().toISOString().split('T')[0],
    state: 'pending',
});

const invoiceNumberPreview = computed(() => {
    if (invoiceForm.name) {
        return invoiceForm.name;
    }

    if (props.nextInvoiceNumber) {
        return props.nextInvoiceNumber;
    }

    return 'Es generarà automàticament en guardar';
});

const formatCurrency = (value) =>
    new Intl.NumberFormat('ca-ES', {
        style: 'currency',
        currency: 'EUR',
    }).format(Number(value) || 0);

const formatDate = (value) => (value ? new Date(value).toLocaleDateString('ca-ES') : '—');

const filteredParts = computed(() =>
    props.parts.filter((part) => {
        const matchesStatus = !filters.status || part.status === filters.status;
        const matchesClient = !filters.client_id || part.client_id === Number(filters.client_id);
        const matchesStart = !filters.start_date || new Date(part.date) >= new Date(filters.start_date);
        const matchesEnd = !filters.end_date || new Date(part.date) <= new Date(filters.end_date);
        return matchesStatus && matchesClient && matchesStart && matchesEnd;
    })
);

const filteredTotal = computed(() => filteredParts.value.reduce((total, part) => total + Number(part.total || 0), 0));

const pendingParts = computed(() => props.parts.filter((part) => part.status === 'pending').length);
const pendingAmount = computed(() => props.parts.filter((part) => part.status === 'pending').reduce((total, part) => total + Number(part.total || 0), 0));

const clearFilters = () => {
    filters.status = '';
    filters.client_id = '';
    filters.start_date = '';
    filters.end_date = '';
};

const toggleSelection = (id) => {
    if (selectedParts.value.includes(id)) {
        selectedParts.value = selectedParts.value.filter((partId) => partId !== id);
    } else {
        selectedParts.value.push(id);
    }
};

const canSelectPart = (part) => part.status === 'pending';

const selectedPartDetails = computed(() => props.parts.filter((part) => selectedParts.value.includes(part.id)));

const selectionWarning = computed(() => {
    if (selectedParts.value.length === 0) {
        return '';
    }

    if (selectedPartDetails.value.some((part) => part.status !== 'pending')) {
        return 'Només pots facturar parts en estat pendent.';
    }

    const uniqueClients = new Set(selectedPartDetails.value.map((part) => part.client_id));
    if (uniqueClients.size > 1) {
        return 'Selecciona parts del mateix client per crear la factura.';
    }

    return '';
});

const canCreateInvoice = computed(() => selectedParts.value.length > 0 && selectionWarning.value === '');

const selectedClientName = computed(() => {
    if (selectedPartDetails.value.length === 0) {
        return '—';
    }

    const client = selectedPartDetails.value[0].client;
    return client ? client.name : '—';
});

const selectedPartsBase = computed(() =>
    selectedPartDetails.value.reduce((total, part) => {
        const partBase = part.items?.reduce((sum, item) => sum + Number(item.total || 0), 0) ?? Number(part.total || 0);
        return total + partBase;
    }, 0)
);

const selectedPartsTax = computed(() =>
    selectedPartDetails.value.reduce((total, part) => {
        const partTax = part.items?.reduce((sum, item) => {
            const base = Number(item.total || 0);
            const ivaRate = Number(item.iva ?? item.product?.iva ?? 0);
            return sum + (base * ivaRate) / 100;
        }, 0) ?? 0;

        return total + partTax;
    }, 0)
);

const selectedPartsTotalWithTax = computed(() => selectedPartsBase.value + selectedPartsTax.value);

const closeInvoiceModal = () => {
    invoiceModalOpen.value = false;
};

const openInvoiceModal = () => {
    if (!canCreateInvoice.value) {
        return;
    }

    invoiceModalOpen.value = true;
    if (!invoiceForm.name) {
        invoiceForm.name = props.nextInvoiceNumber || '';
    }
};

const resetInvoiceForm = () => {
    invoiceForm.name = props.nextInvoiceNumber || '';
    invoiceForm.date = new Date().toISOString().split('T')[0];
    invoiceForm.state = 'pending';
};

const createInvoice = () => {
    if (!canCreateInvoice.value) {
        return;
    }

    Inertia.post(
        route('parts.convertToInvoice'),
        {
            parts: selectedParts.value,
            invoice: {
                name: invoiceForm.name,
                date: invoiceForm.date,
                state: invoiceForm.state,
            },
        },
        {
            onSuccess: () => {
                invoiceModalOpen.value = false;
                selectedParts.value = [];
                resetInvoiceForm();
            },
        }
    );
};

const deletePart = (id) => {
    if (confirm('Segur que vols eliminar aquest parte?')) {
        Inertia.delete(route('parts.destroy', id));
    }
};

const statusBadgeClasses = (status) => {
    if (status === 'invoiced') {
        return 'inline-flex items-center rounded-xl bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700';
    }

    return 'inline-flex items-center rounded-xl bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700';
};

const statusCopy = (status) => (status === 'invoiced' ? 'Facturat' : 'Pendent');
</script>
