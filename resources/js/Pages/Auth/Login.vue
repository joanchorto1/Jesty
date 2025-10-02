<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <div class="flex flex-col items-center gap-4 text-slate-200">
                <AuthenticationCardLogo />
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-sky-200/80">JCT Agency · Accés Intern</p>
            </div>
        </template>

        <div class="space-y-6 text-slate-200">
            <div class="space-y-2 text-center">
                <h1 class="text-2xl font-semibold tracking-tight">Inicia sessió a la suite corporativa</h1>
                <p class="text-sm leading-relaxed text-slate-300">
                    Accedeix a les eines internes de JCT Agency per coordinar projectes, equips i clients amb seguretat reforçada.
                </p>
            </div>

            <div v-if="status" class="rounded-xl border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-200">
                {{ status }}
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
                        placeholder="nom@jctagency.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Contrasenya" class="text-slate-200" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-2 block w-full rounded-2xl border-slate-600/70 bg-slate-900/40 px-4 py-3 text-slate-100 placeholder-slate-500 focus:border-sky-400 focus:ring-sky-400"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-300">
                        <Checkbox v-model:checked="form.remember" name="remember" class="rounded border-slate-500 bg-slate-900/60 text-sky-400 focus:ring-sky-400" />
                        Recorda'm
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm font-medium text-sky-200 transition hover:text-sky-100"
                    >
                        Has oblidat la contrasenya?
                    </Link>
                </div>

                <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Entrar
                </PrimaryButton>
            </form>
        </div>
    </AuthenticationCard>
</template>
