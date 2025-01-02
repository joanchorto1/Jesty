<template>
    <AppLayout>
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-semibold text-gray-700 mb-4">Editar Configuración de Correo</h1>
        <form @submit.prevent="handleSubmit">

            <div class="mb-4">
                <label for="smtp_host" class="block text-gray-700">SMTP Host</label>
                <input
                    type="text"
                    id="smtp_host"
                    v-model="form.smtp_host"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
            <div class="mb-4">
                <label for="smtp_port" class="block text-gray-700">SMTP Port</label>
                <input
                    type="text"
                    id="smtp_port"
                    v-model="form.smtp_port"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
            <div class="mb-4">
                <label for="smtp_port" class="block text-gray-700">STMP Encrypt Type</label>
                <select v-model="form.smtp_encryption" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="ssl">SSL</option>
                    <option value="tls">TLS</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="smtp_username" class="block text-gray-700">SMTP Username</label>
                <input
                    type="text"
                    id="smtp_username"
                    v-model="form.smtp_username"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
            <div class="mb-4">
                <label for="smtp_password" class="block text-gray-700">SMTP Password</label>
                <input
                    type="password"
                    id="smtp_password"
                    v-model="form.smtp_password"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
            <div class="mb-4">
                <label for="from_email" class="block text-gray-700">Correo Remitente</label>
                <input
                    type="email"
                    id="from_email"
                    v-model="form.from_email"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
            <div class="mb-4">
                <label for="from_name" class="block text-gray-700">Nombre Remitente</label>
                <input
                    type="text"
                    id="from_name"
                    v-model="form.from_name"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
            <button
                type="submit"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-900 focus:outline-none"
            >
                <SaveIcon class="w-5 h-5 fill-gray-50 stroke-gray-400"/>
            </button>
        </form>
    </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import {Inertia} from "@inertiajs/inertia";
import AppLayout from "@/Layouts/AppLayout.vue";
import SaveIcon from "@/Components/Icons/SaveIcon.vue";
 const props = defineProps({
            emailConfiguration: Object,
        });

        const form = ref({
            smtp_host: props.emailConfiguration.smtp_host,
            smtp_port: props.emailConfiguration.smtp_port,
            smtp_username: props.emailConfiguration.smtp_username,
            smtp_password: props.emailConfiguration.smtp_password,
            smtp_encryption: props.emailConfiguration.smtp_encryption,
            from_email: props.emailConfiguration.from_email,
            from_name: props.emailConfiguration.from_name,
        });

        const handleSubmit = () => {
            console.log(form.value);
            return Inertia.put(route('email-configurations.update', props.emailConfiguration.id), form.value);
        };





</script>
