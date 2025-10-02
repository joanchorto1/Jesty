<template>

    <head>
        <title>POPERP by JCTAgency</title>
        <!-- Descripción -->
        <meta name="description"
              content="Un ERP online desarrollado para medianas empresas y pymes que contiene módulos de facturación, contabilidad, TPV, CRM, Gestión de clientes, Gestión de productos...">
    </head>
    <div class="min-h-screen min-w-full bg-gradient-to-br from-slate-100 via-white to-slate-200 flex flex-col">
        <!-- Top Bar -->
        <nav class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white shadow-lg border-b border-white/20 px-6 py-4 flex justify-between items-center w-full">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <a href="/dashboard" class="inline-flex items-center gap-3">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                        <img src="/storage/JesTy.jpeg" alt="" class="h-9 w-9 rounded-xl object-cover">
                    </span>
                    <span class="text-lg font-semibold tracking-wide">POPERP by JCTAgency</span>
                </a>
            </div>

            <!-- Users Dropdown -->
            <div class="flex items-center">
                <UserDropdown :user="$page.props.auth.user"/>
            </div>
        </nav>

        <!-- Layout con Sidebar -->
        <div class="flex flex-1 w-full px-4 py-6 lg:px-10 lg:py-10 gap-6">
            <!-- Sidebar Menu -->
            <nav class="w-64 flex-shrink-0">
                <div class="relative h-full">
                    <div class="absolute inset-0 rounded-3xl bg-white/70 shadow-xl shadow-slate-200/60 border border-white/80 backdrop-blur">
                    </div>
                    <div class="relative flex h-full flex-col mt-0 rounded-3xl px-5 py-8 space-y-4 overflow-y-auto">
                        <div class="text-xs uppercase tracking-[0.35em] text-slate-400 font-semibold">Menú</div>
                    <NewNavLink :href="route('dashboard')" :active="route().current('dashboard')"
                                       class="text-slate-500 hover:text-blue-600 transition-colors duration-200 px-3 py-2 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <MenuHomeIcon class="fill-gray-950 h-4 w-5"/>
                            <p class="text-sm font-medium">Home</p>
                        </div>
                    </NewNavLink>

                    <!-- Enlace principal de Facturación -->
                    <template v-for="feature in props.features.value">
                    <NewNavLink  v-if="feature.name === 'Facturación'" :href="route('dashboard.billing')" :active="route().current('dashboard.billing')"
                                       class="text-slate-500 hover:text-blue-600 transition-colors duration-200 px-3 py-2 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <MenuBillingIcon class="h-5 w-5"/>
                            <p class="text-sm font-medium">Facturación</p>
                                <DropdownIcon class="h-4 w-4 fill-blue-500 stroke-0"/>
                        </div>
                    </NewNavLink>
                    </template>

                    <!-- Enlaces específicos de facturación -->
                    <div v-if="isBillingPage" class="flex flex-col border-l-4 border-blue-500/60 space-y-1 ml-4 pl-3">
                        <NewNavLink :href="route('invoices.index')" :active="route().current('invoices.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuInvoiceIcon class="h-5 w-5"/>
                                <p class="text-sm">Facturas</p>
                            </div>
                        </NewNavLink>
                        <NewNavLink :href="route('budgets.index')" :active="route().current('budgets.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuBudgetIcon class="h-5 w-5"/>
                                <p class="text-sm">Presupuestos</p>
                            </div>
                        </NewNavLink>
                    </div>

                    <!-- Otros enlaces del menú -->
                    <template v-for="feature in props.features.value">
                    <NewNavLink v-if="feature.name === 'Inventario'" :href="route('dashboard.products')" :active="route().current('dashboard.products')"
                                       class="text-slate-500 hover:text-blue-600 transition-colors duration-200 px-3 py-2 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <MenuInventoryIcon class="h-5 w-5"/>
                            <p class="text-sm font-medium">Almacén</p>
                                <DropdownIcon class="h-4 w-4 fill-blue-500 stroke-0"/>
                        </div>
                    </NewNavLink>
                    </template>
                    <div v-if="isProductsPage" class="flex flex-col border-l-4 border-blue-500/60 space-y-1 ml-4 pl-3">
                        <NewNavLink :href="route('products.index')" :active="route().current('products.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuProductIcon class="h-5 w-5"/>
                                <p class="text-sm">Productos</p>
                            </div>
                        </NewNavLink>
                        <NewNavLink :href="route('suppliers.index')" :active="route().current('suppliers.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuClientsIcon class="h-5 w-5"/>
                                <p class="text-sm">Proveedores</p>
                            </div>
                        </NewNavLink>
                        <NewNavLink :href="route('stockEntries.index')" :active="route().current('stockEntries.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <AddProductIcon class="h-5 stroke-black w-5"/>

                                <p class="text-sm">Entradas de Stock</p>
                            </div>
                        </NewNavLink>
                        <NewNavLink :href="route('categories.index')"
                                           :active="route().current('categories.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuCategoryIcon class="h-5 w-5"/>

                                <p class="text-sm">Categorías</p>
                            </div>
                        </NewNavLink>
                    </div>
                    <template v-for="feature in props.features.value">
                    <NewNavLink v-if="feature.name==='Clientes'" :href="route('dashboard.clients')" :active="route().current('dashboard.clients')"
                                       class="text-slate-500 hover:text-blue-600 transition-colors duration-200 px-3 py-2 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <MenuClientsIcon class="h-5 w-5"/>
                            <p class="text-sm font-medium">Clientes</p>
                        </div>
                    </NewNavLink>
                    </template>

                    <!-- Enlaces vacíos para futuras funcionalidades -->
                    <template v-for="feature in props.features.value">
                    <NewNavLink v-if="feature.name==='Contabilidad'" :href="route('dashboard.accounting')" :active="route().current('dashboard.accounting')"
                                       class="text-slate-500 hover:text-blue-600 transition-colors duration-200 px-3 py-2 rounded-xl">
                        <div class="flex w-full items-center space-x-3">
                            <MenuAccountingIcon class="h-5 w-5"/>
                            <p class="text-sm font-medium">Contabilidad</p>
                            <DropdownIcon class="h-4 w-4 fill-blue-500 stroke-0"/>

                        </div>
                    </NewNavLink>
                    </template>
                    <div v-if="isAccountingPage" class="flex flex-col border-l-4 border-blue-500/60 space-y-1 ml-4 pl-3">
                        <NewNavLink :href="route('expenses.report')" :active="route().current('expenses.report')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuReportIcon class="h-5 w-5"/>
                                <p class="text-sm">Informes</p>
                            </div>
                        </NewNavLink>
                        <NewNavLink :href="route('incomes.index')" :active="route().current('incomes.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <IncomeIcon class="h-5 w-5"/>
                                <p class="text-sm">Ingresos</p>
                            </div>
                        </NewNavLink>
                        <NewNavLink :href="route('expenses.index')" :active="route().current('expenses.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuExpenseIcon class="h-5 w-5"/>
                                <p class="text-sm">Gastos</p>
                            </div>
                        </NewNavLink>
                        <NewNavLink :href="route('expenseCategories.index')"
                                           :active="route().current('expenseCategories.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuCategoryIcon class="h-5 w-5"/>
                                <p class="text-sm">Categorías</p>
                            </div>
                        </NewNavLink>
                        <NewNavLink :href="route('paymentMethods.index')"
                                           :active="route().current('paymentMethods.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuPaymentIcon class="h-5 w-5"/>
                                <p class="text-sm">Métodos de Pago</p>
                            </div>
                        </NewNavLink>

                    </div>


                    <template v-for="feature in props.features.value">
                    <NewNavLink v-if="feature.name === 'TPV'" :href="route('dashboard.tpv')"  :active="route().current('dashboard.tpv')" class="text-slate-500 hover:text-blue-600 transition-colors duration-200 px-3 py-2 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <PayIcon class="h-5 w-5"/>

                            <p class="text-sm font-medium">TPV</p>
                            <DropdownIcon class="h-4 w-4 fill-blue-500 stroke-0"/>
                        </div>
                    </NewNavLink>
                    </template>


                    <div v-if="isTpvPage" class="flex border-l-4 border-blue-500/60 flex-col space-y-1 ml-4 pl-3  ">
                        <NewNavLink :href="route('tikets.create')" :active="route().current('tikets.create')"
                                    class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                            <MenuPaymentIcon class="h-5 w-5"/>
                            <p class="text-sm">Venta</p>
                            </div>
                        </NewNavLink>
                        <NewNavLink :href="route('tikets.index')" :active="route().current('tikets.index')"
                                    class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2"><MenuInvoiceIcon class="w-5 h-5"/>
                            <p class="text-sm">Tickets</p>
                            </div>
                        </NewNavLink>
                        <NewNavLink :href="route('tikets.productReport')" :active="route().current('tikets.productReport')"
                                    class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuReportIcon class="w-5 h-5"/>
                            <p class="text-sm">Informe de Productos</p>
                            </div>
                        </NewNavLink>
                    </div>

                    <template v-for="feature in props.features.value">
                        <NewNavLink v-if="feature.name==='RRHH'" :href="route('dashboard.rrhh')" :active="route().current('dashboard.rrhh')" class="text-slate-500 hover:text-blue-600 transition-colors duration-200 px-3 py-2 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <MenuRRHHIcon class="h-5 w-5 fill-gray-950"/>
                                <p class="text-sm font-medium">RRHH</p>
                                <DropdownIcon class="h-4 w-4 fill-blue-500 stroke-0"/>
                            </div>
                        </NewNavLink>
                    </template>

                    <div v-if="isRRHHPage" class="flex flex-col border-blue-500/60 border-l-4 space-y-1 ml-4 pl-3 ">

                        <!--                        Employees-->
                        <NewNavLink :href="route('employees.index')" :active="route().current('employees.index')"
                                    class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuClientsIcon class="h-5 w-5"/>
                                <p class="text-sm">Empleados</p>
                            </div>
                        </NewNavLink>

                        <!--                        Departments-->
                        <NewNavLink :href="route('departments.index')" :active="route().current('departments.index')"
                                    class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuCategoryIcon class="h-5 w-5"/>
                                <p class="text-sm">Departamentos</p>
                            </div>
                        </NewNavLink>

                        <!--                        Nominas-->
                        <NewNavLink :href="route('payrolls.index')" :active="route().current('payrolls.index')"
                                    class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuInvoiceIcon class="h-5 w-5"/>
                                <p class="text-sm">Nóminas</p>
                            </div>
                        </NewNavLink>

                        <!--                        Control de horas-->
                        <NewNavLink :href="route('attendances.index')" :active="route().current('attendances.index')"
                                    class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuAccountingIcon class="h-5 w-5"/>
                                <p class="text-sm">Control de horas</p>
                            </div>
                        </NewNavLink>

                        <!--                        Evaluacion de desempeño-->
                        <NewNavLink :href="route('performance-reviews.index')" :active="route().current('preformance-reviews.index')"
                                    class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuReportIcon class="h-5 w-5"/>
                                <p class="text-sm">Desempeño</p>
                            </div>
                        </NewNavLink>

                        <!--                    Vacaciones y dias libres    -->

                        <NewNavLink :href="route('leaves.index')" :active="route().current('leaves.index')"
                                    class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuExpenseIcon class="h-5 w-5"/>
                                <p class="text-sm">Vacaciones y dias libres</p>
                            </div>
                        </NewNavLink>

                        <!--                        Formaciones-->

                        <NewNavLink :href="route('trainings.index')" :active="route().current('trainings.index')"
                                    class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <AddProductIcon class="stroke-black h-5 w-5"/>
                                <p class="text-sm">Formaciones</p>
                            </div>
                        </NewNavLink>


                    </div>


                    <template v-for="feature in props.features.value">
                        <NewNavLink v-if="feature.name==='CRM'" :href="route('dashboard.crm')" :active="route().current('dashboard.crm')" class="text-slate-500 hover:text-blue-600 transition-colors duration-200 px-3 py-2 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <MenuCRMIcon class="h-5 w-5"/>
                            <p class="text-sm font-medium">CRM</p>
                            <DropdownIcon class="h-4 w-4 fill-blue-500 stroke-0"/>

                        </div>
                    </NewNavLink>
                    </template>


                    <div v-if="isCrmPage" class="flex flex-col border-blue-500/60 border-l-4 space-y-1 ml-4 pl-3 ">
                        <NewNavLink :href="route('leads.index')" :active="route().current('leads.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuClientsIcon class="h-5 w-5"/>
                                <p class="text-sm">Leads</p>
                            </div>
                        </NewNavLink>
                        <NewNavLink :href="route('opportunities.index')" :active="route().current('opportunities.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuCategoryIcon class="h-5 w-5"/>
                                <p class="text-sm">Oportunidades</p>
                            </div>
                        </NewNavLink>
