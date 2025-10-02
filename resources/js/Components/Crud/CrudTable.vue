<template>
    <div class="overflow-hidden rounded-3xl border border-slate-200/70 bg-white/90 shadow-sm backdrop-blur">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50/80">
                    <slot name="head" />
                </thead>
                <tbody class="divide-y divide-slate-200/70 bg-white/50">
                    <slot />
                </tbody>
            </table>
        </div>
        <div v-if="$slots.footer" class="border-t border-slate-200/70 bg-slate-50/80 px-6 py-4">
            <slot name="footer" />
        </div>
    </div>
</template>
