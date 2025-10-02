<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="space-y-3">
                        <p class="text-blue-200 text-sm uppercase tracking-widest">Compañías</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Crear compañía</h1>
                        <p class="text-sm text-blue-200 max-w-2xl">Registra una nueva organización para asignar licencias, usuarios y configuraciones específicas.</p>
                    </div>
                    <NavLink :href="route('companies.index')" class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-4 py-2 text-sm font-semibold text-white/80 backdrop-blur transition hover:bg-white/10">
                        Volver al listado
                    </NavLink>
                </div>
            </template>

            <AdminPanel title="Datos generales" description="Completa la información fiscal y de contacto para habilitar la cuenta.">
                <form @submit.prevent="createCompany" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-slate-600">Nombre</label>
                            <input id="name" v-model="form.name" type="text" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2">
                            <label for="nif" class="block text-sm font-medium text-slate-600">NIF</label>
                            <input id="nif" v-model="form.nif" type="text" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2">
                            <label for="phone" class="block text-sm font-medium text-slate-600">Teléfono</label>
                            <input id="phone" v-model="form.phone" type="text" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-slate-600">Correo electrónico</label>
                            <input id="email" v-model="form.email" type="email" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-slate-600">Dirección de facturación</label>
                            <input id="address" v-model="form.address" type="text" :class="inputClasses" required />
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <NavLink :href="route('companies.index')" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Cancelar</NavLink>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 transition">
                            Crear compañía
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

const inputClasses = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none transition';

const form = reactive({
    name: '',
    nif: '',
    phone: '',
    email: '',
    address: '',
});

const createCompany = () => {
    Inertia.post(route('companies.store'), form);
};
</script>
