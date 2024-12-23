<template>
    <AppLayout>
        <div class="container mx-auto p-6">
            <h2 class="text-2xl text-blue-500 font-semibold text-center mb-6">Editar Nómina</h2>

            <form @submit.prevent="submitPayroll" class="space-y-6">
                <!-- Datos del empleado -->
                <div class="bg-gray-100 p-4 rounded-lg">
                    <h3 class="text-xl font-semibold mb-4 text-blue-500">Datos del Empleado</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Employee ID -->
                        <div class="mb-4">
                            <label for="employee_id" class="block text-sm font-medium text-gray-700">Empleado</label>
                            <select v-model="payroll.employee_id" id="employee_id" class="w-full p-2 mt-2 border border-gray-300 rounded-lg" required>
                                <option v-for="employee in props.employees" :key="employee.id" :value="employee.id">{{ employee.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                            <input type="date" id="start_date" v-model="payroll.start_date" class="mt-2 w-full p-2 border rounded-md" required />
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                            <input type="date" id="end_date" v-model="payroll.end_date" class="mt-2 w-full p-2 border rounded-md" required />
                        </div>
                        <div>
                            <label for="payment_date" class="block text-sm font-medium text-gray-700">Fecha de Pago</label>
                            <input type="date" id="payment_date" v-model="payroll.payment_date" class="mt-2 w-full p-2 border rounded-md" required />
                        </div>
                    </div>
                </div>

                <!-- Ingresos -->
                <div class="bg-gray-100 p-4 rounded-lg">
                    <h3 class="text-xl font-semibold mb-4 text-blue-500">Ingresos</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="base_salary" class="block text-sm font-medium text-gray-700">Salario Base</label>
                            <input type="number" id="base_salary" v-model="payroll.base_salary" class="mt-2 w-full p-2 border rounded-md" :readonly="true" required />
                        </div>
                        <div>
                            <label for="overtime" class="block text-sm font-medium text-gray-700">Horas Extras</label>
                            <input type="number" id="overtime" v-model="payroll.overtime" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label for="bonuses" class="block text-sm font-medium text-gray-700">Bonificaciones</label>
                            <input type="number" id="bonuses" v-model="payroll.bonuses" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label for="commissions" class="block text-sm font-medium text-gray-700">Comisiones</label>
                            <input type="number" id="commissions" v-model="payroll.commissions" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label for="vacation_bonus" class="block text-sm font-medium text-gray-700">Bono Vacacional</label>
                            <input type="number" id="vacation_bonus" v-model="payroll.vacation_bonus" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label for="annual_bonus" class="block text-sm font-medium text-gray-700">Bono Anual</label>
                            <input type="number" id="annual_bonus" v-model="payroll.annual_bonus" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label for="other_earnings" class="block text-sm font-medium text-gray-700">Otros Ingresos</label>
                            <input type="number" id="other_earnings" v-model="payroll.other_earnings" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                    </div>
                </div>

                <!-- Deducciones -->
                <div class="bg-gray-100 p-4 rounded-lg">
                    <h3 class="text-xl font-semibold mb-4 text-blue-500">Deducciones</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="income_tax" class="block text-sm font-medium text-gray-700">Impuesto sobre la Renta</label>
                            <input type="number" id="income_tax" v-model="payroll.income_tax" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label for="social_security" class="block text-sm font-medium text-gray-700">Seguridad Social</label>
                            <input type="number" id="social_security" v-model="payroll.social_security" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label for="housing_fund" class="block text-sm font-medium text-gray-700">Fondo de Vivienda</label>
                            <input type="number" id="housing_fund" v-model="payroll.housing_fund" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label for="loans" class="block text-sm font-medium text-gray-700">Préstamos</label>
                            <input type="number" id="loans" v-model="payroll.loans" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label for="savings_fund" class="block text-sm font-medium text-gray-700">Fondo de Ahorro</label>
                            <input type="number" id="savings_fund" v-model="payroll.savings_fund" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label for="union_dues" class="block text-sm font-medium text-gray-700">Cuotas Sindicales</label>
                            <input type="number" id="union_dues" v-model="payroll.union_dues" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label for="other_deductions" class="block text-sm font-medium text-gray-700">Otras Deducciones</label>
                            <input type="number" id="other_deductions" v-model="payroll.other_deductions" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                    </div>
                </div>

                <!-- IRPF -->
                <div class="bg-gray-100 p-4 rounded-lg">
                    <h3 class="text-xl font-semibold mb-4 text-blue-500">IRPF</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="irpf_percentage" class="block text-sm font-medium text-gray-700">Porcentaje de Retención IRPF</label>
                            <input type="number" id="irpf_percentage" v-model="payroll.irpf_percentage" class="mt-2 w-full p-2 border rounded-md" required />
                        </div>
                        <div>
                            <label for="irpf_deduction" class="block text-sm font-medium text-gray-700">Deducción IRPF</label>
                            <input type="number" id="irpf_deduction" v-model="payroll.irpf_deduction" class="mt-2 w-full p-2 border rounded-md" />
                        </div>
                    </div>
                </div>

                <!-- Resumen de Totales -->
                <!-- Resumen de Totales -->
                <div class="bg-gray-100 p-4 rounded-lg">
                    <h3 class="text-xl font-semibold mb-4 text-blue-500">Resumen de Totales</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>Total Ingresos:</strong> {{ totalEarnings }}</p>
                        </div>
                        <div>
                            <p><strong>Total Deducciones:</strong> {{ totalDeductions }}</p>
                        </div>
                        <div>
                            <p><strong>Retención IRPF:</strong> {{ irpfAmount }}</p>
                        </div>
                        <div>
                            <p><strong>Salario Neto:</strong> {{ netSalary }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center mt-6">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import {ref, computed, watch} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import {Inertia} from "@inertiajs/inertia";


const props = defineProps({
    payroll: Object,
    employees: Array
})

console.log(props.payroll)
console.log(props.employees)

const payroll = ref({
    employee_id: props.payroll.employee_id || 0,
    start_date: props.payroll.start_date || 0,
    end_date: props.payroll.end_date || 0,
    payment_date: props.payroll.payment_date || 0,
    base_salary: props.payroll.base_salary || 0,
    overtime: props.payroll.overtime || 0,
    bonuses: props.payroll.bonuses || 0,
    commissions: props.payroll.commissions || 0,
    vacation_bonus: props.payroll.vacation_bonus || 0,
    annual_bonus: props.payroll.annual_bonus || 0,
    other_earnings: props.payroll.other_earnings || 0,
    income_tax: props.payroll.income_tax || 0,
    social_security: props.payroll.social_security || 0,
    housing_fund: props.payroll.housing_fund || 0,
    loans: props.payroll.loans || 0,
    savings_fund: props.payroll.savings_fund || 0,
    union_dues: props.payroll.union_dues || 0,
    other_deductions: props.payroll.other_deductions || 0,
    irpf_percentage: props.payroll.irpf_percentage || 0,
    irpf_deduction: props.payroll.irpf_deduction || 0
})

//Obtener el base_salary de props.employee.salary
const employee = computed(() => {
    return props.employees.find(employee => employee.id === payroll.value.employee_id)
})
watch(() => payroll.value.employee_id, (employee_id) => {
    const employee = props.employees.find(employee => employee.id === employee_id)
    payroll.value.base_salary = employee.salary
})



const totalEarnings = computed(() => {
    return payroll.value.base_salary + payroll.value.overtime + payroll.value.bonuses +
        payroll.value.commissions + payroll.value.vacation_bonus + payroll.value.annual_bonus +
        payroll.value.other_earnings
})

const totalDeductions = computed(() => {
    return payroll.value.income_tax + payroll.value.social_security + payroll.value.housing_fund +
        payroll.value.loans + payroll.value.savings_fund + payroll.value.union_dues +
        payroll.value.other_deductions
})

const irpfAmount = computed(() => {
    return (totalEarnings.value * payroll.value.irpf_percentage) / 100
})

//Actualizar el irpf_deduction cuando cambia el irpf_percentage
watch(() => payroll.value.irpf_percentage, (irpf_percentage) => {
    payroll.value.irpf_deduction = (totalEarnings.value * irpf_percentage) / 100
})


const netSalary = computed(() => {
    return totalEarnings.value - totalDeductions.value - irpfAmount.value
})

const submitPayroll = () => {
    return Inertia.put(route('payrolls.update', props.payroll.id), payroll.value)
}
</script>

<style scoped>
/* Add styles specific to your component */
</style>
