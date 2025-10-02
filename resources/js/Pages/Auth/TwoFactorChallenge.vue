<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <Head title="Two-factor Confirmation" />

    <AuthenticationCard>
        <template #logo>
            <div class="flex flex-col items-center gap-4 text-slate-200">
                <AuthenticationCardLogo />
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-sky-200/80">Autenticació robusta</p>
            </div>
        </template>

        <div class="space-y-6 text-slate-200">
            <div class="space-y-3 text-center">
                <h1 class="text-2xl font-semibold">Valida el teu segon factor</h1>
                <p class="text-sm leading-relaxed text-slate-300">
                    Introduïu el codi del vostre autenticador corporatiu per accedir als recursos de JCT Agency.
                </p>
            </div>

            <div v-if="recovery" class="rounded-xl border border-sky-400/40 bg-sky-500/10 px-4 py-3 text-sm leading-relaxed text-slate-200">
                Utilitza un codi de recuperació d'emergència si no tens el dispositiu d'autenticació.
            </div>

            <form class="space-y-6" @submit.prevent="submit">
                <div v-if="!recovery">
                    <InputLabel for="code" value="Codi d'autenticació" class="text-slate-200" />
                    <TextInput
                        id="code"
                        ref="codeInput"
                        v-model="form.code"
                        type="text"
                        inputmode="numeric"
                        class="mt-2 block w-full rounded-2xl border-slate-600/70 bg-slate-900/40 px-4 py-3 text-slate-100 placeholder-slate-500 focus:border-sky-400 focus:ring-sky-400"
                        autofocus
                        autocomplete="one-time-code"
                        placeholder="000000"
                    />
                    <InputError class="mt-2" :message="form.errors.code" />
                </div>

                <div v-else>
                    <InputLabel for="recovery_code" value="Codi de recuperació" class="text-slate-200" />
                    <TextInput
                        id="recovery_code"
                        ref="recoveryCodeInput"
                        v-model="form.recovery_code"
                        type="text"
                        class="mt-2 block w-full rounded-2xl border-slate-600/70 bg-slate-900/40 px-4 py-3 text-slate-100 placeholder-slate-500 focus:border-sky-400 focus:ring-sky-400"
                        autocomplete="one-time-code"
                        placeholder="xxxx-xxxx"
                    />
                    <InputError class="mt-2" :message="form.errors.recovery_code" />
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4">
                    <button
                        type="button"
                        class="text-sm font-medium text-sky-200 transition hover:text-sky-100"
                        @click.prevent="toggleRecovery"
                    >
                        <template v-if="recovery">
                            Use an authentication code
                        </template>

                        <template v-else>
                            Use a recovery code
                        </template>
                    </button>

                    <PrimaryButton class="px-8" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Validar accés
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticationCard>
</template>
