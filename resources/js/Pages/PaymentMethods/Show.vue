<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen">
            <div class="mx-auto max-w-4xl bg-white rounded-lg shadow p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-semibold text-gray-800">Detalle del método de pago</h1>
                        <p class="text-sm text-gray-500">Información y descripción del método seleccionado.</p>
                    </div>
                    <div class="flex gap-2">
                        <Link :href="route('paymentMethods.edit', paymentMethod.id)" class="rounded-full bg-blue-100 p-2 text-blue-700 hover:bg-blue-200" title="Editar">
                            <EditIcon class="h-5 w-5" />
                        </Link>
                        <button @click="destroy" class="rounded-full bg-red-100 p-2 text-red-600 hover:bg-red-200" title="Eliminar">
                            <DeleteIcon class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <div class="space-y-4 text-sm text-gray-700">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-600">Nombre</h2>
                        <p class="text-gray-900">{{ paymentMethod.name }}</p>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-600">Descripción</h2>
                        <p class="text-gray-900 whitespace-pre-line">{{ paymentMethod.description || 'Sin descripción disponible.' }}</p>
                    </div>
                    <div v-if="paymentMethod.company" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-600">Empresa</h2>
                            <p class="text-gray-900">{{ paymentMethod.company.name }}</p>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-600">CIF</h2>
                            <p class="text-gray-900">{{ paymentMethod.company.cif }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <Link :href="route('paymentMethods.index')" class="text-sm font-medium text-blue-700 hover:underline">Volver al listado</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';

const props = defineProps({
    paymentMethod: Object,
});

const paymentMethod = props.paymentMethod;

const destroy = () => {
    if (confirm('¿Deseas eliminar este método de pago?')) {
        Inertia.delete(route('paymentMethods.destroy', paymentMethod.id));
    }
};
</script>
