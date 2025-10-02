<template>
    <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-[#050915] via-[#0B1728] to-[#132235] px-6 py-12 sm:px-8">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-32 -left-24 h-80 w-80 rounded-full bg-sky-500/20 blur-3xl" />
            <div class="absolute -bottom-24 -right-16 h-96 w-96 rounded-full bg-blue-700/20 blur-3xl" />
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(56,189,248,0.12),_transparent_55%)]" />
        </div>

        <div class="relative mx-auto flex min-h-full w-full max-w-xl flex-col items-center justify-center">
            <div class="flex flex-col items-center text-center">
                <slot name="logo" />
            </div>

            <div class="mt-10 w-full rounded-3xl border border-white/10 bg-white/5 p-10 shadow-2xl backdrop-blur-xl">
                <slot />
            </div>
        </div>
    </div>
</template>
