<template>
    <AppLayout title="Editar método de pago">
        <div class="min-h-screen bg-slate-950">
            <FinancePageHeader
                eyebrow="Pagos"
                :title="`Editar ${form.name || 'método'}`"
                description="Actualiza el método para que los equipos sepan cuándo utilizarlo y cómo se reflejará en los informes."
                :metrics-columns="3"
            >
                <template #metrics>
                    <FinanceSummaryCard label="Nombre" :value="form.name || 'Sin definir'" :helper="`${form.name.length} caracteres`" />
                    <FinanceSummaryCard label="Descripción" :value="`${descriptionLength} car.`" :helper="descriptionLength ? 'Actualizada' : 'Añade contexto'" />
                    <FinanceSummaryCard label="Uso" :value="`${expensesCount} movimientos`" :helper="expensesCount === 1 ? 'Gasto vinculado' : 'Gastos vinculados'" />
                </template>
            </FinancePageHeader>

            <main class="max-w-4xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <section class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="mb-8 border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Datos del método</h2>
                        <p class="mt-1 text-sm text-slate-500">Cualquier ajuste impactará en los formularios de gastos y en los listados contables.</p>
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

                        <input type="hidden" v-model="form.company_id" />

                        <div class="flex items-center gap-3 pt-2">
                            <PrimaryButton type="submit" :disabled="form.processing">
                                Guardar cambios
                            </PrimaryButton>
                            <NavLink
                                :href="route('paymentMethods.show', method.id)"
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
import { useForm, usePage } from '@inertiajs/vue3';
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
    method: {
        type: Object,
        required: true,
    },
    expensesCount: {
        type: Number,
        default: 0,
    },
});

const page = usePage();
const fallbackCompanyId = page.props?.auth?.user?.company_id ?? '';

const method = computed(() => props.method ?? {});

const form = useForm({
    name: method.value.name || '',
    description: method.value.description || '',
    company_id: method.value.company_id ?? fallbackCompanyId,
});

const descriptionLength = computed(() => form.description?.length ?? 0);
const expensesCount = computed(() => props.expensesCount ?? 0);

const submitForm = () => {
    form.put(route('paymentMethods.update', method.value.id));
};
</script>
