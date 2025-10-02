<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="space-y-3">
                    <p class="text-blue-200 text-sm uppercase tracking-widest">Gestión de usuarios</p>
                    <h1 class="text-3xl sm:text-4xl font-semibold text-white">Crear usuario</h1>
                    <p class="text-sm text-blue-200 max-w-2xl">Completa la información del nuevo miembro para incorporarlo al equipo y asignarle los accesos correspondientes.</p>
                </div>
            </template>

            <AdminPanel title="Datos del usuario" description="Revisa cuidadosamente la información antes de confirmar el registro.">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Nombre</label>
                            <input v-model="form.name" type="text" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Email</label>
                            <input v-model="form.email" type="email" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Contraseña</label>
                            <input v-model="form.password" type="password" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Teléfono</label>
                            <input v-model="form.phone" type="text" :class="inputClasses" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Dirección</label>
                            <input v-model="form.address" type="text" :class="inputClasses" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Rol</label>
                            <select v-model="form.role_id" :class="inputClasses" required>
                                <option value="" disabled>Selecciona un rol</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <NavLink :href="route('users.index')" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Cancelar</NavLink>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 transition">
                            Guardar usuario
                        </button>
                    </div>
                </form>
            </AdminPanel>
        </AdminPage>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import AdminPage from "@/Components/Dashboard/AdminPage.vue";
import AdminPanel from "@/Components/Dashboard/AdminPanel.vue";

const props = defineProps({
    roles: Array,
});

const inputClasses = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none transition';

const form = ref({
    name: '',
    email: '',
    password: '',
    phone: '',
    address: '',
    role_id: '',
});

const submit = () => {
    Inertia.post(route('users.store'), form.value);
};
</script>
