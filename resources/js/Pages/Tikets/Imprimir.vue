<template>
    <div id="ticket" class="max-w-sm mx-auto p-4 bg-white border border-gray-300">
        <!-- Header - Company Information -->
        <div class="text-center mb-4">
            <h1 class="text-xl font-bold">{{ company.name }}</h1>
            <p>{{ company.address }}</p>
            <p>{{ company.phone }}</p>
            <p>{{ company.nif }}</p>
            <p>{{ company.email }}</p>
        </div>

        <!-- Ticket Details -->
        <div class="mb-4">
            <p><strong>Ticket #:</strong> {{ tiket.id }}</p>
            <p><strong>Fecha:</strong> {{ tiket.created_at }}</p>
        </div>

        <!-- Ticket Items Table -->
        <div class="mb-4">
            <table class="w-full text-left">
                <thead>
                <tr>
                    <th class="text-left">Producto</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-right">Total</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in tiketItems" :key="item.id">
                    <template v-for="product in products">
                        <td v-if="item.product_id === product.id">{{ product.name }}</td>
                    </template>
                    <td class="text-center">{{ item.quantity }}</td>
                    <td class="text-right">{{ item.total }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="text-right">
            <p><strong>Base Imponible:</strong> {{ tiket.base_imponible }}€</p>
            <p><strong>IVA (21%):</strong> {{ tiket.monto_iva }}€</p>
            <p class="text-lg font-semibold"><strong>Total:</strong> {{ tiket.total }}€</p>
        </div>

        <!-- Print Button -->
        <div class="text-center mt-4">
            <button @click="printTicket" class="px-4 py-2 bg-blue-500 text-white rounded">Imprimir</button>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    company: Object,
    tiket: Object,
    tiketItems: Array,
    products: Array,
});

const printTicket = () => {
    window.print();
};
</script>

<style scoped>
@media print {
    button {
        display: none;
    }
    #ticket {
        width: 80mm;
        border: none;
    }
}
</style>
