<template>
    <section class="rounded-3xl border border-slate-200/70 bg-white/90 p-6 shadow-sm backdrop-blur">
        <div class="flex flex-col gap-4 md:flex-row md:flex-wrap md:items-end">
            <slot />
            <div class="md:ml-auto">
                <slot name="actions" />
            </div>
        </div>
    </section>
</template>
