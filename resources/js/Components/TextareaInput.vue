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
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :rows="rows"
        :value="modelValue"
        @input="updateValue"
    />
</template>
