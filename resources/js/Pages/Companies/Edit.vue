<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="space-y-3">
                        <p class="text-blue-200 text-sm uppercase tracking-widest">Compañías</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Editar compañía</h1>
                        <p class="text-sm text-blue-200 max-w-2xl">Actualiza los datos principales y mantén la información fiscal y de contacto al día.</p>
                    </div>
                    <NavLink :href="route('companies.show', company.id)" class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-4 py-2 text-sm font-semibold text-white/80 backdrop-blur transition hover:bg-white/10">
                        Ver detalles
                    </NavLink>
                </div>
            </template>

            <AdminPanel title="Información general" description="Revisa cada campo antes de guardar los cambios.">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Nombre</label>
                            <input v-model="form.name" type="text" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Correo electrónico</label>
                            <input v-model="form.email" type="email" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Teléfono</label>
                            <input v-model="form.phone" type="text" :class="inputClasses" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">NIF</label>
                            <input v-model="form.nif" type="text" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-600">Dirección</label>
                            <textarea v-model="form.address" rows="3" :class="[inputClasses, 'min-h-[120px]']"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <NavLink :href="route('companies.show', company.id)" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Cancelar</NavLink>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 transition">
                            Guardar cambios
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
    company: Object,
});

const inputClasses = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none transition';

const form = reactive({
    name: props.company.name,
    email: props.company.email,
    phone: props.company.phone,
    nif: props.company.nif,
    address: props.company.address,
});

const submit = () => {
    Inertia.put(route('companies.update', props.company.id), form);
};
</script>
