<template>
    <AppLayout>
        <div class="items-center justify-start bg-white min-h-screen w-full flex flex-col">
            <div class="container mt-10 p-6 bg-white rounded-lg ">
                <h1 class="text-2xl text-blue-500 font-bold mb-6">Detalles del Presupuesto</h1>

                <!-- Información del cliente y presupuesto -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <template v-for="client in clients" :key="client.id">
                            <p v-if="client.id === budget.client_id"><strong>Cliente:</strong> {{ client.name }}</p>
                        </template>
                        <p><strong>Identificador:</strong> {{ budget.name }}</p>
                        <p><strong>Fecha:</strong> {{ budget.date }}</p>
                        <p><strong>Estado:</strong> {{ budget.state }}</p>
                    </div>
                    <div>
                        <p><strong>Total:</strong> {{ budget.total }}</p>
                        <p><strong>Base Imponible:</strong> {{ budget.base_imponible }}</p>
                        <p><strong>IVA (%):</strong> {{ budget.iva }}</p>
                        <p><strong>Monto IVA:</strong> {{ budget.monto_iva }}</p>
                    </div>
                </div>

                <!-- Botones de acciones con íconos -->
                <div class="mt-6 flex justify-start space-x-4">
                    <NavLink :href="route('budgets.print', budget.id)" title="Imprimir Presupuesto">
                        <PrintIcon class="w-6 h-6 text-gray-600" />
                    </NavLink>
                    <NavLink @click="createInvoice" title="Crear Factura">
                        <ConvertToInvoiceIcon class="w-6 h-6 text-gray-600" />
                    </NavLink>
                    <NavLink :href="route('budgets.edit', budget.id)" title="Editar Presupuesto">
                        <EditIcon class="w-6 h-6 text-gray-600" />
                    </NavLink>
                    <button @click="confirmDelete" title="Eliminar Presupuesto">
                        <DeleteIcon class="w-6 h-6 text-gray-600" />
                    </button>
                </div>

                <!-- Tabla de items del presupuesto -->
                <h2 class="text-xl font-semibold mt-6 mb-4">Items del Presupuesto</h2>
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
                    <tr v-for="item in budgetItems" :key="item.id">
                        <template v-for="product in products" :key="product.id">
                            <td v-if="item.product_id === product.id" class="px-6 py-4 whitespace-nowrap">{{ product.name }}</td>
                        </template>
                        <td class="px-6 py-4 whitespace-nowrap">{{ item.quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ item.unit_price }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ item.discount }}</td>
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
import ConvertToInvoiceIcon from "@/Components/Icons/ConvertToInvoiceIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

const props = defineProps({
    budget: Object,
    budgetItems: Array,
    products: Array,
    clients: Array
});

// Función para crear una factura desde el presupuesto
const createInvoice = () => {
    Inertia.post(route('invoices.create-from-budget', props.budget));
};

// Confirmación y eliminación del presupuesto
const confirmDelete = () => {
    if (confirm("¿Estás seguro de que quieres eliminar este presupuesto?")) {
        Inertia.delete(route('budgets.destroy', props.budget.id));
    }
};
</script>

<style scoped>
.icon-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e5e7eb; /* Fondo gris */
    transition: background-color 0.3s, color 0.3s;
    cursor: pointer;
    position: relative;
}

.icon-button:hover {
    background-color: #d1d5db; /* Gris más oscuro al hacer hover */
}

.icon-button::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    padding: 4px 8px;
    background-color: #333;
    color: #fff;
    border-radius: 4px;
    font-size: 12px;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}

.icon-button:hover::after {
    opacity: 1;
}
</style>
