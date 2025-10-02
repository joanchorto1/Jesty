<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import MenuBillingIcon from '@/Components/Icons/MenuBillingIcon.vue';
import MenuInventoryIcon from '@/Components/Icons/MenuInventoryIcon.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    address: '',
});

const submit = () => {
    form.post(route('suppliers.store'));
};
</script>

<template>
    <AppLayout title="Nou proveïdor">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-5xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Afegeix un nou proveïdor"
                    description="Centralitza la informació de contacte i facturació per garantir una relació fluïda amb la cadena de subministrament."
                    :icon="MenuBillingIcon"
                />

                <div class="grid gap-6 md:grid-cols-2">
                    <CrudStatCard
                        label="Canals de contacte"
                        :value="form.email || form.phone ? 'Configurats' : 'Pendents'"
                        :icon="MenuInventoryIcon"
                        icon-background="bg-sky-500/10 text-sky-600"
                    />
                    <CrudStatCard
                        label="Ubicació"
                        :value="form.address || 'Sense adreça'"
                        :icon="MenuBillingIcon"
                        icon-background="bg-indigo-500/10 text-indigo-600"
                    />
                </div>

                <FormSection @submitted="submit">
                    <template #title>
                        Fitxa del proveïdor
                    </template>
                    <template #description>
                        Defineix les dades principals per sincronitzar compres, inventari i facturació.
                    </template>

                    <template #form>
                        <div class="col-span-6 space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="name" value="Nom comercial" />
                                    <TextInput id="name" v-model="form.name" type="text" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="email" value="Correu electrònic" />
                                    <TextInput id="email" v-model="form.email" type="email" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.email" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="phone" value="Telèfon" />
                                    <TextInput id="phone" v-model="form.phone" type="tel" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.phone" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="address" value="Adreça fiscal" />
                                    <TextInput id="address" v-model="form.address" type="text" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.address" class="mt-2" />
                                </div>

                            </div>
                        </div>
                    </template>

                    <template #actions>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Crear proveïdor
                        </PrimaryButton>
                    </template>
                </FormSection>
            </div>
        </div>
    </AppLayout>
</template>
