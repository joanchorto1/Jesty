<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="space-y-3">
                    <p class="text-blue-200 text-sm uppercase tracking-widest">Correo transaccional</p>
                    <h1 class="text-3xl sm:text-4xl font-semibold text-white">Configurar envío de correos</h1>
                    <p class="text-sm text-blue-200 max-w-2xl">Actualiza las credenciales SMTP para garantizar el envío seguro de comunicaciones.</p>
                </div>
            </template>

            <AdminPanel title="Parámetros SMTP" description="Revisa los datos proporcionados por tu proveedor y guarda los cambios.">
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">SMTP Host</label>
                            <input v-model="form.smtp_host" type="text" :class="inputClasses" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">SMTP Port</label>
                            <input v-model="form.smtp_port" type="text" :class="inputClasses" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Cifrado</label>
                            <select v-model="form.smtp_encryption" :class="inputClasses">
                                <option value="ssl">SSL</option>
                                <option value="tls">TLS</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Usuario SMTP</label>
                            <input v-model="form.smtp_username" type="text" :class="inputClasses" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Contraseña SMTP</label>
                            <input v-model="form.smtp_password" type="password" :class="inputClasses" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Correo remitente</label>
                            <input v-model="form.from_email" type="email" :class="inputClasses" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Nombre remitente</label>
                            <input v-model="form.from_name" type="text" :class="inputClasses" />
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <NavLink :href="route('dashboard')" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Cancelar</NavLink>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 transition">
                            Guardar configuración
                        </button>
                    </div>
                </form>
            </AdminPanel>
        </AdminPage>
    </AppLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import AdminPage from "@/Components/Dashboard/AdminPage.vue";
import AdminPanel from "@/Components/Dashboard/AdminPanel.vue";

const props = defineProps({
    emailConfiguration: Object,
});

const inputClasses = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none transition';

const form = reactive({
    smtp_host: props.emailConfiguration.smtp_host,
    smtp_port: props.emailConfiguration.smtp_port,
    smtp_username: props.emailConfiguration.smtp_username,
    smtp_password: props.emailConfiguration.smtp_password,
    smtp_encryption: props.emailConfiguration.smtp_encryption,
    from_email: props.emailConfiguration.from_email,
    from_name: props.emailConfiguration.from_name,
});

const handleSubmit = () => {
    Inertia.put(route('email-configurations.update', props.emailConfiguration.id), form);
};
</script>
