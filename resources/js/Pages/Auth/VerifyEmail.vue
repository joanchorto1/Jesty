<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Email Verification" />

    <AuthenticationCard>
        <template #logo>
            <div class="flex flex-col items-center gap-4 text-slate-700">
                <AuthenticationCardLogo />
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-slate-400">Verificació corporativa</p>
            </div>
        </template>

        <div class="space-y-6 text-slate-700">
            <div class="space-y-3 text-center">
                <h1 class="text-2xl font-semibold text-slate-800">Confirma el teu correu JCT</h1>
                <p class="text-sm leading-relaxed text-slate-500">
                    Hem enviat un enllaç de verificació al teu correu corporatiu. Revisa la safata d'entrada i segueix les instruccions per validar l'accés.
                </p>
            </div>

            <div v-if="verificationLinkSent" class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-medium text-sky-700">
                Hem reenviat l'enllaç de verificació a l'adreça registrada.
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Reenviar correu de verificació
                </PrimaryButton>

                <div class="flex flex-wrap justify-center gap-4 text-sm font-medium">
                    <Link
                        :href="route('profile.show')"
                        class="text-slate-500 underline-offset-4 transition hover:text-sky-600 hover:underline"
                    >
                        Actualitzar dades de perfil
                    </Link>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-slate-500 underline-offset-4 transition hover:text-sky-600 hover:underline"
                    >
                        Tancar sessió
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticationCard>
</template>
