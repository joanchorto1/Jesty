<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="space-y-3">
                    <p class="text-blue-200 text-sm uppercase tracking-widest">Gestión de roles</p>
                    <h1 class="text-3xl sm:text-4xl font-semibold text-white">Crear rol</h1>
                    <p class="text-sm text-blue-200 max-w-2xl">Define un nuevo perfil de permisos para adaptar la plataforma a la operativa de tu organización.</p>
                </div>
            </template>

            <AdminPanel title="Configuración del rol" description="Selecciona el nombre, descripción y permisos que describan su alcance.">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Nombre</label>
                            <input v-model="form.name" type="text" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-600">Descripción</label>
                            <textarea v-model="form.description" rows="3" :class="[inputClasses, 'min-h-[120px]']" required></textarea>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-slate-600">Permisos</label>
                        <p class="text-xs text-slate-400">Marca las funcionalidades a las que tendrá acceso este rol.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                            <label
                                v-for="feature in features"
                                :key="feature.id"
                                class="flex items-center gap-3 rounded-2xl border border-slate-200/70 bg-white px-4 py-3 text-sm text-slate-600 shadow-sm transition hover:border-blue-400"
                            >
                                <input
                                    v-model="form.feature_ids"
                                    :value="feature.id"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-500 focus:ring-indigo-500"
                                />
                                <span>{{ feature.name }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <NavLink :href="route('roles.index')" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Cancelar</NavLink>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 transition">
                            Crear rol
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
    features: Array,
});

const inputClasses = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none transition';

const form = ref({
    name: '',
    description: '',
    feature_ids: [],
});

const submit = () => {
    Inertia.post(route('roles.store'), form.value);
};
</script>
