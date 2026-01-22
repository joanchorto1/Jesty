<template>
    <AppLayout title="Crear categoría">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-4xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Nueva categoría contable"
                    description="Clasifica gastos o ingresos bajo etiquetas consistentes para facilitar la elaboración de informes."
                />

                <div class="grid gap-6 md:grid-cols-3">
                    <CrudStatCard label="Nombre" :value="form.name || 'Sin definir'" icon-background="bg-sky-500/10 text-sky-600" />
                    <CrudStatCard label="Descripción" :value="`${descriptionLength} car.`" icon-background="bg-indigo-500/10 text-indigo-600">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ descriptionLength ? 'Texto preparado' : 'Añade una descripción' }}</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Estado" value="Borrador" icon-background="bg-amber-500/10 text-amber-600">
                        <template #description>
                            <p class="text-xs text-slate-500">Pendiente de guardar</p>
                        </template>
                    </CrudStatCard>
                </div>

                <Panel title="Datos de la categoría" description="Define un nombre representativo y una descripción clara para que el equipo contable identifique fácilmente la categoría.">
                    <form @submit.prevent="submit" class="grid grid-cols-1 gap-6">
                        <div>
                            <InputLabel for="name" value="Nombre" />
                            <TextInput id="name" v-model="form.name" type="text" class="mt-2 block w-full" />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Descripción" />
                            <TextareaInput id="description" v-model="form.description" rows="4" class="mt-2 block w-full" />
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <PrimaryButton type="submit" :disabled="form.processing">
                                Guardar categoría
                            </PrimaryButton>
                            <NavLink
                                :href="route('categories.index')"
                                class="text-sm font-semibold text-slate-500 transition hover:text-slate-700"
                            >
                                Cancelar y volver
                            </NavLink>
                        </div>
                    </form>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import Panel from '@/Components/UI/Panel.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import NavLink from '@/Components/NavLink.vue';

const form = useForm({
    name: '',
    description: '',
});

const descriptionLength = computed(() => form.description?.length ?? 0);

const submit = () => {
    form.post(route('categories.store'));
};
</script>
