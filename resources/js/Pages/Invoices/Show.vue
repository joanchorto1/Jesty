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
                        <p><strong>Identificador::</strong> {{ invoice.name }}</p>
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
                    <NavLink :href="route('invoices.send', invoice.id)" title="Enviar factura">
                        <SendIcon class="w-6 h-6 fill-gray-950" />
                    </NavLink>
                    <button @click="confirmDelete" title="Eliminar Factura">
                        <DeleteIcon class="w-6 h-6 text-gray-600" />
                    </button>
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
import {ref} from "vue";

const props = defineProps({
    invoice: Object,
    invoiceItems: Array,
    products: Array,
    clients: Array
});

// Confirmación y eliminación de la factura
const confirmDelete = () => {
    if (confirm("¿Estás seguro de que quieres eliminar esta factura?")) {
        Inertia.delete(route('invoices.destroy', props.invoice.id));
    }
};


</script>

<style scoped>

</style>
