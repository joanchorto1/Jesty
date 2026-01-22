<template>
    <AppLayout title="Editar categoría">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-4xl flex-col gap-10 px-6">
                <CrudPageHeader
                    :title="`Editar ${form.name || 'categoría'}`"
                    description="Mantén tus etiquetas contables coherentes para comparar periodos y realizar auditorías con facilidad."
                />

                <div class="grid gap-6 md:grid-cols-3">
                    <CrudStatCard label="Nombre" :value="form.name || 'Sin definir'" icon-background="bg-sky-500/10 text-sky-600" />
                    <CrudStatCard label="Descripción" :value="`${descriptionLength} car.`" icon-background="bg-indigo-500/10 text-indigo-600">
                        <template #description>
                            <p class="text-xs text-slate-500">{{ descriptionLength ? 'Actualizada' : 'Añade contexto' }}</p>
                        </template>
                    </CrudStatCard>
                    <CrudStatCard label="Creación" :value="formatDate(category.created_at)" icon-background="bg-emerald-500/10 text-emerald-600">
                        <template #description>
                            <p class="text-xs text-slate-500">Última actualización: {{ formatDate(category.updated_at) }}</p>
                        </template>
                    </CrudStatCard>
                </div>

                <Panel title="Datos de la categoría" description="Los cambios se verán reflejados en el resto de vistas contables que utilizan esta categoría.">
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
                                Guardar cambios
                            </PrimaryButton>
                            <NavLink
                                :href="route('categories.show', category.id)"
                                class="text-sm font-semibold text-slate-500 transition hover:text-slate-700"
                            >
                                Cancelar y ver ficha
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

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
});

const category = computed(() => props.category ?? {});

const form = useForm({
    name: category.value.name || '',
    description: category.value.description || '',
});

const descriptionLength = computed(() => form.description?.length ?? 0);

const formatDate = (value) => {
    if (!value) {
        return '—';
    }
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }
    return new Intl.DateTimeFormat('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(date);
};

const submit = () => {
    form.put(route('categories.update', category.value.id));
};
</script>
