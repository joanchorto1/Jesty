<template>
    <form @submit.prevent="$emit('submit')" class="space-y-8">
        <div
            v-if="form.hasErrors"
            class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700"
            role="alert"
        >
            <p class="font-semibold">Revisa los campos marcados en rojo.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="lead-name" class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Nombre</label>
                <input
                    id="lead-name"
                    v-model="form.name"
                    type="text"
                    required
                    placeholder="Ej. Laura Martínez"
                    class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200"
                    :aria-invalid="hasError('name')"
                    :aria-describedby="hasError('name') ? 'lead-name-error' : undefined"
                />
                <p v-if="hasError('name')" :id="'lead-name-error'" class="mt-2 text-sm text-rose-300">{{ form.errors.name }}</p>
            </div>

            <div>
                <label for="lead-company" class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Compañía</label>
                <input
                    id="lead-company"
                    v-model="form.company_name"
                    type="text"
                    required
                    placeholder="Nombre de la empresa"
                    class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200"
                    :aria-invalid="hasError('company_name')"
                    :aria-describedby="hasError('company_name') ? 'lead-company-error' : undefined"
                />
                <p v-if="hasError('company_name')" :id="'lead-company-error'" class="mt-2 text-sm text-rose-300">{{ form.errors.company_name }}</p>
            </div>

            <div>
                <label for="lead-email" class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Email</label>
                <input
                    id="lead-email"
                    v-model="form.email"
                    type="email"
                    required
                    placeholder="contacto@empresa.com"
                    class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200"
                    :aria-invalid="hasError('email')"
                    :aria-describedby="hasError('email') ? 'lead-email-error' : undefined"
                />
                <p v-if="hasError('email')" :id="'lead-email-error'" class="mt-2 text-sm text-rose-300">{{ form.errors.email }}</p>
            </div>

            <div>
                <label for="lead-phone" class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Teléfono</label>
                <input
                    id="lead-phone"
                    v-model="form.phone"
                    type="tel"
                    placeholder="+34 600 000 000"
                    class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200"
                    :aria-invalid="hasError('phone')"
                    :aria-describedby="hasError('phone') ? 'lead-phone-error' : undefined"
                />
                <p v-if="hasError('phone')" :id="'lead-phone-error'" class="mt-2 text-sm text-rose-300">{{ form.errors.phone }}</p>
            </div>

            <div>
                <label for="lead-position" class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Cargo</label>
                <input
                    id="lead-position"
                    v-model="form.position"
                    type="text"
                    placeholder="Director/a de compras"
                    class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200"
                    :aria-invalid="hasError('position')"
                    :aria-describedby="hasError('position') ? 'lead-position-error' : undefined"
                />
                <p v-if="hasError('position')" :id="'lead-position-error'" class="mt-2 text-sm text-rose-300">{{ form.errors.position }}</p>
            </div>

            <div>
                <label for="lead-source" class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Origen</label>
                <input
                    id="lead-source"
                    v-model="form.source"
                    type="text"
                    :list="sources.length ? sourceListId : undefined"
                    placeholder="Campaña, recomendación, evento..."
                    class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200"
                    :aria-invalid="hasError('source')"
                    :aria-describedby="hasError('source') ? 'lead-source-error' : undefined"
                />
                <datalist v-if="sources.length" :id="sourceListId">
                    <option v-for="option in sources" :key="option" :value="option" />
                </datalist>
                <p v-if="hasError('source')" :id="'lead-source-error'" class="mt-2 text-sm text-rose-300">{{ form.errors.source }}</p>
            </div>

            <div>
                <label for="lead-status" class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Estado</label>
                <select
                    id="lead-status"
                    v-model="form.status"
                    class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-sm text-white focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200"
                    :aria-invalid="hasError('status')"
                    :aria-describedby="hasError('status') ? 'lead-status-error' : undefined"
                >
                    <option v-for="statusOption in statuses" :key="statusOption" :value="statusOption">{{ statusOption }}</option>
                </select>
                <p v-if="hasError('status')" :id="'lead-status-error'" class="mt-2 text-sm text-rose-300">{{ form.errors.status }}</p>
            </div>
        </div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 text-xs uppercase tracking-[0.3em] text-slate-400">
                <span>Atajos</span>
                <span aria-hidden="true">•</span>
                <span>Ctrl + Enter para guardar</span>
            </div>
            <div class="flex flex-wrap gap-3">
                <button
                    v-if="cancelHref"
                    type="button"
                    class="inline-flex items-center justify-center rounded-2xl border border-white/20 px-4 py-2 text-sm font-semibold text-white/80 hover:bg-white/10 transition"
                    @click="$emit('cancel', cancelHref)"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-violet-500 px-6 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-500/30 hover:bg-violet-600 focus:outline-none focus:ring-2 focus:ring-violet-200 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:cursor-not-allowed disabled:opacity-70"
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
        default: 'Guardar lead',
    },
    statuses: {
        type: Array,
        default: () => ['Nuevo', 'En Proceso', 'Convertido', 'Perdido'],
    },
    sources: {
        type: Array,
        default: () => ['Web', 'Recomendación', 'Campaña', 'Evento', 'Otros'],
    },
    cancelHref: {
        type: String,
        default: null,
    },
});

const sourceListId = computed(() => 'lead-source-options');

function hasError(field) {
    return Boolean(props.form.errors?.[field]);
}
</script>
