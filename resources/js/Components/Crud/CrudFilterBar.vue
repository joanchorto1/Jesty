<template>
    <section class="rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex flex-col gap-4 md:flex-row md:flex-wrap md:items-end">
            <slot />
            <div class="md:ml-auto">
                <slot name="actions" />
            </div>
        </div>
    </section>
</template>
