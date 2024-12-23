<template>
    <AppLayout>
        <div class="bg-gray-100 min-h-screen py-10 ">
            <div class="w-auto mx-10  bg-white shadow-md rounded-lg p-8">
                <!-- Título -->
                <h1 class="text-3xl font-bold text-blue-500 mb-10">Detalles de la Nómina</h1>

                <!-- Datos del Empleado -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class=""><strong class="text-gray-500  font-normal text-sm"> Empleado:</strong> {{ employee.name }}</p>
                        <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Cédula:</strong> {{ employee.id_number }}</p>
                        <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Departamento:</strong> {{ employee.department }}</p>
                    </div>
                    <div>
                        <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Fecha de Ingreso:</strong> {{ employee.hire_date }}</p>
                        <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Email:</strong> {{ employee.email }}</p>
                    </div>
                </div>

                <!-- Fechas de la Nómina -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Fecha de Inicio:</strong> {{ payroll.start_date }}</p>
                        <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Fecha de Fin:</strong> {{ payroll.end_date }}</p>
                    </div>
                    <div>
                        <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Fecha de Pago:</strong> {{ payroll.payment_date }}</p>
                    </div>
                </div>

                <!-- Ingresos -->
                <div class="border-t pt-6 mt-6 mb-6">
                    <h3 class="text-xl font-semibold text-blue-500">Ingresos</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Sueldo Base:</strong> {{ payroll.base_salary | currency }}</p>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Horas Extras:</strong> {{ payroll.overtime | currency }}</p>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Bonificaciones:</strong> {{ payroll.bonuses | currency }}</p>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Comisiones:</strong> {{ payroll.commissions | currency }}</p>
                        </div>
                        <div>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Prima Vacacional:</strong> {{ payroll.vacation_bonus | currency }}</p>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Prima Anual:</strong> {{ payroll.annual_bonus | currency }}</p>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Otros Ingresos:</strong> {{ payroll.other_earnings | currency }}</p>
                            <p class="text-md font-semibold"><strong class="text-gray-500 text-md font-normal text-md"> Total de Ingresos:</strong> {{ payroll.total_earnings | currency }}</p>
                        </div>
                    </div>
                </div>

                <!-- Deducciones -->
                <div class="border-t pt-6 mt-6 mb-6">
                    <h3 class="text-xl font-semibold text-blue-500">Deducciones</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Impuesto sobre la Renta (IRPF):</strong> {{ payroll.income_tax | currency }}</p>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Seguridad Social:</strong> {{ payroll.social_security | currency }}</p>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Fondo de Vivienda:</strong> {{ payroll.housing_fund | currency }}</p>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Préstamos:</strong> {{ payroll.loans | currency }}</p>
                        </div>
                        <div>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Fondo de Ahorro:</strong> {{ payroll.savings_fund | currency }}</p>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Aportaciones Sindicales:</strong> {{ payroll.union_dues | currency }}</p>
                            <p class="text-sm"><strong class="text-gray-500 text-md font-normal text-md"> Otros Deducciones:</strong> {{ payroll.other_deductions | currency }}</p>
                            <p class="text-md font-semibold"><strong class="text-gray-500 text-md font-normal text-md"> Total Deducciones:</strong> {{ payroll.total_deductions | currency }}</p>
                        </div>
                    </div>
                </div>

                <!-- Neto a Pagar -->
                <div class="border-t pt-6 mt-6 mb-6">
                    <h3 class="text-xl font-semibold text-blue-500">Pago Neto</h3>
                    <p class="text-md font-semibold text-blue-600"><strong class="text-gray-500 text-md font-normal text-md"> Total Neto a Pagar:</strong> {{ payroll.net_pay | currency }}</p>
                </div>

                <!-- Botones de acciones -->
                <div class="mt-8 flex  border-t space-x-4">
                    <NavLink :href="route('payrolls.print', payroll.id)" title="Imprimir Nómina">
                        <PrintIcon class="w-8 h-8 text-blue-600 hover:text-blue-800" />
                    </NavLink>
                    <button @click="sendPayroll" title="Enviar Nómina por Correo">
                        <SendIcon class="w-7 h-7 fill-gray-950 text-blue-600 hover:text-blue-800" />
                    </button>
                    <NavLink :href="route('payrolls.edit', payroll.id)" title="Editar Nómina">
                        <EditIcon class="h-6 w-6 text-blue-600 hover:text-blue-800" />
                    </NavLink>
                    <button @click="confirmDelete" title="Eliminar Nómina">
                        <DeleteIcon class="h-6 w-6 text-blue-600 hover:text-blue-800" />
                    </button>
                </div>
            </div>

            <!-- Popup de notificación -->
            <div v-if="popupVisible" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white rounded-lg p-6 shadow-lg w-96 text-center">
                    <h2 class="text-md font-semibold text-blue-500 mb-4">Enviando nómina...</h2>
                    <p v-if="popupMessage">{{ popupMessage }}</p>
                    <button
                        v-if="popupMessage"
                        class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                        @click="closePopup"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import PrintIcon from "@/Components/Icons/PrintIcon.vue";
import SendIcon from "@/Components/Icons/SendIcon.vue";
import NavLink from "@/Components/NavLink.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";

// Props
const props = defineProps({
    payroll: Object,
    employee: Object
});

// Popup de notificación
const popupVisible = ref(false);
const popupMessage = ref("");

// Función para enviar la nómina por correo
const sendPayroll = async () => {
    popupVisible.value = true;
    popupMessage.value = "";
    try {
        await Inertia.get(route("payrolls.send", props.payroll.id));
        popupMessage.value = "¡Nómina enviada correctamente!";
    } catch (error) {
        popupMessage.value = "No se pudo enviar la nómina.";
    }
};

// Cerrar el popup
const closePopup = () => {
    popupVisible.value = false;
    popupMessage.value = "";
};

// Filtro de formato de moneda
const currency = (value) => {
    return new Intl.NumberFormat("es-ES", {
        style: "currency",
        currency: "EUR",
    }).format(value);
};

// Confirmar eliminación de la nómina
const confirmDelete = () => {
    if (confirm("¿Estás seguro de que quieres eliminar esta nómina?")) {
        Inertia.delete(route('payrolls.destroy', props.payroll.id));
    }
};
</script>

<style scoped>
/* Ajuste de estilos */
.text-md {
    line-height: 1.75rem;
}
</style>
