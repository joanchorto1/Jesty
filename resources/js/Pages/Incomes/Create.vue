<template>
    <AppLayout title="Registrar ingreso">
        <div class="min-h-screen bg-slate-950">
            <FinancePageHeader
                eyebrow="Contabilidad"
                title="Registrar nuevo ingreso"
                description="Completa la información clave del ingreso para que el dashboard financiero pueda calcular los indicadores al instante."
                :metrics-columns="3"
            >
                <template #metrics>
                    <FinanceSummaryCard
                        label="Base imponible"
                        :value="formatCurrency(form.tax_base)"
                        :helper="form.date ? formatDate(form.date) : 'Fecha pendiente'"
                    />
                    <FinanceSummaryCard
                        label="IVA estimado"
                        :value="formatCurrency(taxAmount)"
                        :helper="`${form.tax_rate || 0}% aplicado`"
                    />
                    <FinanceSummaryCard
                        label="Total previsto"
                        :value="formatCurrency(totalAmount)"
                        :helper="form.source ? `Origen: ${form.source}` : 'Origen pendiente'"
                    />
                </template>
            </FinancePageHeader>

            <main class="max-w-4xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <section class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="mb-8 border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Datos del ingreso</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Estos campos alimentan los cálculos de impuestos y totales. Revisa especialmente el tipo impositivo y la fecha de registro.
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
                                placeholder="Ej. Suscripción premium"
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
                                placeholder="Ej. Factura 2024-015"
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
                                Guardar ingreso
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

const form = useForm({
    name: '',
    source: '',
    tax_base: '',
    tax_rate: 21,
    date: new Date().toISOString().split('T')[0],
});

const numeric = (value) => Number(value || 0);

const taxAmount = computed(() => {
    return numeric(form.tax_base) * numeric(form.tax_rate) / 100;
});

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
    form.post(route('incomes.store'));
};
</script>
