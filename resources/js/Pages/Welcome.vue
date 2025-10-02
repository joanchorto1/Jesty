<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome" />

    <div class="relative min-h-screen overflow-hidden bg-gradient-to-b from-[#050915] via-[#081224] to-[#0F1A2D] text-slate-100">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-20 -left-20 h-96 w-96 rounded-full bg-sky-500/15 blur-3xl" />
            <div class="absolute top-1/2 right-[-12rem] h-[32rem] w-[32rem] -translate-y-1/2 rounded-full bg-blue-700/20 blur-3xl" />
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(56,189,248,0.12),_transparent_55%)]" />
        </div>

        <div v-if="canLogin" class="relative z-10 flex justify-end p-8">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="rounded-full border border-white/20 bg-white/10 px-6 py-2 text-sm font-semibold uppercase tracking-[0.2em] text-slate-100 backdrop-blur-md transition hover:bg-white/20"
            >
                Tauler de control
            </Link>

            <Link
                v-else
                :href="route('login')"
                class="rounded-full border border-white/20 bg-white/10 px-6 py-2 text-sm font-semibold uppercase tracking-[0.2em] text-slate-100 backdrop-blur-md transition hover:bg-white/20"
            >
                Inicia sessió
            </Link>
        </div>

        <div class="relative z-10 mx-auto flex max-w-6xl flex-col gap-24 px-6 pb-24 pt-12 sm:px-10 lg:px-16">
            <section class="grid grid-cols-1 items-center gap-12 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="space-y-8">
                    <ApplicationLogo class="w-full max-w-md" />
                    <h1 class="text-4xl font-bold tracking-tight text-slate-50 sm:text-5xl">
                        La plataforma integral de gestió empresarial de <span class="text-sky-300">JCT Agency</span>.
                    </h1>
                    <p class="text-lg leading-relaxed text-slate-300">
                        Centralitza clients, projectes, finances i equips en una sola suite corporativa. Construïda per a la nostra agència i optimitzada per oferir una experiència premium a cada departament.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <Link
                            v-if="canLogin"
                            :href="$page.props.auth.user ? route('dashboard') : route('login')"
                            class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-sky-500 via-sky-600 to-blue-700 px-8 py-3 text-sm font-semibold uppercase tracking-[0.24em] text-white shadow-lg shadow-sky-900/30 transition hover:from-sky-400 hover:via-sky-500 hover:to-blue-600"
                        >
                            Accedir a la suite
                        </Link>
                        <div class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-6 py-3 text-sm font-semibold uppercase tracking-[0.24em] text-slate-200 backdrop-blur">
                            SaaS privat multiempresa
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-0 rounded-3xl border border-white/10 bg-gradient-to-br from-white/10 via-white/5 to-transparent blur-md" />
                    <div class="relative rounded-3xl border border-white/10 bg-white/10 p-8 backdrop-blur-xl shadow-2xl shadow-black/30">
                        <h2 class="text-xl font-semibold uppercase tracking-[0.3em] text-sky-200">Visió 360º</h2>
                        <p class="mt-4 text-base leading-relaxed text-slate-200">
                            Dashboards unificats, analítica en temps real i fluxos aprovats per JCT Agency per coordinar equips de màrqueting, vendes i operacions amb garanties de seguretat empresarial.
                        </p>
                        <ul class="mt-6 space-y-4 text-sm text-slate-300">
                            <li class="flex items-center gap-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-500/20 text-sky-300">CRM</span>
                                Control de relacions i oportunitats en un pipeline únic.
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600/20 text-blue-300">Finances</span>
                                Facturació, pressupostos i tresoreria sincronitzats amb la direcció.
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-500/20 text-cyan-300">Operacions</span>
                                RRHH, inventari i suport coordinats amb fluxos aprovats.
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="grid gap-10 md:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-8 backdrop-blur-xl shadow-lg shadow-black/30">
                    <h3 class="text-lg font-semibold text-slate-50">Identitat Corporativa</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-300">
                        Cada detall visual reflecteix l'ADN de JCT Agency. El nostre equip opera amb una interfície coherent que reforça la marca a cada interacció.
                    </p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-8 backdrop-blur-xl shadow-lg shadow-black/30">
                    <h3 class="text-lg font-semibold text-slate-50">Control Total Intern</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-300">
                        Accés restringit i sense autroregistre extern. Gestionem equips i permisos des del nucli de l'agència per garantir confidencialitat i governança.
                    </p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-8 backdrop-blur-xl shadow-lg shadow-black/30 md:col-span-2 xl:col-span-1">
                    <h3 class="text-lg font-semibold text-slate-50">Tecnologia de confiança</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-300">
                        Construïda sobre Laravel {{ laravelVersion }} i PHP {{ phpVersion }}, amb processos optimitzats per desplegar evolucions ràpides sense comprometre l'estabilitat.
                    </p>
                </div>
            </section>
        </div>
    </div>
</template>

