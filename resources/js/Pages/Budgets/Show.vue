<template>
    <AppLayout>
        <div class="items-center justify-start bg-white min-h-screen w-full flex flex-col">
            <div class="container mt-10 p-6 bg-white rounded-lg">
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
                    <button @click="sendBudget" title="Enviar Presupesto">
                        <SendIcon class="w-6 h-6 fill-gray-950 text-gray-600" />
                    </button>
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

            <!-- Popup de notificación -->
            <div v-if="popupVisible" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white rounded-lg p-6 shadow-lg w-96 text-center">
                    <h2 class="text-lg font-semibold text-blue-500 mb-4">Enviando presupuesto...</h2>
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

// Estado del popup
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
    popupMessage.value = ""; // Mostra "Enviando factura..."
    try {
        await Inertia.get(route("budgets.send", props.budget.id));

        popupMessage.value = "Missatge enviat correctament!";
    } catch (error) {
        popupMessage.value = "El missatge no se ha pogut enviar.";
    }
};

const closePopup = () => {
    popupVisible.value = false;
    popupMessage.value = "";
};
</script>

<style scoped></style>
