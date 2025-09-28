<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Compañía</h2>
                        <p class="text-blue-300 text-xl">{{ company.name }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Usuarios</h2>
                        <p class="text-blue-300 text-2xl">{{ users.length }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Módulos activos</h2>
                        <p class="text-blue-300 text-2xl">{{ modules.length }}</p>
                    </div>
                </div>
            </div>

            <!-- Información de la compañía -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg text-blue-500 font-semibold">Información de la Empresa</h2>
                    <NavLink :href="route('companies.edit',company.id)"  class=" text-white font-bold py-2 px-4 ">
                        <EditIcon class="w-6 h-6 fill-gray-200" />
                    </NavLink>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-700"><span class="font-semibold">Nombre:</span> {{ company.name }}</p>
                        <p class="text-gray-700"><span class="font-semibold">NIF:</span> {{ company.nif }}</p>
                        <p class="text-gray-700"><span class="font-semibold">Teléfono:</span> {{ company.phone }}</p>
                    </div>
                    <div>
                        <p class="text-gray-700"><span class="font-semibold">Correo Electrónico:</span> {{ company.email }}</p>
                        <p class="text-gray-700"><span class="font-semibold">Dirección:</span> {{ company.address }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg text-blue-500 font-semibold">Configuración de Correo</h2>
                    <NavLink :href="route('email-configurations.edit', emailConfig.id)" class="text-white font-bold py-2 px-4">
                        <EditIcon class="w-6 h-6 fill-gray-200" />
                    </NavLink>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-700"><span class="font-semibold">Empresa:</span> {{ company.name }}</p>
                        <p class="text-gray-700"><span class="font-semibold">Correo Remitente:</span> {{ emailConfig.from_email }}</p>
                        <p class="text-gray-700"><span class="font-semibold">Nombre Remitente:</span> {{ emailConfig.from_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-700"><span class="font-semibold">SMTP Host:</span> {{ emailConfig.smtp_host }}</p>
                        <p class="text-gray-700"><span class="font-semibold">SMTP Puerto:</span> {{ emailConfig.smtp_port }}</p>
                        <p class="text-gray-700"><span class="font-semibold">Usuario SMTP:</span> {{ emailConfig.smtp_username }}</p>
                    </div>
                </div>
            </div>

<!--            Informacion de las keys-->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg text-blue-500 font-semibold">Información de las Keys</h2>
                    <NavLink :href="route('companies.showKeys',company.id)" class=" text-white font-bold py-2 px-4 ">
                        <EditIcon class="w-6 h-6 fill-gray-200" />
                    </NavLink>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-700 line-clamp-2"><span class="font-semibold">Public Key:</span> {{ company.public_key }}</p>
                    </div>
                    <div>
                        <p class="text-gray-700 line-clamp-2"><span class="font-semibold">Private Key:</span> {{ company.private_key }}</p>
                    </div>
                </div>
            </div>



            <div  class="bg-white p-6 rounded-lg space-y-6 shadow-md mb-6">
                <h4 class="text-blue-500 font-bold text-lg">Dar de baja la empresa</h4>
                <p class="text-sm text-gray-500">Esa accion consiste en la eliminación de todos los datos relacionados con la empresa del sistema JesTy (usuarios, facturas, igresos, gastos...).
                <strong class="text-red-600">NO es una accion reversible.</strong></p>
                <button @click="deleteCompany" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                    Eliminar Empresa
                </button>
            </div>


        </div>
    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import NavLink from "@/Components/NavLink.vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";

// Definición de props
const props = defineProps({
    company: Object,
    users: Array,
    roles: Array,
    emailConfig: Object,
    modules: Array,
});

console.log(props.emailConfig);


const deleteCompany =() => {
    if (confirm("¿Estás seguro de que deseas eliminar esta compañía?")) {
        Inertia.delete(route('companies.destroy', props.company.id));
    }
};
</script>

<style scoped>
/* Estilos personalizados aquí */
</style>
