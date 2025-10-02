<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-900">
            <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-slate-900 pb-24">
                <div class="max-w-5xl mx-auto px-6 pt-14">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div>
                            <p class="text-sm uppercase tracking-widest text-indigo-200">Presupuesto</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white mt-2">{{ budget.name }}</h1>
                            <p class="text-sm text-indigo-100 mt-3 flex flex-wrap items-center gap-4">
                                <span class="inline-flex items-center gap-2"><span class="font-semibold">Cliente:</span> {{ getClientName(budget.client_id) }}</span>
                                <span class="inline-flex items-center gap-2"><span class="font-semibold">Fecha:</span> {{ formatDate(budget.date) }}</span>
                                <span class="inline-flex items-center gap-2"><span class="font-semibold">Estado:</span>
                                    <span :class="statusBadgeClasses(budget.state)">{{ statusCopy(budget.state) }}</span>
                                </span>
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <NavLink :href="route('budgets.print', budget.id)" title="Imprimir" class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/25 transition">
                                <PrintIcon class="w-5 h-5" />
                                Imprimir
                            </NavLink>
                            <NavLink @click="createInvoice" title="Crear factura" class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/25 transition">
                                <ConvertToInvoiceIcon class="w-5 h-5" />
                                Convertir a factura
                            </NavLink>
                            <NavLink :href="route('budgets.edit', budget.id)" title="Editar" class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/25 transition">
                                <EditIcon class="w-5 h-5" />
                                Editar
                            </NavLink>
                            <button @click="sendBudget" title="Enviar" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500/90 px-4 py-2 text-sm font-semibold text-white shadow-lg hover:bg-emerald-500 transition">
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
                                    <dd class="font-medium text-slate-800">{{ getClientName(budget.client_id) }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-slate-500">Fecha</dt>
                                    <dd class="font-medium text-slate-800">{{ formatDate(budget.date) }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-slate-500">Estado</dt>
                                    <dd><span :class="statusBadgeClasses(budget.state)">{{ statusCopy(budget.state) }}</span></dd>
                                </div>
                            </dl>
                        </div>
                        <div class="space-y-4">
                            <h2 class="text-lg font-semibold text-slate-800">Resumen económico</h2>
                            <dl class="space-y-3 text-sm text-slate-600">
                                <div class="flex items-center justify-between">
                                    <dt class="text-slate-500">Base imponible</dt>
                                    <dd class="font-medium text-slate-800">{{ formatCurrency(budget.base_imponible) }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-slate-500">IVA</dt>
                                    <dd class="font-medium text-slate-800">{{ budget.iva }}%</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-slate-500">Monto IVA</dt>
                                    <dd class="font-medium text-slate-800">{{ formatCurrency(budget.monto_iva) }}</dd>
                                </div>
                                <div class="flex items-center justify-between text-base font-semibold text-slate-800">
                                    <dt>Total</dt>
                                    <dd>{{ formatCurrency(budget.total) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">Items del presupuesto</h2>
                            <p class="text-sm text-slate-500 mt-1">Detalle de productos, cantidades y descuentos aplicados.</p>
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
                                <tr v-for="item in budgetItems" :key="item.id" class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 font-medium text-slate-700">{{ getProductName(item.product_id) }}</td>
                                    <td class="px-6 py-4">{{ item.quantity }}</td>
                                    <td class="px-6 py-4">{{ formatCurrency(item.unit_price) }}</td>
                                    <td class="px-6 py-4">{{ item.discount }}%</td>
                                    <td class="px-6 py-4 font-semibold text-slate-700">{{ formatCurrency(item.total) }}</td>
                                </tr>
                                <tr v-if="budgetItems.length === 0">
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400">No hay productos registrados en este presupuesto.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-if="popupVisible" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm flex justify-center items-center z-50">
                <div class="bg-white rounded-3xl p-8 shadow-2xl w-full max-w-md text-center">
                    <h2 class="text-lg font-semibold text-slate-800 mb-3">Enviando presupuesto...</h2>
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
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import PrintIcon from "@/Components/Icons/PrintIcon.vue";
import ConvertToInvoiceIcon from "@/Components/Icons/ConvertToInvoiceIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import NavLink from "@/Components/NavLink.vue";
import SendIcon from "@/Components/Icons/SendIcon.vue";

const props = defineProps({
    budget: Object,
    budgetItems: Array,
    products: Array,
    clients: Array
});

const popupVisible = ref(false);
const popupMessage = ref("");

const createInvoice = () => {
    Inertia.post(route('invoices.create-from-budget', props.budget));
};

const confirmDelete = () => {
    if (confirm("¿Estás seguro de que quieres eliminar este presupuesto?")) {
        Inertia.delete(route('budgets.destroy', props.budget.id));
    }
};

const sendBudget = async () => {
    popupVisible.value = true;
    popupMessage.value = "";
    try {
        await Inertia.get(route("budgets.send", props.budget.id));
        popupMessage.value = "Mensaje enviado correctamente";
    } catch (error) {
        popupMessage.value = "No se ha podido enviar el mensaje";
    }
};

const closePopup = () => {
    popupVisible.value = false;
    popupMessage.value = "";
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
        case 'accepted':
            return 'Aceptado';
        case 'in_process':
            return 'En proceso';
        case 'rejected':
            return 'Rechazado';
        default:
            return 'Sin estado';
    }
};

const statusBadgeClasses = (state) => {
    switch (state) {
        case 'accepted':
            return 'inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700';
        case 'in_process':
            return 'inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700';
        case 'rejected':
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
/* Estilos personalizados */
</style>
