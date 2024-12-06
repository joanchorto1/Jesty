<template>
    <div class="container mx-auto">
        <h1 class="text-xl font-bold">Admin Login</h1>
        <form @submit.prevent="login">
            <div class="mb-4">
                <label for="email" class="block">Email</label>
                <input v-model="form.email" type="email" id="email" class="border w-full" required />
            </div>
            <div class="mb-4">
                <label for="password" class="block">Password</label>
                <input v-model="form.password" type="password" id="password" class="border w-full" required />
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2">Login</button>
            <p v-if="errors" class="text-red-500 mt-2">{{ errors }}</p>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const form = reactive({
    email: '',
    password: '',
});

const errors = ref(null);

const login = () => {
    useForm({
        email: form.email,
        password: form.password,
    }).post(route('admin.login'), {
        onError: (error) => {
            errors.value = error.email || 'Invalid credentials';
        },
    });
};
</script>
