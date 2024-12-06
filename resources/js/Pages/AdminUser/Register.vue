<template>
    <div class="container mx-auto">
        <h1 class="text-xl font-bold">Admin Register</h1>
        <form @submit.prevent="register">
            <div class="mb-4">
                <label for="name" class="block">Name</label>
                <input v-model="form.name" type="text" id="name" class="border w-full" required />
            </div>
            <div class="mb-4">
                <label for="email" class="block">Email</label>
                <input v-model="form.email" type="email" id="email" class="border w-full" required />
            </div>
            <div class="mb-4">
                <label for="password" class="block">Password</label>
                <input v-model="form.password" type="password" id="password" class="border w-full" required />
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="block">Confirm Password</label>
                <input v-model="form.password_confirmation" type="password" id="password_confirmation" class="border w-full" required />
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2">Register</button>
            <p v-if="errors" class="text-red-500 mt-2">{{ errors }}</p>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const errors = ref(null);

const register = () => {
    useForm(form).post(route('admin.register'), {
        onError: (error) => {
            errors.value = error;
        },
    });
};
</script>
