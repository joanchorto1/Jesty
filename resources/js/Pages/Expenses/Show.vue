<template>
    <AppLayout>
        <div class="items-center justify-start bg-white min-h-screen w-full flex flex-col">
            <div class="container mt-10 p-6 bg-white rounded-lg ">
                <h1 class="text-2xl text-blue-500 font-bold mb-6">Detalles del Gasto</h1>

                <!-- Información del gasto -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p><strong>Nombre:</strong> {{ expense.name }}</p>
                        <p><strong>Descripción:</strong> {{ expense.description }}</p>
                        <p><strong>Fecha:</strong> {{ expense.date }}</p>
                        <template v-for="paymentMethod in paymentMethods" :key="paymentMethod.id">
                            <p v-if="paymentMethod.id === expense.payment_method_id"><strong>Método de Pago:</strong> {{ paymentMethod.name }}</p>
                        </template>
                        <template v-for="category in categories" :key="category.id">
                            <p v-if="category.id === expense.expense_category_id"><strong>Categoría:</strong> {{ category.name }}</p>
                        </template>

                    </div>
                    <div>
                        <p><strong>Total:</strong> {{ expense.amount }}</p>
                        <p><strong>IVA (%):</strong> {{ expense.iva }}</p>
                        <p><strong>Monto IVA:</strong> {{ (expense.amount * (expense.iva / 100)).toFixed(2) }}</p>
                    </div>
                </div>

                <!-- Archivo adjunto -->
                <div class="mt-6">
                    <p v-if="expense.file"><strong>Archivo Adjunto:</strong> <a :href="'/storage/'+expense.file" target="_blank" class="text-blue-600 hover:underline">Ver Archivo</a></p>
                    <p v-else><strong>No hay archivo adjunto.</strong></p>
                </div>

                <!-- Botones de acciones con íconos -->
                <div class="mt-6 flex justify-start space-x-4">
                    <NavLink :href="route('expenses.edit', expense.id)" title="Editar Gasto">
                        <EditIcon class="w-6 h-6 text-gray-600" />
                    </NavLink>
                    <button @click="confirmDelete" title="Eliminar Gasto">
                        <DeleteIcon class="w-6 h-6 text-gray-600" />
                    </button>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    expense: Object,
    paymentMethods: Array,
    categories: Array,
});


// Confirmación y eliminación del gasto
const confirmDelete = () => {
    if (confirm("¿Estás seguro de que quieres eliminar este gasto?")) {
        Inertia.delete(route('expenses.destroy', props.expense.id));
    }
};
</script>

<style scoped>
/* Estilos adicionales pueden ser añadidos aquí */
</style>

