<template>
    <AppLayout>
        <div class="container mx-auto max-w-3xl bg-gray-50 min-h-screen p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Detalle de la categoría</h1>
                    <p class="text-sm text-gray-500">Consulta los datos registrados para esta categoría de inventario.</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('categories.edit', category.id)" class="rounded-full bg-blue-100 p-2 text-blue-700 hover:bg-blue-200" title="Editar">
                        <EditIcon class="h-5 w-5" />
                    </Link>
                    <button @click="destroy" class="rounded-full bg-red-100 p-2 text-red-600 hover:bg-red-200" title="Eliminar">
                        <DeleteIcon class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-lg bg-white p-6 shadow">
                    <dl class="space-y-4 text-sm text-gray-700">
                        <div>
                            <dt class="font-semibold text-gray-500">Nombre</dt>
                            <dd class="text-gray-900">{{ category.name }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Descripción</dt>
                            <dd class="text-gray-900 whitespace-pre-line">{{ category.description || 'Sin descripción' }}</dd>
                        </div>
                        <div v-if="category.company" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="font-semibold text-gray-500">Empresa</dt>
                                <dd class="text-gray-900">{{ category.company.name }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-gray-500">CIF</dt>
                                <dd class="text-gray-900">{{ category.company.cif }}</dd>
                            </div>
                        </div>
                    </dl>
                </div>

                <div>
                    <Link :href="route('categories.index')" class="text-sm font-medium text-blue-700 hover:underline">Volver al listado</Link>
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
    category: Object,
});

const category = props.category;

const destroy = () => {
    if (confirm('¿Deseas eliminar esta categoría?')) {
        Inertia.delete(route('categories.destroy', category.id));
    }
};
</script>
