<template>
    <AppLayout title="Editar gasto">
        <div class="min-h-screen bg-slate-950">
            <FinancePageHeader
                eyebrow="Contabilidad"
                :title="`Editar ${form.name || 'gasto'}`"
                description="Actualiza los datos del gasto con una interfaz alineada al resto de la suite contable."
                :metrics-columns="3"
            >
                <template #metrics>
                    <FinanceSummaryCard
                        label="Base imponible"
                        :value="formatCurrency(form.amount)"
                        :helper="form.date ? formatDate(form.date) : 'Fecha pendiente'"
                    />
                    <FinanceSummaryCard
                        label="IVA recalculado"
                        :value="formatCurrency(taxAmount)"
                        :helper="`${form.iva || 0}% aplicado`"
                    />
                    <FinanceSummaryCard
                        label="Total actualizado"
                        :value="formatCurrency(totalAmount)"
                        :helper="form.payment_method_id ? paymentMethodName : 'Método pendiente'"
                    />
                </template>
            </FinancePageHeader>

            <main class="max-w-5xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <section class="rounded-3xl border border-white/10 bg-white/95 p-8 shadow-xl">
                    <header class="mb-8 border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-semibold text-slate-800">Detalles del gasto</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Los cambios se reflejarán en informes, dashboards y en la ficha del gasto inmediatamente.
                        </p>
                    </header>

                    <form @submit.prevent="submitForm" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <InputLabel for="date" value="Fecha" />
                            <TextInput id="date" v-model="form.date" type="date" class="mt-2 block w-full" />
                            <InputError :message="form.errors.date" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="name" value="Nombre del gasto" />
                            <TextInput id="name" v-model="form.name" type="text" class="mt-2 block w-full" />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="expense_category_id" value="Categoría" />
                            <SelectInput id="expense_category_id" v-model="form.expense_category_id" class="mt-2 block w-full">
                                <option value="">Selecciona una categoría</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </SelectInput>
                            <InputError :message="form.errors.expense_category_id" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="payment_method_id" value="Método de pago" />
                            <SelectInput id="payment_method_id" v-model="form.payment_method_id" class="mt-2 block w-full">
                                <option value="">Selecciona un método</option>
                                <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                                    {{ method.name }}
                                </option>
                            </SelectInput>
                            <InputError :message="form.errors.payment_method_id" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="amount" value="Base imponible" />
                            <TextInput id="amount" v-model="form.amount" type="number" step="0.01" min="0" class="mt-2 block w-full" />
                            <InputError :message="form.errors.amount" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="iva" value="IVA (%)" />
                            <TextInput id="iva" v-model="form.iva" type="number" step="0.01" min="0" class="mt-2 block w-full" />
                            <InputError :message="form.errors.iva" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2">
                            <InputLabel for="description" value="Descripción" />
                            <TextareaInput id="description" v-model="form.description" rows="3" class="mt-2 block w-full" />
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2 space-y-3">
                            <InputLabel for="file" value="Documento adjunto" />
                            <div v-if="currentFileUrl" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                                <span class="font-medium text-slate-700">Archivo actual:</span>
                                <a :href="currentFileUrl" target="_blank" class="text-emerald-600 hover:text-emerald-800">Ver documento</a>
                            </div>
                            <input
                                id="file"
                                type="file"
                                class="mt-2 block w-full rounded-md border border-dashed border-slate-300 bg-slate-50 p-3 text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-emerald-100 file:px-4 file:py-2 file:text-emerald-700 hover:border-emerald-300"
                                @change="handleFileChange"
                            />
                            <p v-if="fileName" class="text-sm text-slate-500">Nuevo archivo: {{ fileName }}</p>
                            <InputError :message="form.errors.file" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2 flex items-center gap-3 pt-4">
                            <PrimaryButton type="submit" :disabled="form.processing">
                                Guardar cambios
                            </PrimaryButton>
                            <NavLink
                                :href="route('expenses.show', expense.id)"
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
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FinancePageHeader from '@/Components/Finance/FinancePageHeader.vue';
import FinanceSummaryCard from '@/Components/Finance/FinanceSummaryCard.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    paymentMethods: {
        type: Array,
        default: () => [],
    },
    expense: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.expense.name || '',
    description: props.expense.description || '',
    amount: props.expense.amount ?? '',
    iva: props.expense.iva ?? 21,
    date: props.expense.date || new Date().toISOString().split('T')[0],
    payment_method_id: props.expense.payment_method_id ?? '',
    expense_category_id: props.expense.expense_category_id ?? '',
    file: null,
});

const fileName = ref('');

const numeric = (value) => Number(value || 0);

const taxAmount = computed(() => numeric(form.amount) * numeric(form.iva) / 100);
const totalAmount = computed(() => numeric(form.amount) + taxAmount.value);

const paymentMethodName = computed(() => {
    const method = props.paymentMethods.find((item) => item.id === form.payment_method_id);
    return method?.name || '';
});

const currentFileUrl = computed(() => {
    if (!props.expense.file) {
        return '';
    }
    const path = props.expense.file.startsWith('public/')
        ? props.expense.file.replace('public/', '')
        : props.expense.file;
    return `/storage/${path}`;
});

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

const handleFileChange = (event) => {
    const [file] = event.target.files;
    form.file = file || null;
    fileName.value = file ? file.name : '';
};

const submitForm = () => {
    form.post(route('expenses.update2', props.expense.id), {
        forceFormData: true,
        onSuccess: () => {
            fileName.value = '';
            form.file = null;
        },
    });
};
</script>
