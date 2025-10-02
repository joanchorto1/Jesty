<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import MenuRRHHIcon from '@/Components/Icons/MenuRRHHIcon.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';

const props = defineProps({
    departments: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    address: '',
    job_title: '',
    department_id: '',
    salary: '',
    hire_date: '',
    status: 'activo',
    dni: '',
    nnss: '',
    iban: '',
    birth_date: '',
});

const submit = () => {
    form.post(route('employees.store'));
};
</script>

<template>
    <AppLayout title="Nou empleat">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-6xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Incorpora un nou talent"
                    description="Completa la fitxa de l'empleat perquè RRHH, finances i la resta d'equips comparteixin la mateixa informació."
                    :icon="MenuRRHHIcon"
                />

                <div class="grid gap-6 md:grid-cols-3">
                    <CrudStatCard
                        label="Equips disponibles"
                        :value="departments.length"
                        :icon="MenuRRHHIcon"
                        icon-background="bg-indigo-500/10 text-indigo-600"
                    />
                    <CrudStatCard
                        label="Estat inicial"
                        :value="form.status === 'activo' ? 'Actiu' : 'Inactiu'"
                        :icon="AddIcon"
                        icon-background="bg-emerald-500/10 text-emerald-600"
                    />
                    <CrudStatCard
                        label="Data d'alta"
                        :value="form.hire_date || '—'"
                        :icon="MenuRRHHIcon"
                        icon-background="bg-sky-500/10 text-sky-600"
                    />
                </div>

                <FormSection @submitted="submit">
                    <template #title>
                        Fitxa de l'empleat
                    </template>
                    <template #description>
                        Agrupa les dades personals, contractuals i bancàries per agilitzar els processos interns.
                    </template>

                    <template #form>
                        <div class="col-span-6 space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Identificació i contacte</h3>
                                <p class="mt-1 text-sm text-slate-500">Informació bàsica perquè l'equip pugui contactar amb l'empleat.</p>
                            </div>
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="name" value="Nom complet" />
                                    <TextInput id="name" v-model="form.name" type="text" class="mt-2 block w-full" autocomplete="off" />
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
                                    <InputLabel for="address" value="Adreça" />
                                    <TextInput id="address" v-model="form.address" type="text" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.address" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="dni" value="DNI" />
                                    <TextInput id="dni" v-model="form.dni" type="text" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.dni" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="birth_date" value="Data de naixement" />
                                    <TextInput id="birth_date" v-model="form.birth_date" type="date" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.birth_date" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="col-span-6 space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Dades laborals</h3>
                                <p class="mt-1 text-sm text-slate-500">Defineix el rol, l'equip i la situació contractual.</p>
                            </div>
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="job_title" value="Càrrec" />
                                    <TextInput id="job_title" v-model="form.job_title" type="text" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.job_title" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="department_id" value="Departament" />
                                    <SelectInput id="department_id" v-model="form.department_id" class="mt-2 block w-full">
                                        <option value="">Selecciona un departament</option>
                                        <option v-for="department in departments" :key="department.id" :value="department.id">
                                            {{ department.name }}
                                        </option>
                                    </SelectInput>
                                    <InputError :message="form.errors.department_id" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="hire_date" value="Data d'incorporació" />
                                    <TextInput id="hire_date" v-model="form.hire_date" type="date" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.hire_date" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="status" value="Estat" />
                                    <SelectInput id="status" v-model="form.status" class="mt-2 block w-full">
                                        <option value="activo">Actiu</option>
                                        <option value="inactivo">Inactiu</option>
                                    </SelectInput>
                                    <InputError :message="form.errors.status" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="salary" value="Sou anual (€)" />
                                    <TextInput id="salary" v-model="form.salary" type="number" min="0" step="0.01" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.salary" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="col-span-6 space-y-6 rounded-3xl border border-slate-200/80 bg-white/70 p-6 shadow-sm">
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Informació administrativa</h3>
                                <p class="mt-1 text-sm text-slate-500">Necessària per a nòmines i tràmits oficials.</p>
                            </div>
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="nnss" value="Número de Seguretat Social" />
                                    <TextInput id="nnss" v-model="form.nnss" type="text" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.nnss" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <InputLabel for="iban" value="IBAN" />
                                    <TextInput id="iban" v-model="form.iban" type="text" class="mt-2 block w-full" />
                                    <InputError :message="form.errors.iban" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </template>

                    <template #actions>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Crear empleat
                        </PrimaryButton>
                    </template>
                </FormSection>
            </div>
        </div>
    </AppLayout>
</template>