<!--                        <NewNavLink :href="route('tasks.index')" :active="route().current('tasks.index')"-->
<!--                                           class="text-sm text-gray-400 hover:text-gray-500 py-1">-->
<!--                            <div class="flex items-center space-x-2">-->
<!--                                <MenuBillingIcon class="h-5 w-5"/>-->
<!--                                <p class="text-sm">Tareas</p>-->
<!--                            </div>-->
<!--                        </NewNavLink>-->

                    </div>

                    <template v-for="feature in props.features.value">
                        <NewNavLink v-if="feature.name==='Administradores'" :href="route('dashboard.admin')" :active="route().current('dashboard.admin')" class="text-slate-500 hover:text-blue-600 transition-colors duration-200 px-3 py-2 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <MenuReportIcon class="h-5 w-5"/>
                            <p class="text-sm font-medium">Administración</p>
                            <DropdownIcon class="h-4 w-4 fill-blue-500 stroke-0"/>
                        </div>
                        </NewNavLink>
                    </template>


                    <div v-if="isAdminPage" class="flex flex-col border-blue-500/60 border-l-4 space-y-1 ml-4 pl-3 ">
                        <NewNavLink :href="route('users.index')" :active="route().current('users.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuClientsIcon class="h-5 w-5"/>
                                <p class="text-sm">Usuarios</p>
                            </div>
                        </NewNavLink>
                        <NewNavLink :href="route('user_tasks.index')" :active="route().current('user_tasks.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuInvoiceIcon class="h-5 w-5"/>
                                <p class="text-sm">Tareas</p>
                            </div>
                        </NewNavLink>

                        <NewNavLink :href="route('roles.index')" :active="route().current('roles.index')"
                                           class="text-sm text-slate-400 hover:text-slate-600 transition-colors duration-200 py-1">
                            <div class="flex items-center space-x-2">
                                <MenuCategoryIcon class="h-5 w-5"/>
                                <p class="text-sm">Roles</p>
                            </div>
                        </NewNavLink>
                    </div>




                    </div>
                </div>
            </nav>

            <!-- Main Content Area -->
            <div class="flex-1 flex">
                <!-- Page Content -->
                <main class="flex-1">
                    <div class="h-full w-full rounded-3xl bg-white/70 backdrop-blur border border-white/60 shadow-xl shadow-slate-200/60 px-4 py-6 sm:px-8 sm:py-10">
                        <slot/>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<script setup>
