<template>
    <AppLayout>
    <div>
        <h1 class="text-2xl font-bold mb-4">Edit Supplier</h1>
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block text-gray-700">Name</label>
                <input v-model="form.name" class="w-full border px-4 py-2" type="text" />
                <p v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input v-model="form.email" class="w-full border px-4 py-2" type="email" />
                <p v-if="errors.email" class="text-red-500 text-sm">{{ errors.email }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Phone</label>
                <input v-model="form.phone" class="w-full border px-4 py-2" type="text" />
                <p v-if="errors.phone" class="text-red-500 text-sm">{{ errors.phone }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Address</label>
                <input v-model="form.address" class="w-full border px-4 py-2" type="text" />
                <p v-if="errors.address" class="text-red-500 text-sm">{{ errors.address }}</p>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
        </form>
    </div>
    </AppLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from "@/Layouts/AppLayout.vue";
import {Inertia} from "@inertiajs/inertia";
import {route} from "ziggy-js";
const props = defineProps({
    supplier: Object,
});

const form = reactive({
    id: props.supplier.id,
    name: props.supplier.name,
    email: props.supplier.email,
    phone: props.supplier.phone,
    address: props.supplier.address,
});

const { errors } = useForm(form);

function submit() {
    Inertia.put(route('suppliers.update',props.supplier.id), form);
}
</script>
