<template>
    <div class="w-full">
        <div class="flex flex-wrap items-center justify-center gap-6">
            <div v-for="(step, index) in steps" :key="step.label" class="flex items-center gap-4">
                <div class="relative">
                    <div class="h-12 w-12 rounded-full border-2" :class="stepClasses(index)">
                        <div class="absolute inset-0 flex items-center justify-center text-sm font-semibold" :class="stepTextClasses(index)">
                            {{ index + 1 }}
                        </div>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold" :class="labelClasses(index)">{{ step.label }}</p>
                    <p v-if="step.description" class="text-xs text-slate-400 max-w-[14rem]">{{ step.description }}</p>
                </div>
                <div v-if="index < steps.length - 1" class="hidden sm:block w-12 h-px bg-gradient-to-r from-violet-500/40 to-violet-500/80"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    steps: {
        type: Array,
        default: () => [],
    },
    activeIndex: {
        type: Number,
        default: 0,
    },
});

const stepClasses = index => [
    'flex items-center justify-center border-violet-300/60',
    props.activeIndex >= index ? 'bg-violet-500 text-white shadow-lg shadow-violet-500/40' : 'bg-slate-900 text-slate-400',
];

const stepTextClasses = index => [
    props.activeIndex >= index ? 'text-white' : 'text-slate-400',
];

const labelClasses = index => [
    'uppercase tracking-widest text-xs',
    props.activeIndex >= index ? 'text-violet-200' : 'text-slate-400',
];
</script>
