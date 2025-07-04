<template>
    <div class="flex items-center justify-center min-h-screen py-10 bg-gradient-to-bl from-cyan-400 via-blue-500 to-blue-800">
        <div class="w-full max-w-5xl p-8 bg-white rounded-xl shadow-lg">
            <h1 class="mb-8 text-3xl font-extrabold text-center text-blue-600">Registro de Compañía</h1>
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Datos de la Compañía -->
                    <div>
                        <h2 class="mb-6 text-xl font-bold text-gray-700">Datos de la Compañía</h2>
                        <div v-for="(field, key) in companyFields" :key="key" class="mb-6">
                            <label :for="key" class="block text-sm font-semibold text-gray-600">{{ field.label }}</label>
                            <input
                                v-model="form[key]"
                                :type="field.type"
                                :id="key"
                                :placeholder="field.placeholder"
                                class="w-full px-5 py-3 mt-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                :required="field.required"
                            />
                        </div>

                    </div>



                    <!-- Datos del Usuario -->
                    <div>
                        <h2 class="mb-6 text-xl font-bold text-gray-700">Datos del Usuario</h2>
                        <div v-for="(field, key) in userFields" :key="key" class="mb-6">
                            <label :for="key" class="block text-sm font-semibold text-gray-600">{{ field.label }}</label>
                            <input
                                v-model="form[key]"
                                :type="field.type"
                                :id="key"
                                :placeholder="field.placeholder"
                                class="w-full px-5 py-3 mt-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                :required="field.required"
                            />
                        </div>
                    </div>
                </div>

            <!--Seleccion del plan-->

                <div class="mb-6">
                    <h2 class="mb-6 text-xl font-bold text-gray-700">Selecciona un Plan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div
                            v-for="plan in props.plans"
                            :key="plan.id"
                            class="bg-white shadow-md rounded-lg p-6 relative"
                            :class="{ 'border-4 border-blue-500': form.plan_id === plan.id }"
                        >
                            <h2 class="text-xl text-blue-600 font-semibold mb-4">{{ plan.name }}</h2>
                            <p class="text-gray-600 mb-2">Precio: {{ plan.price }} €/ mes</p>
                            <p class="text-gray-500 text-sm mb-4">{{ plan.description }}</p>
                            <h3 class="text-lg font-semibold mb-2">Características</h3>
                            <ul class="mb-6">
                                <li v-for="feature in feturesByPlan(plan)" :key="feature.id" class="text-gray-700 flex items-center gap-2">
                                    <p>✔</p> {{ feature.name }}
                                </li>
                            </ul>
                            <button
                                v-if="form.plan_id !== plan.id"
                                @click="selectPlan(plan.id)"
                                type="button"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
                            >
                                Seleccionar
                            </button>
                            <span
                                v-else
                                class="absolute top-2 right-2 bg-green-100 text-green-600 px-2 py-1 rounded text-sm"
                            >
                                Plan actual
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Resumen del Pago -->
                <div class="mt-6">
                    <p class="text-lg font-semibold text-gray-800">Total a Pagar: <span class="text-blue-600">{{ price }}€</span></p>
                </div>

                <!-- Botón de envío -->
                <div class="mt-8">
                    <button
                        type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg"
                    >
                        Registrarse y Pagar
                    </button>
                </div>
            </form>

            <div class="mt-10">
                <NavLink :href="route('login')" class="text-sm text-gray-400">Ya tengo una cuenta, quiero iniciar sesion.</NavLink>

            </div>
        </div>
    </div>
</template>


<script setup>
import {reactive, ref, onMounted} from 'vue';
import { loadStripe } from '@stripe/stripe-js';
import NavLink from "../../../../vendor/laravel/jetstream/stubs/inertia/resources/js/Components/NavLink.vue";

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

onMounted(async () => {

});

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
