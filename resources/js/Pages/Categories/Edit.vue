<template>
    <AppLayout>
        <div class="container mx-auto max-w-3xl bg-gray-50 min-h-screen p-6">
            <h1 class="text-3xl font-semibold text-gray-800 mb-6">Editar categoría</h1>

            <form @submit.prevent="submit" class="space-y-6 bg-white p-6 rounded-lg shadow">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="name">Nombre</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700" for="description">Descripción</label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <Link :href="route('categories.show', category.id)" class="text-sm font-medium text-gray-600 hover:text-gray-800">Cancelar y volver</Link>
                    <button type="submit" class="inline-flex items-center rounded-full bg-blue-900 px-4 py-2 text-white shadow hover:bg-blue-700">
                        <SaveIcon class="mr-2 h-5 w-5 stroke-gray-100" />
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from '@/Components/Icons/SaveIcon.vue';

const props = defineProps({
    category: Object,
});

const form = useForm({
    name: props.category?.name ?? '',
    description: props.category?.description ?? '',
});

const submit = () => {
    form.put(route('categories.update', props.category.id));
};
</script>
