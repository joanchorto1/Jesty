<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    rows: {
        type: Number,
        default: 3,
    },
});

const emit = defineEmits(['update:modelValue']);
const textarea = ref(null);

onMounted(() => {
    if (textarea.value?.hasAttribute('autofocus')) {
        textarea.value.focus();
    }
});

const updateValue = (event) => emit('update:modelValue', event.target.value);
</script>

<template>
    <textarea
        ref="textarea"
        class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-200"
        :rows="rows"
        :value="modelValue"
        @input="updateValue"
    />
</template>
