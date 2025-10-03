<template>
    <AppLayout title="Editar categoría">
        <div class="min-h-screen bg-slate-950">
            <FinancePageHeader
                eyebrow="Catálogo"
                :title="`Editar ${form.name || 'categoría'}`"
                description="Mantén tus etiquetas contables coherentes para comparar periodos y realizar auditorías con facilidad."
                :metrics-columns="3"
            >
                <template #metrics>
                    <FinanceSummaryCard label="Nombre" :value="form.name || 'Sin definir'" :helper="`${form.name.length} caracteres`" />
                    <FinanceSummaryCard label="Descripción" :value="`${descriptionLength} car.`" :helper="descriptionLength ? 'Actualizada' : 'Añade contexto'" />
                    <FinanceSummaryCard label="Creación" :value="formatDate(category.created_at)" :helper="`Última actualización: ${formatDate(category.updated_at)}`" />
                </template>
            </FinancePageHeader>

            <main class="max-w-4xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <section class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="mb-8 border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Datos de la categoría</h2>
                        <p class="mt-1 text-sm text-slate-500">Los cambios se verán reflejados en el resto de vistas contables que utilizan esta categoría.</p>
                    </header>

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
                </section>
            </main>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FinancePageHeader from '@/Components/Finance/FinancePageHeader.vue';
import FinanceSummaryCard from '@/Components/Finance/FinanceSummaryCard.vue';
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
