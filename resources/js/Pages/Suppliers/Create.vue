<template>
    <AppLayout>
    <div>
        <h1 class="text-2xl font-bold mb-4">Create Supplier</h1>
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block text-gray-700">Name</label>
                <input v-model="form.name" class="w-full border px-4 py-2" type="text" placeholder="Supplier Name" />
                <p v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input v-model="form.email" class="w-full border px-4 py-2" type="email" placeholder="Email" />
                <p v-if="errors.email" class="text-red-500 text-sm">{{ errors.email }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Phone</label>
                <input v-model="form.phone" class="w-full border px-4 py-2" type="text" placeholder="Phone" />
                <p v-if="errors.phone" class="text-red-500 text-sm">{{ errors.phone }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Address</label>
                <input v-model="form.address" class="w-full border px-4 py-2" type="text" placeholder="Address" />
                <p v-if="errors.address" class="text-red-500 text-sm">{{ errors.address }}</p>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create</button>
        </form>
    </div>
    </AppLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from "@/Layouts/AppLayout.vue";
import {Inertia} from "@inertiajs/inertia";

const form = reactive({
    name: '',
    email: '',
    phone: '',
    address: '',
});

const { errors } = useForm(form);

function submit() {
    Inertia.post(route('suppliers.store'), form);
}
</script>
