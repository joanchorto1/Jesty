<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    password: '',
});

const passwordInput = ref(null);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();

            passwordInput.value.focus();
        },
    });
};
</script>

<template>
    <Head title="Secure Area" />

    <AuthenticationCard>
        <template #logo>
            <div class="flex flex-col items-center gap-4 text-slate-200">
                <AuthenticationCardLogo />
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-sky-200/80">Validació necessària</p>
            </div>
        </template>

        <div class="space-y-6 text-slate-200">
            <div class="space-y-3 text-center">
                <h1 class="text-2xl font-semibold">Confirma la teva identitat</h1>
                <p class="text-sm leading-relaxed text-slate-300">
                    Per protegir els espais sensibles de JCT Agency, torna a introduir la contrasenya abans de continuar.
                </p>
            </div>

            <form class="space-y-6" @submit.prevent="submit">
                <div>
                    <InputLabel for="password" value="Contrasenya" class="text-slate-200" />
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-2 block w-full rounded-2xl border-slate-600/70 bg-slate-900/40 px-4 py-3 text-slate-100 placeholder-slate-500 focus:border-sky-400 focus:ring-sky-400"
                        required
                        autocomplete="current-password"
                        autofocus
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Confirmar accés
                </PrimaryButton>
            </form>
        </div>
    </AuthenticationCard>
</template>
