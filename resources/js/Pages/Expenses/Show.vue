<template>
    <AppLayout>
        <div class="items-center justify-start bg-gray-50 min-h-screen w-full flex flex-col">
            <div class="container mt-10 p-6 bg-white rounded-lg shadow max-w-5xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl text-gray-800 font-semibold">Detalles del gasto</h1>
                        <p class="text-sm text-gray-500">Consulta la información y los importes asociados a este gasto.</p>
                    </div>
                    <div class="flex gap-2">
                        <Link :href="route('expenses.edit', expense.id)" title="Editar Gasto" class="rounded-full bg-blue-100 p-2 text-blue-700 hover:bg-blue-200">
                            <EditIcon class="w-5 h-5" />
                        </Link>
                        <button @click="confirmDelete" title="Eliminar Gasto" class="rounded-full bg-red-100 p-2 text-red-600 hover:bg-red-200">
                            <DeleteIcon class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <section class="space-y-3">
                        <h2 class="text-lg font-semibold text-gray-700">Información general</h2>
                        <div class="rounded-lg border border-gray-200 p-4">
                            <dl class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <dt class="font-medium text-gray-500">Nombre</dt>
                                    <dd class="text-gray-800">{{ expense.name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="font-medium text-gray-500">Fecha</dt>
                                    <dd class="text-gray-800">{{ expense.date }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="font-medium text-gray-500">Método de pago</dt>
                                    <dd class="text-gray-800">{{ paymentMethodName }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="font-medium text-gray-500">Categoría</dt>
                                    <dd class="text-gray-800">{{ categoryName }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="rounded-lg border border-gray-200 p-4">
                            <h3 class="text-sm font-semibold text-gray-600 mb-2">Descripción</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ expense.description || 'Sin descripción' }}</p>
                        </div>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-lg font-semibold text-gray-700">Resumen económico</h2>
                        <div class="rounded-lg border border-gray-200 p-4 space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span class="font-medium text-gray-500">Importe base</span>
                                <span class="text-gray-800">{{ Number(expense.amount).toFixed(2) }} €</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span class="font-medium text-gray-500">IVA aplicado</span>
                                <span class="text-gray-800">{{ expense.iva }} %</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span class="font-medium text-gray-500">Monto IVA</span>
                                <span class="text-gray-800">{{ ivaAmount.toFixed(2) }} €</span>
                            </div>
                            <div class="flex justify-between text-base font-semibold text-gray-700">
                                <span>Total con impuestos</span>
                                <span>{{ totalAmount.toFixed(2) }} €</span>
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-200 p-4">
                            <h3 class="text-sm font-semibold text-gray-600 mb-2">Archivo adjunto</h3>
                            <template v-if="expense.file">
                                <a :href="`/storage/${expense.file}`" target="_blank" class="text-blue-700 text-sm hover:underline">
                                    Ver archivo
                                </a>
                            </template>
                            <p v-else class="text-sm text-gray-500">No se adjuntó ningún archivo.</p>
                        </div>
                    </section>
                </div>

                <div class="mt-8">
                    <Link :href="route('expenses.index')" class="text-sm font-medium text-blue-700 hover:underline">Volver al listado de gastos</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';

const props = defineProps({
    expense: Object,
    paymentMethods: Array,
    categories: Array,
});

const ivaAmount = computed(() => Number(props.expense.amount || 0) * (Number(props.expense.iva || 0) / 100));
const totalAmount = computed(() => Number(props.expense.amount || 0) + ivaAmount.value);

const paymentMethodName = computed(() => {
    const method = props.paymentMethods.find((item) => item.id === props.expense.payment_method_id);
    return method ? method.name : 'Sin método asignado';
});

const categoryName = computed(() => {
    const category = props.categories.find((item) => item.id === props.expense.expense_category_id);
    return category ? category.name : 'Sin categoría';
});

const confirmDelete = () => {
    if (confirm('¿Estás seguro de que quieres eliminar este gasto?')) {
        Inertia.delete(route('expenses.destroy', props.expense.id));
    }
};
</script>
