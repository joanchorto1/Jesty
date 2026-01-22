<script setup>
import Panel from '@/Components/UI/Panel.vue';
</script>

<template>
    <div class="min-h-screen bg-slate-50 px-6 py-12 sm:px-8">
        <div class="mx-auto flex min-h-full w-full max-w-xl flex-col items-center justify-center gap-8">
            <div class="flex flex-col items-center text-center">
                <slot name="logo" />
            </div>

            <Panel :headerBorder="false" class="w-full p-8 sm:p-10">
                <slot />
            </Panel>
        </div>
    </div>
</template>
