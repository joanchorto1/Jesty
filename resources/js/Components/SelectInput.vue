<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean, Object, Array, null],
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);
const select = ref(null);

onMounted(() => {
    if (select.value?.hasAttribute('autofocus')) {
        select.value.focus();
    }
});

const updateValue = (event) => {
    emit('update:modelValue', event.target.value);
};
</script>

<template>
    <select
        ref="select"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        :disabled="disabled"
        @input="updateValue"
    >
        <slot />
    </select>
</template>
