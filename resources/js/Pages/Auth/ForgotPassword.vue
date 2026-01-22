<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <AuthenticationCard>
        <template #logo>
            <div class="flex flex-col items-center gap-4 text-slate-700">
                <AuthenticationCardLogo />
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-slate-400">Recuperació d'accés</p>
            </div>
        </template>

        <div class="space-y-6 text-slate-700">
            <div class="space-y-3 text-center">
                <h1 class="text-2xl font-semibold text-slate-800">Restableix la teva contrasenya</h1>
                <p class="text-sm leading-relaxed text-slate-500">
                    Introdueix el teu correu corporatiu i t'enviarem un enllaç segur per crear una nova contrasenya per a la suite JCT Agency.
                </p>
            </div>

            <div v-if="status" class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-medium text-sky-700">
                {{ status }}
            </div>

            <form class="space-y-6" @submit.prevent="submit">
                <div>
                    <InputLabel for="email" value="Correu electrònic" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-800 shadow-sm placeholder-slate-400 focus:border-sky-500 focus:ring-sky-500"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="nom@jctagency.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Enviar enllaç de restabliment
                </PrimaryButton>
            </form>
        </div>
    </AuthenticationCard>
</template>
