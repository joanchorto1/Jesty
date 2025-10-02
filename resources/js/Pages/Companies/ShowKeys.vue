<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="space-y-3">
                    <p class="text-blue-200 text-sm uppercase tracking-widest">Compañías</p>
                    <h1 class="text-3xl sm:text-4xl font-semibold text-white">Gestión de claves API</h1>
                    <p class="text-sm text-blue-200 max-w-2xl">Administra las credenciales públicas y privadas utilizadas para integrar la plataforma con otros sistemas.</p>
                </div>
            </template>

            <AdminPanel title="Claves de autenticación" description="Guarda los cambios para activar las credenciales o genera un nuevo par de claves.">
                <form @submit.prevent="saveKeys" class="space-y-6">
                    <div class="space-y-2">
                        <label for="public_key" class="block text-sm font-medium text-slate-600">Clave pública</label>
                        <textarea
                            id="public_key"
                            v-model="form.public_key"
                            rows="5"
                            :class="textareaClasses"
                            placeholder="Introduce aquí la clave pública"
                        ></textarea>
                    </div>
                    <div class="space-y-2">
                        <label for="private_key" class="block text-sm font-medium text-slate-600">Clave privada</label>
                        <textarea
                            id="private_key"
                            v-model="form.private_key"
                            rows="5"
                            :class="textareaClasses"
                            placeholder="Introduce aquí la clave privada"
                        ></textarea>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <button
                            type="button"
                            @click="generateKeys"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-blue-600"
                        >
                            Generar nuevas claves
                        </button>
                        <div class="flex gap-3">
                            <NavLink :href="route('companies.show', company.id)" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Cancelar</NavLink>
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 transition">
                                Guardar claves
                            </button>
                        </div>
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
    public_key: String,
    private_key: String,
});

const textareaClasses = 'w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none transition';

const form = reactive({
    public_key: props.public_key,
    private_key: props.private_key,
});

const saveKeys = () => {
    Inertia.put(route('companies.updateKeys', props.company.id), form);
};

const generateKeys = () => {
    Inertia.get(route('companies.generateKeys', props.company.id));
};
</script>