import UserDropdown from "@/Components/UserDropdown.vue";
import MenuBillingIcon from "@/Components/Icons/MenuBillingIcon.vue";
import MenuInvoiceIcon from "@/Components/Icons/MenuInvoiceIcon.vue";
import MenuBudgetIcon from "@/Components/Icons/MenuBudgetIcon.vue";
import MenuInventoryIcon from "@/Components/Icons/MenuInventoryIcon.vue";
import MenuProductIcon from "@/Components/Icons/MenuProductIcon.vue";
import MenuCategoryIcon from "@/Components/Icons/MenuCategoryIcon.vue";
import MenuClientsIcon from "@/Components/Icons/MenuClientsIcon.vue";
import MenuAccountingIcon from "@/Components/Icons/MenuAccountingIcon.vue";
import MenuReportIcon from "@/Components/Icons/MenuReportIcon.vue";
import MenuExpenseIcon from "@/Components/Icons/MenuExpenseIcon.vue";
import MenuPaymentIcon from "@/Components/Icons/MenuPaymentIcon.vue";
import MenuCRMIcon from "@/Components/Icons/MenuCRMIcon.vue";
import DropdownIcon from "@/Components/Icons/DropdownIcon.vue";
import NewNavLink from "@/Components/NewNavLink.vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import PayIcon from "@/Components/Icons/PayIcon.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import AddProductIcon from "@/Components/Icons/AddProductIcon.vue";
import IncomeIcon from "@/Components/Icons/IncomeIcon.vue";
import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";
import MenuRRHHIcon from "@/Components/Icons/MenuRRHHIcon.vue";
import MenuHomeIcon from "@/Components/Icons/MenuHomeIcon.vue";


