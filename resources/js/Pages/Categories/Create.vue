<template>
    <AppLayout>
    <div>
        <h1 class="text-2xl font-bold mb-4">Create Category</h1>
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block text-gray-700">Name</label>
                <input v-model="form.name" type="text" class="mt-1 block w-full border border-gray-300 p-2 rounded" placeholder="Category name">
                <span v-if="errors.name" class="text-red-500">{{ errors.name }}</span>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Description</label>
                <textarea v-model="form.description" class="mt-1 block w-full border border-gray-300 p-2 rounded" placeholder="Category description"></textarea>
                <span v-if="errors.description" class="text-red-500">{{ errors.description }}</span>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                <SaveIcon /> Save Category
            </button>
        </form>
    </div>
    </AppLayout>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from "@/Layouts/AppLayout.vue";
import SaveIcon from "@/Components/Icons/SaveIcon.vue";

export default {
    components: {SaveIcon, AppLayout},
    setup() {
        const form = useForm({
            name: '',
            description: '',
        });

        const submit = () => {
            form.post(route('categories.store'));
        };

        return {
            form,
            submit,
            errors: form.errors,
        };
    }
};
</script>
