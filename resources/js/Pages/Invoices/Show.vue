<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-900">
            <div class="bg-gradient-to-r from-blue-700 via-sky-600 to-slate-900 pb-24">
                <div class="max-w-5xl mx-auto px-6 pt-14">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div>
                            <p class="text-sm uppercase tracking-widest text-sky-200">Factura</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white mt-2">{{ invoice.name }}</h1>
                            <p class="text-sm text-sky-100 mt-3 flex flex-wrap items-center gap-4">
                                <span class="inline-flex items-center gap-2"><span class="font-semibold">Cliente:</span> {{ getClientName(invoice.client_id) }}</span>
                                <span class="inline-flex items-center gap-2"><span class="font-semibold">Fecha:</span> {{ formatDate(invoice.date) }}</span>
                                <span class="inline-flex items-center gap-2"><span class="font-semibold">Estado:</span>
                                    <span :class="statusBadgeClasses(invoice.state)">{{ statusCopy(invoice.state) }}</span>
                                </span>
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <NavLink :href="route('invoices.print', invoice.id)" title="Imprimir" class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/25 transition">
                                <PrintIcon class="w-5 h-5" />
                                Imprimir
                            </NavLink>
                            <NavLink :href="route('invoices.edit', invoice.id)" title="Editar" class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/25 transition">
                                <EditIcon class="w-5 h-5" />
                                Editar
                            </NavLink>
                            <NavLink :href="route('invoices.copy', invoice.id)" title="Duplicar" class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/25 transition">
                                <CopyIcon class="w-5 h-5" />
                                Duplicar
                            </NavLink>
                            <NavLink :href="route('invoices.credit-notes.create', invoice.id)" title="Nuevo abono" class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/25 transition">
                                <MenuBillingIcon class="w-5 h-5" />
                                Crear abono
                            </NavLink>
                            <button @click="sendInvoice" title="Enviar" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500/90 px-4 py-2 text-sm font-semibold text-white shadow-lg hover:bg-emerald-500 transition">
                                <SendIcon class="w-5 h-5" />
                                Enviar
                            </button>
                            <button @click="confirmDelete" title="Eliminar" class="inline-flex items-center gap-2 rounded-xl bg-rose-500/90 px-4 py-2 text-sm font-semibold text-white shadow-lg hover:bg-rose-500 transition">
                                <DeleteIcon class="w-5 h-5" />
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-6 -mt-20 pb-16 space-y-10">
                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <h2 class="text-lg font-semibold text-slate-800">Información general</h2>
                            <dl class="space-y-3 text-sm text-slate-600">
                                <div class="flex items-center justify-between">
                                    <dt class="text-slate-500">Cliente</dt>
                                    <dd class="font-medium text-slate-800">{{ getClientName(invoice.client_id) }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-slate-500">Fecha</dt>
                                    <dd class="font-medium text-slate-800">{{ formatDate(invoice.date) }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-slate-500">Estado</dt>
                                    <dd><span :class="statusBadgeClasses(invoice.state)">{{ statusCopy(invoice.state) }}</span></dd>
                                </div>
                            </dl>
                        </div>
                        <div class="space-y-4">
                            <h2 class="text-lg font-semibold text-slate-800">Resumen económico</h2>
                            <dl class="space-y-3 text-sm text-slate-600">
                                <div class="flex items-center justify-between">
                                    <dt class="text-slate-500">Base imponible</dt>
                                    <dd class="font-medium text-slate-800">{{ formatCurrency(invoice.base_imponible) }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-slate-500">IVA</dt>
                                    <dd class="font-medium text-slate-800">{{ invoice.iva }}%</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-slate-500">Monto IVA</dt>
                                    <dd class="font-medium text-slate-800">{{ formatCurrency(invoice.monto_iva) }}</dd>
                                </div>
                                <div class="flex items-center justify-between text-base font-semibold text-slate-800">
                                    <dt>Total</dt>
                                    <dd>{{ formatCurrency(invoice.total) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Items de la factura</h2>
                            <p class="text-sm text-slate-500 mt-1">Detalle de conceptos, cantidades y descuentos aplicados.</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto pt-6">
                        <table class="min-w-full divide-y divide-slate-100 text-left">
                            <thead>
                                <tr class="text-xs uppercase tracking-widest text-slate-400">
                                    <th class="px-6 py-3">Producto</th>
                                    <th class="px-6 py-3">Cantidad</th>
                                    <th class="px-6 py-3">Precio unitario</th>
                                    <th class="px-6 py-3">Descuento</th>
                                    <th class="px-6 py-3">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                                <tr v-for="item in invoiceItems" :key="item.id" class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 font-medium text-slate-700">{{ getProductName(item.product_id) }}</td>
                                    <td class="px-6 py-4">{{ item.quantity }}</td>
                                    <td class="px-6 py-4">{{ formatCurrency(item.unit_price) }}</td>
                                    <td class="px-6 py-4">{{ item.discount }}%</td>
                                    <td class="px-6 py-4 font-semibold text-slate-700">{{ formatCurrency(item.total) }}</td>
                                </tr>
                                <tr v-if="invoiceItems.length === 0">
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400">No hay items registrados en esta factura.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl p-6" v-if="creditNotes">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Abonos asociados</h2>
                            <p class="text-sm text-slate-500 mt-1">Gestiona los abonos aplicados a esta factura.</p>
                        </div>
                    </div>

                    <div class="pt-6 space-y-4">
                        <div v-if="creditNotes.length === 0" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 p-6 text-center text-sm text-slate-400">
                            No hay abonos para esta factura.
                        </div>
                        <div v-else class="grid grid-cols-1 gap-4">
                            <div v-for="creditNote in creditNotes" :key="creditNote.id" class="rounded-2xl border border-slate-200 p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="space-y-2 text-sm text-slate-600">
                                    <p><span class="text-slate-500">Fecha:</span> {{ formatDate(creditNote.updated_at) }}</p>
                                    <p><span class="text-slate-500">Total sin IVA:</span> {{ formatCurrency(creditNote.total_without_tax) }}</p>
                                    <p><span class="text-slate-500">Total con IVA:</span> {{ formatCurrency(creditNote.total_with_tax) }}</p>
                                </div>
                                <NavLink :href="route('credit-notes.edit', creditNote.id)" class="inline-flex items-center gap-2 rounded-xl bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-100 transition">
                                    <EditIcon class="w-4 h-4" />
                                    Editar abono
                                </NavLink>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="popupVisible" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm flex justify-center items-center z-50">
                <div class="bg-white rounded-3xl p-8 shadow-2xl w-full max-w-md text-center">
                    <h2 class="text-lg font-semibold text-slate-800 mb-3">Enviando factura...</h2>
                    <p class="text-sm text-slate-600" v-if="!popupMessage">Estamos notificando al cliente. Por favor, espera unos segundos.</p>
                    <p class="text-sm text-slate-600" v-else>{{ popupMessage }}</p>
                    <button
                        v-if="popupMessage"
                        class="mt-6 inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition"
                        @click="closePopup"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import PrintIcon from "@/Components/Icons/PrintIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import NavLink from "@/Components/NavLink.vue";
import CopyIcon from "@/Components/Icons/CopyIcon.vue";
import SendIcon from "@/Components/Icons/SendIcon.vue";
import { ref } from "vue";
import MenuBillingIcon from "@/Components/Icons/MenuBillingIcon.vue";

const props = defineProps({
    invoice: Object,
    invoiceItems: Array,
    products: Array,
    clients: Array,
    creditNotes: Array,
});

const popupVisible = ref(false);
const popupMessage = ref("");

const sendInvoice = async () => {
    popupVisible.value = true;
    popupMessage.value = "";
    try {
        await Inertia.get(route("invoices.send", props.invoice.id));
        popupMessage.value = "Mensaje enviado correctamente";
    } catch (error) {
        popupMessage.value = "No se ha podido enviar el mensaje";
    }
};

const closePopup = () => {
    popupVisible.value = false;
    popupMessage.value = "";
};

const confirmDelete = () => {
    if (confirm("¿Estás seguro de que quieres eliminar esta factura?")) {
        Inertia.delete(route('invoices.destroy', props.invoice.id));
    }
};

const getClientName = (clientId) => {
    const client = props.clients.find(client => client.id === clientId);
    return client ? client.name : 'Cliente sin asignar';
};

const getProductName = (productId) => {
    const product = props.products.find(product => product.id === productId);
    return product ? product.name : 'Producto sin especificar';
};

const statusCopy = (state) => {
    switch (state) {
        case 'paid':
            return 'Pagado';
        case 'pending':
            return 'Pendiente';
        case 'cancelled':
            return 'Cancelado';
        default:
            return 'Sin estado';
    }
};

const statusBadgeClasses = (state) => {
    switch (state) {
        case 'paid':
            return 'inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700';
        case 'pending':
            return 'inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700';
        case 'cancelled':
            return 'inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700';
        default:
            return 'inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600';
    }
};

const formatDate = (date) => {
    if (!date) {
        return '—';
    }

    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatCurrency = (value) => {
    const numericValue = Number(value);

    if (Number.isNaN(numericValue)) {
        return '—';
    }

    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(numericValue);
};
</script>

<style scoped>
/* Estils personalitzats per al popup */
</style>
