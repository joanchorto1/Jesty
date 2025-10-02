<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />

    <AuthenticationCard>
        <template #logo>
            <div class="flex flex-col items-center gap-4 text-slate-200">
                <AuthenticationCardLogo />
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-sky-200/80">Seguretat reforçada</p>
            </div>
        </template>

        <div class="space-y-6 text-slate-200">
            <div class="space-y-3 text-center">
                <h1 class="text-2xl font-semibold">Crea una nova contrasenya</h1>
                <p class="text-sm leading-relaxed text-slate-300">
                    Defineix una nova credencial robusta per assegurar l'accés als entorns interns de JCT Agency.
                </p>
            </div>

            <form class="space-y-6" @submit.prevent="submit">
                <div>
                    <InputLabel for="email" value="Correu electrònic" class="text-slate-200" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-2 block w-full rounded-2xl border-slate-600/70 bg-slate-900/40 px-4 py-3 text-slate-100 placeholder-slate-500 focus:border-sky-400 focus:ring-sky-400"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Nova contrasenya" class="text-slate-200" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-2 block w-full rounded-2xl border-slate-600/70 bg-slate-900/40 px-4 py-3 text-slate-100 placeholder-slate-500 focus:border-sky-400 focus:ring-sky-400"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirma la contrasenya" class="text-slate-200" />
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-2 block w-full rounded-2xl border-slate-600/70 bg-slate-900/40 px-4 py-3 text-slate-100 placeholder-slate-500 focus:border-sky-400 focus:ring-sky-400"
                        required
                        autocomplete="new-password"
                        placeholder="Repeteix la contrasenya"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Guardar nova contrasenya
                </PrimaryButton>
            </form>
        </div>
    </AuthenticationCard>
</template>