const features = computed(() => usePage().props.features)

const props = {
    features: features,
}

const isRRHHPage = route().current('dashboard.rrhh') || route().current('employees.index') || route().current('departments.index') || route().current('payrolls.index') || route().current('attendances.index') || route().current('performance-reviews.index') || route().current('leaves.index') || route().current('trainings.index');
const isBillingPage = route().current('dashboard.billing') || route().current('budgets.index') || route().current('invoices.index');
const isProductsPage = route().current('products.index') || route().current('categories.index') || route().current('dashboard.products')||route().current('suppliers.index')||route().current('stockEntries.index');
const isAccountingPage = route().current('expenses.index') || route().current('expenses.report') || route().current('dashboard.accounting') || route().current('expenseCategories.index') || route().current('paymentMethods.index')||route().current('incomes.index');
const isTpvPage = route().current('dashboard.tpv') || route().current('tikets.create') || route().current('tikets.index')|| route().current('tikets.productReport');
const isCrmPage = route().current('dashboard.crm')|| route().current('leads.index')|| route().current('opportunities.index')|| route().current('tasks.index')|| route().current('activities.index')|| route().current('notes.index')||route().current('leads.create')||route().current('opportunities.create')||route().current('tasks.create')||route().current('activities.create')||route().current('notes.create')||route().current('leads.edit')||route().current('opportunities.edit')||route().current('tasks.edit')||route().current('activities.edit')||route().current('notes.edit')||route().current('leads.show')||route().current('opportunities.show')||route().current('tasks.show')||route().current('activities.show')||route().current('notes.show');
const isAdminPage = route().current('dashboard.admin')|| route().current('users.index')|| route().current('roles.index')|| route().current('permissions.index')|| route().current('users.create')|| route().current('roles.create')|| route().current('permissions.create')|| route().current('users.edit')|| route().current('roles.edit')|| route().current('permissions.edit')|| route().current('users.show')|| route().current('roles.show')|| route().current('permissions.show')|| route().current('users.destroy')|| route().current('roles.destroy')|| route().current('permissions.destroy')||route().current('user_tasks.index')||route().current('user_tasks.adminCreate')||route().current('user_tasks.adminEdit')||route().current('user_tasks.destroy');
</script>

