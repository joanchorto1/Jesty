<template>
    <form @submit.prevent="$emit('submit')" class="space-y-8">
        <div v-if="form.hasErrors" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700" role="alert">
            <p class="font-semibold">Revisa los campos con errores.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-if="companies.length" class="md:col-span-2">
                <label for="company_id" class="block text-sm font-semibold text-slate-700">Empresa</label>
                <select
                    id="company_id"
                    v-model="form.company_id"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                    :aria-invalid="hasError('company_id')"
                    :aria-describedby="hasError('company_id') ? 'company_id-error' : undefined"
                >
                    <option value="" disabled>Selecciona una empresa</option>
                    <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                </select>
                <p v-if="hasError('company_id')" :id="'company_id-error'" class="mt-2 text-sm text-rose-500">{{ form.errors.company_id }}</p>
            </div>
            <div v-else class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700">Empresa</label>
                <div class="mt-2 flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600">
                    <span>{{ companyName }}</span>
                    <span class="text-xs uppercase tracking-widest text-slate-400">Asignada</span>
                </div>
                <input type="hidden" v-model="form.company_id" />
            </div>

            <div v-for="field in fields" :key="field.key" :class="field.full ? 'md:col-span-2' : ''">
                <label :for="field.key" class="block text-sm font-semibold text-slate-700">{{ field.label }}</label>
                <component
                    :is="field.component"
                    :id="field.key"
                    v-model="form[field.key]"
                    v-bind="field.props"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                    :aria-invalid="hasError(field.key)"
                    :aria-describedby="hasError(field.key) ? `${field.key}-error` : undefined"
                />
                <p v-if="hasError(field.key)" :id="`${field.key}-error`" class="mt-2 text-sm text-rose-500">{{ form.errors[field.key] }}</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3 text-xs text-slate-400 uppercase tracking-widest">
                <span>Atajos</span>
                <span aria-hidden="true">•</span>
                <span>Ctrl + Enter para guardar</span>
            </div>
            <div class="flex flex-wrap gap-3">
                <button
                    v-if="cancelHref"
                    type="button"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition"
                    @click="$emit('cancel', cancelHref)"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-6 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:ring-offset-2 focus:ring-offset-white disabled:cursor-not-allowed disabled:opacity-70"
                    :disabled="processing"
                >
                    <span v-if="processing" class="flex items-center gap-2">
                        <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/60 border-t-white"></span>
                        Guardando
                    </span>
                    <span v-else>{{ submitLabel }}</span>
                </button>
            </div>
        </div>
    </form>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    processing: {
        type: Boolean,
        default: false,
    },
    submitLabel: {
        type: String,
        default: 'Guardar cambios',
    },
    companies: {
        type: Array,
        default: () => [],
    },
    companyName: {
        type: String,
        default: '',
    },
    cancelHref: {
        type: String,
        default: null,
    },
});

const fields = computed(() => [
    { key: 'name', label: 'Nombre del cliente', component: 'input', props: { type: 'text', required: true } },
    { key: 'nif', label: 'NIF', component: 'input', props: { type: 'text', required: true } },
    { key: 'bank', label: 'Banco', component: 'input', props: { type: 'text', required: true } },
    { key: 'phone', label: 'Teléfono', component: 'input', props: { type: 'tel', required: true } },
    { key: 'email', label: 'Correo electrónico', component: 'input', props: { type: 'email', required: true } },
    { key: 'address', label: 'Dirección', component: 'textarea', props: { rows: 3, required: true }, full: true },
]);

function hasError(key) {
    return Boolean(props.form.errors?.[key]);
}
</script>
