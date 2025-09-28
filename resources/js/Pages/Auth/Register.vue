<template>
    <div class="flex items-center justify-center min-h-screen py-10 bg-gradient-to-bl from-cyan-400 via-blue-500 to-blue-800">
        <div class="w-full max-w-5xl p-8 bg-white rounded-xl shadow-lg">
            <h1 class="mb-8 text-3xl font-extrabold text-center text-blue-600">Registro de Compañía</h1>
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Datos de la Compañía -->
                    <div>
                        <h2 class="mb-6 text-xl font-bold text-gray-700">Datos de la Compañía</h2>
                        <div v-for="(field, key) in companyFields" :key="key" class="mb-6">
                            <label :for="key" class="block text-sm font-semibold text-gray-600">{{ field.label }}</label>
                            <input
                                v-model="form[key]"
                                :type="field.type"
                                :id="key"
                                :placeholder="field.placeholder"
                                class="w-full px-5 py-3 mt-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                :required="field.required"
                            />
                        </div>

                    </div>



                    <!-- Datos del Usuario -->
                    <div>
                        <h2 class="mb-6 text-xl font-bold text-gray-700">Datos del Usuario</h2>
                        <div v-for="(field, key) in userFields" :key="key" class="mb-6">
                            <label :for="key" class="block text-sm font-semibold text-gray-600">{{ field.label }}</label>
                            <input
                                v-model="form[key]"
                                :type="field.type"
                                :id="key"
                                :placeholder="field.placeholder"
                                class="w-full px-5 py-3 mt-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                :required="field.required"
                            />
                        </div>
                    </div>
                </div>
                <!-- Botón de envío -->
                <div class="mt-8">
                    <button
                        type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg"
                    >
                        Registrarse
                    </button>
                </div>
            </form>

            <div class="mt-10">
                <NavLink :href="route('login')" class="text-sm text-gray-400">Ya tengo una cuenta, quiero iniciar sesion.</NavLink>

            </div>
        </div>
    </div>
</template>


<script setup>
import { useForm } from '@inertiajs/vue3';
import NavLink from "../../../../vendor/laravel/jetstream/stubs/inertia/resources/js/Components/NavLink.vue";

const form = useForm({
    company_name: '',
    company_nif: '',
    company_address: '',
    company_phone: '',
    company_email: '',
    name: '',
    email: '',
    password: '',
    address: '',
    phone: '',
});

const companyFields = {
    company_name: { label: 'Nombre', placeholder: 'Nombre de la compañía', type: 'text', required: true },
    company_nif: { label: 'NIF', placeholder: 'Número de Identificación Fiscal', type: 'text', required: true },
    company_address: { label: 'Dirección', placeholder: 'Dirección de la compañía', type: 'text', required: true },
    company_phone: { label: 'Teléfono', placeholder: 'Teléfono de contacto', type: 'text', required: true },
    company_email: { label: 'Email', placeholder: 'Email de la compañía', type: 'email', required: true },
};

const userFields = {
    name: { label: 'Nombre', placeholder: 'Nombre del usuario', type: 'text', required: true },
    email: { label: 'Email', placeholder: 'Email del usuario', type: 'email', required: true },
    password: { label: 'Contraseña', placeholder: 'Contraseña segura', type: 'password', required: true },
    address: { label: 'Dirección', placeholder: 'Dirección del usuario', type: 'text', required: true },
    phone: { label: 'Teléfono', placeholder: 'Teléfono del usuario', type: 'text', required: true },
};
const submitForm = () => {
    form.post(route('auth.register'));
};
</script>
