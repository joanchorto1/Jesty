<template>
    <AppLayout title="Crear método de pago">
        <div class="min-h-screen bg-slate-950">
            <FinancePageHeader
                eyebrow="Pagos"
                title="Nuevo método de pago"
                description="Define cómo se registran los cobros para mantener alineados los informes financieros."
                :metrics-columns="3"
            >
                <template #metrics>
                    <FinanceSummaryCard label="Nombre" :value="form.name || 'Sin definir'" :helper="`${form.name.length} caracteres`" />
                    <FinanceSummaryCard label="Descripción" :value="`${descriptionLength} car.`" :helper="descriptionLength ? 'Texto preparado' : 'Añade más contexto'" />
                    <FinanceSummaryCard label="Estado" value="Activo" helper="Disponible tras guardar" />
                </template>
            </FinancePageHeader>

            <main class="max-w-4xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <section class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="mb-8 border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Datos del método</h2>
                        <p class="mt-1 text-sm text-slate-500">Especifica cómo se denomina este método y, opcionalmente, describe cuándo debe utilizarse.</p>
                    </header>

                    <form @submit.prevent="submitForm" class="grid grid-cols-1 gap-6">
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
                                Guardar método
                            </PrimaryButton>
                            <NavLink
                                :href="route('paymentMethods.index')"
                                class="text-sm font-semibold text-slate-500 transition hover:text-slate-700"
                            >
                                Cancelar y volver
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

const form = useForm({
    name: '',
    description: '',
});

const descriptionLength = computed(() => form.description?.length ?? 0);

const submitForm = () => {
    form.post(route('paymentMethods.store'));
};
</script>
