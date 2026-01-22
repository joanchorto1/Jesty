<template>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <slot name="head" />
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <slot />
                </tbody>
            </table>
        </div>
        <div v-if="$slots.footer" class="border-t border-slate-200 bg-slate-50 px-6 py-4">
            <slot name="footer" />
        </div>
    </div>
</template>
