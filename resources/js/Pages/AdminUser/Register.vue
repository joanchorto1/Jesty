<template>
    <AdminPage :flushHeader="true">
        <div class="mx-auto mt-24 w-full max-w-md">
            <AdminPanel title="Crear cuenta administradora" description="Introduce los datos necesarios para generar un acceso.">
                <form @submit.prevent="submit" class="space-y-5">
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-slate-600">Nombre</label>
                        <input id="name" v-model="form.name" type="text" :class="inputClasses" required autocomplete="name" />
                        <p v-if="form.errors.name" class="text-xs text-rose-500">{{ form.errors.name }}</p>
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-slate-600">Correo electrónico</label>
                        <input id="email" v-model="form.email" type="email" :class="inputClasses" required autocomplete="email" />
                        <p v-if="form.errors.email" class="text-xs text-rose-500">{{ form.errors.email }}</p>
                    </div>
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-slate-600">Contraseña</label>
                        <input id="password" v-model="form.password" type="password" :class="inputClasses" required autocomplete="new-password" />
                        <p v-if="form.errors.password" class="text-xs text-rose-500">{{ form.errors.password }}</p>
                    </div>
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-600">Confirmar contraseña</label>
                        <input id="password_confirmation" v-model="form.password_confirmation" type="password" :class="inputClasses" required autocomplete="new-password" />
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 transition" :disabled="form.processing">
                        {{ form.processing ? 'Creando...' : 'Registrar cuenta' }}
                    </button>
                </form>
            </AdminPanel>
        </div>
    </AdminPage>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AdminPage from "@/Components/Dashboard/AdminPage.vue";
import AdminPanel from "@/Components/Dashboard/AdminPanel.vue";

const inputClasses = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none transition';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('admin.register'));
};
</script>
