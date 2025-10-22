<script setup>
import { computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Actualitzar estat',
    },
    documentName: {
        type: String,
        default: '',
    },
    currentStatusLabel: {
        type: String,
        default: '',
    },
    currentStatusClass: {
        type: String,
        default: '',
    },
    options: {
        type: Array,
        default: () => [],
    },
    modelValue: {
        type: String,
        default: '',
    },
    loading: {
        type: Boolean,
        default: false,
    },
    confirmDisabled: {
        type: Boolean,
        default: false,
    },
    confirmLabel: {
        type: String,
        default: 'Actualitzar estat',
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['close', 'confirm', 'update:modelValue']);

const selected = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const computedCurrentStatusClass = computed(() => {
    if (props.currentStatusClass) {
        return props.currentStatusClass;
    }

    return 'inline-flex items-center rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-300/60 shadow-sm';
});
</script>

<template>
    <Modal :show="show" max-width="lg" @close="emit('close')">
        <div class="relative rounded-lg bg-white p-6 shadow-2xl">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        {{ title || 'Actualitzar estat' }}
                    </h2>
                    <p v-if="documentName" class="mt-1 text-sm text-slate-500">
                        Document seleccionat: <span class="font-medium text-slate-700">{{ documentName }}</span>
                    </p>
                </div>
                <button
                    type="button"
                    class="-mr-2 -mt-2 inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                    @click="emit('close')"
                    :disabled="loading"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 0 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-100 bg-slate-50 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">Estat actual</p>
                <span :class="[computedCurrentStatusClass, 'mt-3 w-fit']">
                    {{ currentStatusLabel || 'Sense estat' }}
                </span>
            </div>

            <div class="mt-6">
                <p class="text-sm font-medium text-slate-600">Selecciona un nou estat disponible:</p>
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <button
                        v-for="option in options"
                        :key="option.value"
                        type="button"
                        class="flex flex-col gap-3 rounded-2xl border bg-white p-4 text-left shadow-sm transition"
                        :class="[
                            option.value === selected
                                ? 'border-sky-500 ring-2 ring-sky-200/60'
                                : 'border-slate-200 hover:border-sky-400 hover:shadow-md',
                        ]"
                        @click="selected = option.value"
                        :disabled="loading"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-700">{{ option.label }}</span>
                            <span v-if="option.value === selected" class="text-sky-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.338a1 1 0 0 1-1.434.012l-3.492-3.5a1 1 0 1 1 1.414-1.414l2.772 2.778 6.536-6.613a1 1 0 0 1 1.448-.015Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        <span
                            :class="[
                                'inline-flex w-fit items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset',
                                option.badgeClass || 'bg-slate-100 text-slate-600 ring-slate-300/60',
                            ]"
                        >
                            {{ option.label }}
                        </span>
                        <p v-if="option.description" class="text-xs leading-relaxed text-slate-500">
                            {{ option.description }}
                        </p>
                    </button>
                </div>
                <p v-if="error" class="mt-2 text-sm text-rose-600">{{ error }}</p>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3">
                <SecondaryButton type="button" @click="emit('close')" :disabled="loading">
                    Cancel·lar
                </SecondaryButton>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-sky-500 via-sky-600 to-blue-700 px-5 py-3 text-sm font-semibold uppercase tracking-[0.2em] text-white shadow-lg shadow-sky-900/30 transition ease-in-out duration-200 disabled:cursor-not-allowed disabled:opacity-70"
                    :disabled="loading || confirmDisabled"
                    @click="emit('confirm')"
                >
                    <svg
                        v-if="loading"
                        class="h-5 w-5 animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span>{{ confirmLabel }}</span>
                </button>
            </div>
        </div>
    </Modal>
</template>
