<template>
    <AppLayout>
        <div class="items-center justify-start bg-white min-h-screen w-full flex flex-col">
            <div class="container mt-10 p-6 bg-white rounded-lg ">
                <h1 class="text-2xl text-blue-500 font-bold mb-6">Detalles de la Factura</h1>

                <!-- Información del cliente y factura -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <template v-for="client in clients" :key="client.id">
                            <p v-if="client.id === invoice.client_id"><strong>Cliente:</strong> {{ client.name }}</p>
                        </template>
                        <p><strong>Identificador:</strong> {{ invoice.name }}</p>
                        <p><strong>Fecha:</strong> {{ invoice.date }}</p>
                        <p><strong>Estado:</strong> {{ invoice.state }}</p>
                    </div>
                    <div>
                        <p><strong>Total:</strong> {{ invoice.total }}</p>
                        <p><strong>Base Imponible:</strong> {{ invoice.base_imponible }}</p>
                        <p><strong>IVA (%):</strong> {{ invoice.iva }}</p>
                        <p><strong>Monto IVA:</strong> {{ invoice.monto_iva }}</p>
                    </div>
                </div>

                <!-- Botones de acciones con íconos -->
                <div class="mt-6 flex justify-start space-x-4">
                    <NavLink :href="route('invoices.print', invoice.id)" title="Imprimir Factura">
                        <PrintIcon class="w-6 h-6 text-gray-600" />
                    </NavLink>
                    <NavLink :href="route('invoices.edit', invoice.id)" title="Editar Factura">
                        <EditIcon class="w-6 h-6 text-gray-600" />
                    </NavLink>
                    <NavLink :href="route('invoices.copy', invoice.id)" title="Copiar Facturas">
                        <CopyIcon class="w-6 h-6 text-gray-600 fill-gray-950" />
                    </NavLink>
                    <button @click="sendInvoice" title="Enviar factura">
                        <SendIcon class="w-6 h-6 fill-gray-950" />
                    </button>
                    <NavLink :href="route('invoices.credit-notes.create', invoice.id)" class="btn btn-primary">
                        <MenuBillingIcon class="w-6 h-6 fill-gray-950" title="Hacer un abono"/>
                    </NavLink>
                    <button @click="confirmDelete" title="Eliminar Factura">
                        <DeleteIcon class="w-6 h-6 text-gray-600" />
                    </button>
                </div>

                <!-- Popup de notificación -->
                <div v-if="popupVisible" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
                    <div class="bg-white rounded-lg p-6 shadow-lg w-96 text-center">
                        <h2 class="text-lg font-semibold text-blue-500 mb-4">Enviando factura...</h2>
                        <p v-if="popupMessage">{{ popupMessage }}</p>
                        <button
                            v-if="popupMessage"
                            class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                            @click="closePopup"
                        >
                            Tancar
                        </button>
                    </div>
                </div>

                <!-- Tabla de items de la factura -->
                <h2 class="text-xl font-semibold mt-6 mb-4">Items de la Factura</h2>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unitario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descuento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in invoiceItems" :key="item.id">
                        <template v-for="product in products" :key="product.id">
                            <td v-if="item.product_id === product.id" class="px-6 py-4 whitespace-nowrap">{{ product.name }}</td>
                        </template>
                        <td class="px-6 py-4 whitespace-nowrap">{{ item.quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ item.unit_price }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ item.discount }}% </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ item.total }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-6">
                <h2 class="text-xl font-medium">Abonos</h2>
                <div v-if="creditNotes.length === 0" class="text-gray-500">No hay abonos para esta factura.</div>
                <div v-else>
                    <div v-for="(creditNote, index) in creditNotes" :key="index" class="mb-4">
                        <div class="border p-4 rounded-lg shadow-sm">
                            <div><strong>Data:</strong> {{ creditNote.created_at }}</div>
                            <div><strong>Total sense IVA:</strong> {{ creditNote.total_without_tax }}</div>
                            <div><strong>Total amb IVA:</strong> {{ creditNote.total_with_tax }}</div>
                            <NavLink :href="route('credit-notes.edit', creditNote.id)" class="btn btn-link">Editar</NavLink>
                        </div>
                    </div>
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

// Estado del popup
const popupVisible = ref(false);
const popupMessage = ref("");

// Acción para enviar factura
const sendInvoice = async () => {
    popupVisible.value = true;
    popupMessage.value = ""; // Mostra "Enviando factura..."
    try {
        await Inertia.get(route("invoices.send", props.invoice.id));
        popupMessage.value = "Missatge enviat correctament!";
    } catch (error) {
        popupMessage.value = "El missatge no se ha pogut enviar.";
    }
};

// Tancar el popup
const closePopup = () => {
    popupVisible.value = false;
    popupMessage.value = "";
};

// Confirmació i eliminació de la factura
const confirmDelete = () => {
    if (confirm("¿Estás seguro de que quieres eliminar esta factura?")) {
        Inertia.delete(route('invoices.destroy', props.invoice.id));
    }
};
</script>

<style scoped>
/* Estils personalitzats per al popup */
</style>
