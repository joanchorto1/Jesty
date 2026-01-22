<template>
    <div class="min-h-screen bg-slate-50 px-6 py-12">
        <div class="mx-auto w-full max-w-6xl">
            <Panel
                title="Registro de Compañía"
                description="Completa la informació per crear l'espai corporatiu i activar el pla."
                :headerBorder="false"
                class="p-8 sm:p-10"
            >
                <form @submit.prevent="submitForm" class="space-y-10">
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-6">
                            <h2 class="mb-6 text-lg font-semibold text-slate-700">Datos de la Compañía</h2>
                            <div v-for="(field, key) in companyFields" :key="key" class="mb-5">
                                <InputLabel :for="key" :value="field.label" />
                                <TextInput
                                    v-model="form[key]"
                                    :type="field.type"
                                    :id="key"
                                    :placeholder="field.placeholder"
                                    class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm placeholder-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                    :required="field.required"
                                />
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-6">
                            <h2 class="mb-6 text-lg font-semibold text-slate-700">Datos del Usuario</h2>
                            <div v-for="(field, key) in userFields" :key="key" class="mb-5">
                                <InputLabel :for="key" :value="field.label" />
                                <TextInput
                                    v-model="form[key]"
                                    :type="field.type"
                                    :id="key"
                                    :placeholder="field.placeholder"
                                    class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm placeholder-slate-400 focus:border-sky-500 focus:ring-sky-500"
                                    :required="field.required"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-700">Selecciona un Plan</h2>
                            <p class="mt-1 text-sm text-slate-500">Escull el paquet que millor s'adapti a la teva organització.</p>
                        </div>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div
                                v-for="plan in props.plans"
                                :key="plan.id"
                                class="relative rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition hover:border-sky-200 hover:shadow-md"
                                :class="{ 'border-sky-200 ring-2 ring-sky-500': form.plan_id === plan.id }"
                            >
                                <h3 class="text-lg font-semibold text-slate-800 mb-2">{{ plan.name }}</h3>
                                <p class="text-sm text-slate-500 mb-1">Precio: <span class="font-semibold text-slate-700">{{ plan.price }} €</span> / mes</p>
                                <p class="text-sm text-slate-500 mb-4">{{ plan.description }}</p>
                                <h4 class="text-sm font-semibold text-slate-600 mb-2">Características</h4>
                                <ul class="mb-6 space-y-2 text-sm text-slate-600">
                                    <li v-for="feature in feturesByPlan(plan)" :key="feature.id" class="flex items-center gap-2">
                                        <span class="text-sky-500">✔</span>
                                        <span>{{ feature.name }}</span>
                                    </li>
                                </ul>
                                <SecondaryButton
                                    v-if="form.plan_id !== plan.id"
                                    @click="selectPlan(plan.id)"
                                    type="button"
                                    class="w-full justify-center"
                                >
                                    Seleccionar
                                </SecondaryButton>
                                <span
                                    v-else
                                    class="absolute top-4 right-4 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700"
                                >
                                    Plan actual
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-start gap-4 border-t border-slate-100 pt-6 md:flex-row md:items-center md:justify-between">
                        <p class="text-base font-semibold text-slate-700">
                            Total a Pagar: <span class="text-sky-600">{{ price }}€</span>
                        </p>
                        <PrimaryButton type="submit" class="w-full justify-center md:w-auto">
                            Registrarse y Pagar
                        </PrimaryButton>
                    </div>
                </form>
            </Panel>

            <div class="mt-6 text-center">
                <NavLink :href="route('login')" class="text-sm text-slate-500 hover:text-sky-600">
                    Ya tengo una cuenta, quiero iniciar sesion.
                </NavLink>
            </div>
        </div>
    </div>
</template>


<script setup>
import { reactive, ref } from 'vue';
import { loadStripe } from '@stripe/stripe-js';
import InputLabel from '@/Components/InputLabel.vue';
import NavLink from '@/Components/NavLink.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Panel from '@/Components/UI/Panel.vue';

const stripePromise = loadStripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY);

const form = reactive({
    company_name: '',
    company_nif: '',
    company_address: '',
    company_phone: '',
    company_email: '',
    plan_id: '',
    name: '',
    email: '',
    password: '',
    address: '',
    phone: '',
});


const price = ref(0);

const props = defineProps({
    plans: { type: Array, required: true },
    features: { type: Array, required: true },
    planFeatures: { type: Array, required: true },
});

const feturesByPlan = (plan) => {
    return props.planFeatures.filter(pf => pf.plan_id === plan.id).map(pf => {
        return props.features.find(f => f.id === pf.feature_id);
    });
};



const companyFields = {
    company_name: { label: 'Nombre', placeholder: 'Nombre de la compañía', type: 'text', required: true },
    company_nif: { label: 'NIF', placeholder: 'Número de Identificación Fiscal', type: 'text', required: true },
    company_address: { label: 'Dirección', placeholder: 'Dirección de la compañía', type: 'text', required: true },
    company_phone: { label: 'Teléfono', placeholder: 'Teléfono de contacto', type: 'text', required: true },
    company_email: { label: 'Email', placeholder: 'Email de la compañía', type: 'email', required: true },
};

const userFields = {
    name: { label: 'Nombre', placeholder: 'Nombre del usuario', type: 'text', required: true },
    email: { label: 'Email', placeholder: 'Email del usuario', type: 'email', required: true },
    password: { label: 'Contraseña', placeholder: 'Contraseña segura', type: 'password', required: true },
    address: { label: 'Dirección', placeholder: 'Dirección del usuario', type: 'text', required: true },
    phone: { label: 'Teléfono', placeholder: 'Teléfono del usuario', type: 'text', required: true },
};

// Actualiza el precio según el plan seleccionado
const updatePlanPrice = () => {
    const selectedPlan = props.plans.find(plan => plan.id === parseInt(form.plan_id));
    price.value = selectedPlan ? selectedPlan.price : 0;
};

// Método para seleccionar un plan
const selectPlan = (planId) => {
    form.plan_id = planId;
    updatePlanPrice();
};

// Enviar formulario
const submitForm = async () => {
    if (!form.plan_id) {
        alert('Selecciona un pla abans de continuar.');
        return;
    }

    try {
        const stripe = await stripePromise;

        const response = await axios.post(route('checkout.session'), {
            ...form,
        });

        console.log('Response:', response.data);

        if (response.data.sessionId) {
            const result = await stripe.redirectToCheckout({
                sessionId: response.data.sessionId
            });

            if (result.error) {
                console.error('Stripe redirect error:', result.error.message);
                alert('Error al redirigir a Stripe: ' + result.error.message);
            }
        } else {
            alert('Error: no s\'ha rebut el sessionId de Stripe.');
        }

    } catch (error) {
        console.error('Error en la sessió de pagament (catch):', error);
        alert('Hi ha hagut un problema amb el pagament.');
    }
};



</script>
