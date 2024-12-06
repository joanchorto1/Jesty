<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">

            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Gastos</h2>
                        <p class="text-blue-300 text-2xl">{{ filteredExpenses.length }}</p>
                    </div>
                </div>
            </div>
            <!-- Filtros -->
            <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                <h2 class="text-lg text-blue-700 font-semibold mb-4">Filtrar Gastos</h2>
                <div class="flex space-x-10">

                    <select v-model="selectedPaymentMethod" class="border rounded p-2">
                        <option value="">Método de Pago</option>
                        <option v-for="paymentMethod in paymentMethods" :key="paymentMethod.id" :value="paymentMethod.id">{{ paymentMethod.name }}</option>

                    </select>

                    <input
                        type="date"
                        v-model="startDate"
                        class="border rounded p-2"
                    />
                    <input
                        type="date"
                        v-model="endDate"
                        class="border rounded p-2"
                    />
                    <select v-model="selectedCategory" class="border rounded p-2">
                        <option value="">Categoría de Gasto</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                    <button @click="deleteFilters" class="bg-red-600 flex font-semibold  text-white rounded p-2">
                        Limpiar filtros
                    </button>
                </div>
            </div>

            <!-- Tabla de gastos -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('expenses.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Nuevo Gasto
                    <AddIcon class="w-6 h-6 fill-gray-200 ml-5"/>
                </NavLink>
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Identificador</th>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-left">Monto</th>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="expense in filteredExpenses" :key="expense.id" class="border-t">
                        <td class="px-4 py-2">{{ expense.id }}</td>
                        <td class="px-4 py-2">{{ expense.name }}</td>
                        <td class="px-4 py-2">{{ expense.description }}</td>
                        <td class="px-4 py-2">{{ expense.amount }}</td>
                        <td class="px-4 py-2">{{ expense.date }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('expenses.show', expense.id)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('expenses.edit', expense.id)" class="text-yellow-500 ml-2">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteExpense(expense.id)" class="text-red-500 ml-2">
                                <DeleteIcon class="w-5 h-5"/>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from "@/Components/NavLink.vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import { ref, computed } from 'vue';

const props = defineProps({
    expenses: Array,
    categories: Array,
    paymentMethods: Array,// Asegúrate de que las categorías estén pasadas como props
    user: Object
});

// Estados para los filtros
const selectedPaymentMethod = ref('');
const startDate = ref('');
const endDate = ref('');
const selectedCategory = ref('');

// Computed para filtrar gastos
const filteredExpenses = computed(() => {
    return props.expenses.filter(expense => {
        const matchesPaymentMethod = selectedPaymentMethod.value ? expense.payment_method_id === selectedPaymentMethod.value: true;
        const matchesCategory = selectedCategory.value ? expense.expense_category_id === selectedCategory.value : true;
        const matchesStartDate = startDate.value ? new Date(expense.date) >= new Date(startDate.value) : true;
        const matchesEndDate = endDate.value ? new Date(expense.date) <= new Date(endDate.value) : true;

        return matchesPaymentMethod && matchesCategory && matchesStartDate && matchesEndDate;
    });
});

// Función para aplicar los filtros
const deleteFilters = () => {
    selectedPaymentMethod.value = '';
    startDate.value = '';
    endDate.value = '';
    selectedCategory.value = '';
};

// Función para eliminar un gasto
const deleteExpense = (id) => {
    if (confirm("¿Estás seguro de que deseas eliminar este gasto?")) {
        Inertia.delete(route('expenses.destroy', id));
    }
};
</script>

