<template>
    <AppLayout title="Editar ingreso">
        <div class="min-h-screen bg-slate-950">
            <FinancePageHeader
                eyebrow="Contabilidad"
                :title="`Editar ${form.name || 'ingreso'}`"
                description="Ajusta cualquier dato del ingreso para mantener consistentes los indicadores contables y la trazabilidad histórica."
                :metrics-columns="3"
            >
                <template #metrics>
                    <FinanceSummaryCard
                        label="Base imponible"
                        :value="formatCurrency(form.tax_base)"
                        :helper="form.date ? formatDate(form.date) : 'Fecha pendiente'"
                    />
                    <FinanceSummaryCard
                        label="IVA recalculado"
                        :value="formatCurrency(taxAmount)"
                        :helper="`${form.tax_rate || 0}% aplicado`"
                    />
                    <FinanceSummaryCard
                        label="Total actualizado"
                        :value="formatCurrency(totalAmount)"
                        :helper="form.source ? `Origen: ${form.source}` : 'Origen pendiente'"
                    />
                </template>
            </FinancePageHeader>

            <main class="max-w-4xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <section class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="mb-8 border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Información del ingreso</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Los cambios se reflejan automáticamente en el dashboard financiero y en los informes impresos.
                        </p>
                    </header>

                    <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <InputLabel for="name" value="Nombre del ingreso" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-2 block w-full"
                                autocomplete="off"
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2">
                            <InputLabel for="source" value="Origen o cliente" />
                            <TextInput
                                id="source"
                                v-model="form.source"
                                type="text"
                                class="mt-2 block w-full"
                            />
                            <InputError :message="form.errors.source" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="tax_base" value="Base imponible" />
                            <TextInput
                                id="tax_base"
                                v-model="form.tax_base"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-2 block w-full"
                            />
                            <InputError :message="form.errors.tax_base" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="tax_rate" value="IVA (%)" />
                            <TextInput
                                id="tax_rate"
                                v-model="form.tax_rate"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-2 block w-full"
                            />
                            <InputError :message="form.errors.tax_rate" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="date" value="Fecha de registro" />
                            <TextInput id="date" v-model="form.date" type="date" class="mt-2 block w-full" />
                            <InputError :message="form.errors.date" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2 flex items-center gap-3 pt-4">
                            <PrimaryButton type="submit" :disabled="form.processing">
                                Guardar cambios
                            </PrimaryButton>
                            <NavLink
                                :href="route('incomes.index')"
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
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
    income: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.income.name || '',
    source: props.income.source || '',
    tax_base: props.income.tax_base ?? '',
    tax_rate: props.income.tax_rate ?? 21,
    date: props.income.date || new Date().toISOString().split('T')[0],
});

const numeric = (value) => Number(value || 0);

const taxAmount = computed(() => numeric(form.tax_base) * numeric(form.tax_rate) / 100);
const totalAmount = computed(() => numeric(form.tax_base) + taxAmount.value);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
    }).format(numeric(value));
};

const formatDate = (value) => {
    if (!value) {
        return 'Sin fecha';
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
    form.put(route('incomes.update', props.income.id));
};
</script>
