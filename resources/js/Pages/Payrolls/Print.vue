<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();

// Propiedades obtenidas del servidor
const company = props.company;
const employee = props.employee;
const payroll = props.payroll;

// Funciones computadas para formatear números
const formatCurrency = (value) =>
    value ? new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value) : '0,00 €';

const irpfDeduction = computed(() =>
    payroll.irpf_deduction ? `${formatCurrency(payroll.irpf_deduction)} (${payroll.irpf_percentage}%)` : '0,00 €'
);

//imprimir
const printPayroll = () => {
    window.print();
};
</script>


<template>
    <div class="bg-gray-100 min-h-screen p-6">
        <div class="bg-white max-w-4xl mx-auto rounded-lg shadow-lg p-6">
            <!-- Encabezado -->
            <header class="text-center mb-6">
                <h1 class="text-2xl font-bold text-blue-600">{{ company.name }}</h1>
                <p class="text-gray-600">{{ company.address }}</p>
                <p class="text-lg font-semibold mt-2">Nómina #{{ payroll.id }}</p>
            </header>

            <!-- Detalles del Empleado -->
            <section>
                <div class="bg-blue-600 text-white px-4 py-2 rounded-md mb-4">Detalles del Empleado</div>
                <table class="w-full text-left border-collapse border border-gray-300">
                    <tbody>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Nombre del Empleado</th>
                        <td class="px-4 py-2">{{ employee.name }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Días Trabajados</th>
                        <td class="px-4 py-2">{{ payroll.days_worked }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Días de Ausencia</th>
                        <td class="px-4 py-2">{{ payroll.days_absent }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Fecha de Inicio</th>
                        <td class="px-4 py-2">{{ payroll.start_date }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Fecha de Fin</th>
                        <td class="px-4 py-2">{{ payroll.end_date }}</td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 bg-gray-100">Fecha de Pago</th>
                        <td class="px-4 py-2">{{ payroll.payment_date }}</td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <!-- Ganancias -->
            <section class="mt-6">
                <div class="bg-blue-600 text-white px-4 py-2 rounded-md mb-4">Ganancias</div>
                <table class="w-full text-left border-collapse border border-gray-300">
                    <tbody>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Salario Base</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.base_salary) }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Bonos</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.bonuses) }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Horas Extra</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.overtime) }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Comisiones</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.commissions) }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Bono de Vacaciones</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.vacation_bonus) }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Otras Ganancias</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.other_earnings) }}</td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 bg-gray-100">Ganancias Totales</th>
                        <td class="px-4 py-2 font-semibold">{{ formatCurrency(payroll.total_earnings) }}</td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <!-- Deducciones -->
            <section class="mt-6">
                <div class="bg-blue-600 text-white px-4 py-2 rounded-md mb-4">Deducciones</div>
                <table class="w-full text-left border-collapse border border-gray-300">
                    <tbody>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Seguridad Social</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.social_security) }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Impuesto sobre la Renta</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.income_tax) }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Deducción IRPF</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.irpf_deduction) }} ({{ payroll.irpf_percentage }}%)</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Cuotas Sindicales</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.union_dues) }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Préstamos</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.loans) }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Fondo de Vivienda</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.housing_fund) }}</td>
                    </tr>
                    <tr class="border-b border-gray-300">
                        <th class="px-4 py-2 bg-gray-100">Fondo de Ahorro</th>
                        <td class="px-4 py-2">{{ formatCurrency(payroll.savings_fund) }}</td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 bg-gray-100">Deducciones Totales</th>
                        <td class="px-4 py-2 font-semibold">{{ formatCurrency(payroll.total_deductions) }}</td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <!-- Pago Neto -->
            <section class="mt-6">
                <div class="bg-blue-600 text-white px-4 py-2 rounded-md mb-4">Pago Neto</div>
                <table class="w-full text-left border-collapse border border-gray-300">
                    <tbody>
                    <tr>
                        <th class="px-4 py-2 bg-gray-100">Pago Neto</th>
                        <td class="px-4 py-2 font-bold text-lg">{{ formatCurrency(payroll.net_pay) }}</td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <div>
                <button @click="printPayroll" class="bg-blue-600 text-white px-4 py-2 rounded-md mt-6">Imprimir Nómina</button>
            </div>

            <!-- Pie de Página -->
            <footer class="text-center mt-6 text-gray-600 text-sm">
                Generado el {{ payroll.created_at }}
            </footer>
        </div>
    </div>
</template>

<style scoped>
@media print {
    @page {
        size: A4;
        margin: 20mm;
    }

    /* Prevent table rows from breaking */


    button {
        display: none;
    }


}

</style>
