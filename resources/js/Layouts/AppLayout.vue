<template>
    <div class="relative min-h-screen bg-slate-950 text-slate-100">
        <head>
            <title>POPERP by JCTAgency</title>
            <meta
                name="description"
                content="Un ERP online desarrollado para medianas empresas y pymes que contiene módulos de facturación, contabilidad, TPV, CRM, Gestión de clientes, Gestión de productos..."
            >
        </head>
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900"></div>
        <div class="relative z-10 flex min-h-screen flex-col">
            <nav class="bg-gradient-to-r from-blue-600 via-indigo-600 to-slate-900 border-b border-white/10 text-white">
                <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-6 py-4">
                    <a href="/dashboard" class="inline-flex items-center gap-3">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-inner shadow-blue-900/40">
                            <img src="/storage/JesTy.jpeg" alt="Logo de POPERP" class="h-9 w-9 rounded-xl object-cover">
                        </span>
                        <span class="flex flex-col">
                            <span class="text-lg font-semibold tracking-wide">POPERP by JCTAgency</span>
                            <span class="text-xs uppercase tracking-[0.35em] text-blue-100/80">Business Control Suite</span>
                        </span>
                    </a>
                    <div class="flex items-center">
                        <UserDropdown :user="$page.props.auth.user" />
                    </div>
                </div>
            </nav>
       <div class="flex-1 w-full pb-12 pt-8">
           <!-- Contenidor a pantalla completa sense límit de width -->
            <div class="flex w-full max-w-none flex-col gap-8 px-4 lg:flex-row lg:items-start lg:px-6">
                  <!-- Sidebar amb amplada fixa i sense encongir -->
                   <aside class="w-full lg:w-64 lg:shrink-0">
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl shadow-2xl shadow-blue-500/20">
                            <div class="space-y-6">
                                <div>
                                    <p class="text-[0.65rem] font-semibold uppercase tracking-[0.35em] text-blue-200/80">Menú</p>
                                    <p class="mt-2 text-sm text-blue-100/60">Accedeix ràpidament als mòduls que tens actius.</p>
                                </div>

                                <div class="space-y-6">
                                    <div class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-blue-200/60">General</p>
                                        <NewNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                            <div class="flex items-center gap-3">
                                                <span class="icon-accent flex h-4 items-center justify-center rounded-xl bg-white/10 text-blue-200 transition group-hover:bg-white/20">
                                                    <MenuHomeIcon class="size-2" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Inici</p>
                                                    <p class="text-xs text-blue-100/70">Resum general de l'activitat</p>
                                                </div>
                                            </div>
                                        </NewNavLink>
                                    </div>

                                    <div v-if="hasFeature('Facturación')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-blue-200/60">Facturació</p>
                                        <NewNavLink :href="route('dashboard.billing')" :active="route().current('dashboard.billing')">
                                            <div class="flex items-center gap-3">
                                                <span class="icon-accent flex h-4 items-center justify-center rounded-xl bg-white/10 text-blue-200 transition group-hover:bg-white/20">
                                                    <MenuBillingIcon class="size-2" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Facturació</p>
                                                    <p class="text-xs text-blue-100/70">Gestió de pressupostos i factures</p>
                                                </div>
                                            </div>
                                            <DropdownIcon class="h-3 w-3 text-blue-100/70" />
                                        </NewNavLink>
                                        <div v-if="isBillingPage" class="ml-4 space-y-2 border-l border-white/10 pl-4">
                                            <NewNavLink :href="route('invoices.index')" :active="route().current('invoices.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuInvoiceIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Factures</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('budgets.index')" :active="route().current('budgets.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuBudgetIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Pressupostos</span>
                                                </span>
                                            </NewNavLink>
                                        </div>
                                    </div>

                                    <div v-if="hasFeature('Inventario')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-blue-200/60">Magatzem</p>
                                        <NewNavLink :href="route('dashboard.products')" :active="route().current('dashboard.products')">
                                            <div class="flex items-center gap-3">
                                                <span class="icon-accent flex h-4 items-center justify-center rounded-xl bg-white/10 text-blue-200 transition group-hover:bg-white/20">
                                                    <MenuInventoryIcon class="size-2" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Almacén</p>
                                                    <p class="text-xs text-blue-100/70">Control d'estoc i categories</p>
                                                </div>
                                            </div>
                                            <DropdownIcon class="h-3 w-3 text-blue-100/70" />
                                        </NewNavLink>
                                        <div v-if="isProductsPage" class="ml-4 space-y-2 border-l border-white/10 pl-4">
                                            <NewNavLink :href="route('products.index')" :active="route().current('products.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuProductIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Productes</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('suppliers.index')" :active="route().current('suppliers.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuClientsIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Proveïdors</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('stockEntries.index')" :active="route().current('stockEntries.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <AddProductIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Entrades de stock</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('categories.index')" :active="route().current('categories.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuCategoryIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Categories</span>
                                                </span>
                                            </NewNavLink>
                                        </div>
                                    </div>

                                    <div v-if="hasFeature('Clientes')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-blue-200/60">Clients</p>
                                        <NewNavLink :href="route('dashboard.clients')" :active="route().current('dashboard.clients')">
                                            <div class="flex items-center gap-3">
                                                <span class="icon-accent flex h-4 items-center justify-center rounded-xl bg-white/10 text-blue-200 transition group-hover:bg-white/20">
                                                    <MenuClientsIcon class="size-2" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Clients</p>
                                                    <p class="text-xs text-blue-100/70">Gestió de comptes i relacions</p>
                                                </div>
                                            </div>
                                        </NewNavLink>
                                    </div>

                                    <div v-if="hasFeature('Contabilidad')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-blue-200/60">Finances</p>
                                        <NewNavLink :href="route('dashboard.accounting')" :active="route().current('dashboard.accounting')">
                                            <div class="flex items-center gap-3">
                                                <span class="icon-accent flex h-4 items-center justify-center rounded-xl bg-white/10 text-blue-200 transition group-hover:bg-white/20">
                                                    <MenuAccountingIcon class="size-2" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Comptabilitat</p>
                                                    <p class="text-xs text-blue-100/70">Informes i seguiment econòmic</p>
                                                </div>
                                            </div>
                                            <DropdownIcon class="h-3 w-3 text-blue-100/70" />
                                        </NewNavLink>
                                        <div v-if="isAccountingPage" class="ml-4 space-y-2 border-l border-white/10 pl-4">
                                            <NewNavLink :href="route('expenses.report')" :active="route().current('expenses.report')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuReportIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Informes</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('incomes.index')" :active="route().current('incomes.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <IncomeIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Ingressos</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('expenses.index')" :active="route().current('expenses.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuExpenseIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Despeses</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('expenseCategories.index')" :active="route().current('expenseCategories.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuCategoryIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Categories</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('paymentMethods.index')" :active="route().current('paymentMethods.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuPaymentIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Mètodes de pagament</span>
                                                </span>
                                            </NewNavLink>
                                        </div>
                                    </div>

                                    <div v-if="hasFeature('TPV')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-blue-200/60">TPV</p>
                                        <NewNavLink :href="route('dashboard.tpv')" :active="route().current('dashboard.tpv')">
                                            <div class="flex items-center gap-3">
                                                <span class="icon-accent flex h-4 items-center justify-center rounded-xl bg-white/10 text-blue-200 transition group-hover:bg-white/20">
                                                    <PayIcon class="size-2" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">TPV</p>
                                                    <p class="text-xs text-blue-100/70">Cobraments i punts de venda</p>
                                                </div>
                                            </div>
                                            <DropdownIcon class="h-3 w-3 text-blue-100/70" />
                                        </NewNavLink>
                                        <div v-if="isTpvPage" class="ml-4 space-y-2 border-l border-white/10 pl-4">
                                            <NewNavLink :href="route('tikets.create')" :active="route().current('tikets.create')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuPaymentIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Nova venda</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('tikets.index')" :active="route().current('tikets.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuInvoiceIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Tickets</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('tikets.productReport')" :active="route().current('tikets.productReport')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuReportIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Informe de productes</span>
                                                </span>
                                            </NewNavLink>
                                        </div>
                                    </div>

                                    <div v-if="hasFeature('RRHH')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-blue-200/60">Persones</p>
                                        <NewNavLink :href="route('dashboard.rrhh')" :active="route().current('dashboard.rrhh')">
                                            <div class="flex items-center gap-3">
                                                <span class="icon-accent flex h-4 items-center justify-center rounded-xl bg-white/10 text-blue-200 transition group-hover:bg-white/20">
                                                    <MenuRRHHIcon class="size-2" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">RRHH</p>
                                                    <p class="text-xs text-blue-100/70">Equip, fitxatges i formació</p>
                                                </div>
                                            </div>
                                            <DropdownIcon class="h-3 w-3 text-blue-100/70" />
                                        </NewNavLink>
                                        <div v-if="isRRHHPage" class="ml-4 space-y-2 border-l border-white/10 pl-4">
                                            <NewNavLink :href="route('employees.index')" :active="route().current('employees.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuClientsIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Empleats</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('departments.index')" :active="route().current('departments.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuCategoryIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Departaments</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('payrolls.index')" :active="route().current('payrolls.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuInvoiceIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Nòmines</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('attendances.index')" :active="route().current('attendances.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuAccountingIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Control horari</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('performance-reviews.index')" :active="route().current('performance-reviews.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuReportIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Desempeño</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('leaves.index')" :active="route().current('leaves.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuExpenseIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Vacances i absències</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('trainings.index')" :active="route().current('trainings.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <AddProductIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Formacions</span>
                                                </span>
                                            </NewNavLink>
                                        </div>
                                    </div>

                                    <div v-if="hasFeature('CRM')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-blue-200/60">CRM</p>
                                        <NewNavLink :href="route('dashboard.crm')" :active="route().current('dashboard.crm')">
                                            <div class="flex items-center gap-3">
                                                <span class="icon-accent flex h-4 items-center justify-center rounded-xl bg-white/10 text-blue-200 transition group-hover:bg-white/20">
                                                    <MenuCRMIcon class="size-2" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">CRM</p>
                                                    <p class="text-xs text-blue-100/70">Oportunitats i seguiment comercial</p>
                                                </div>
                                            </div>
                                            <DropdownIcon class="h-3 w-3 text-blue-100/70" />
                                        </NewNavLink>
                                        <div v-if="isCrmPage" class="ml-4 space-y-2 border-l border-white/10 pl-4">
                                            <NewNavLink :href="route('leads.index')" :active="route().current('leads.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuClientsIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Leads</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('opportunities.index')" :active="route().current('opportunities.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuCategoryIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Oportunitats</span>
                                                </span>
                                            </NewNavLink>
                                        </div>
                                    </div>

                                    <div v-if="hasFeature('Administradores')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-blue-200/60">Administració</p>
                                        <NewNavLink :href="route('dashboard.admin')" :active="route().current('dashboard.admin')">
                                            <div class="flex items-center gap-3">
                                                <span class="icon-accent flex h-4 items-center justify-center rounded-xl bg-white/10 text-blue-200 transition group-hover:bg-white/20">
                                                    <MenuReportIcon class="size-2" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Administració</p>
                                                    <p class="text-xs text-blue-100/70">Usuaris i rols del sistema</p>
                                                </div>
                                            </div>
                                            <DropdownIcon class="h-3 w-3 text-blue-100/70" />
                                        </NewNavLink>
                                        <div v-if="isAdminPage" class="ml-4 space-y-2 border-l border-white/10 pl-4">
                                            <NewNavLink :href="route('users.index')" :active="route().current('users.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuClientsIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Usuaris</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('user_tasks.index')" :active="route().current('user_tasks.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuInvoiceIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Tasques</span>
                                                </span>
                                            </NewNavLink>
                                            <NewNavLink :href="route('roles.index')" :active="route().current('roles.index')" variant="sub">
                                                <span class="flex items-center gap-2 text-sm">
                                                    <MenuCategoryIcon class="icon-accent h-3 w-3 text-blue-100" />
                                                    <span>Rols</span>
                                                </span>
                                            </NewNavLink>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>
  <main class="flex-1 min-w-0">
                        <div class="h-full w-full rounded-3xl border border-white/10 bg-slate-900/60 px-4 py-6 shadow-2xl shadow-blue-500/20 backdrop-blur lg:px-8 lg:py-10">
                           <slot />
                        </div>
                    </main>
                </div>
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
import PayIcon from "@/Components/Icons/PayIcon.vue";
import AddProductIcon from "@/Components/Icons/AddProductIcon.vue";
import IncomeIcon from "@/Components/Icons/IncomeIcon.vue";
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import MenuRRHHIcon from "@/Components/Icons/MenuRRHHIcon.vue";
import MenuHomeIcon from "@/Components/Icons/MenuHomeIcon.vue";

const features = computed(() => usePage().props.features);

const hasFeature = (featureName) => features.value?.some((feature) => feature.name === featureName);

const isRRHHPage = route().current('dashboard.rrhh') || route().current('employees.index') || route().current('departments.index') || route().current('payrolls.index') || route().current('attendances.index') || route().current('performance-reviews.index') || route().current('leaves.index') || route().current('trainings.index');
const isBillingPage = route().current('dashboard.billing') || route().current('budgets.index') || route().current('invoices.index');
const isProductsPage = route().current('products.index') || route().current('categories.index') || route().current('dashboard.products') || route().current('suppliers.index') || route().current('stockEntries.index');
const isAccountingPage = route().current('expenses.index') || route().current('expenses.report') || route().current('dashboard.accounting') || route().current('expenseCategories.index') || route().current('paymentMethods.index') || route().current('incomes.index');
const isTpvPage = route().current('dashboard.tpv') || route().current('tikets.create') || route().current('tikets.index') || route().current('tikets.productReport');
const isCrmPage = route().current('dashboard.crm') || route().current('leads.index') || route().current('opportunities.index') || route().current('tasks.index') || route().current('activities.index') || route().current('notes.index') || route().current('leads.create') || route().current('opportunities.create') || route().current('tasks.create') || route().current('activities.create') || route().current('notes.create') || route().current('leads.edit') || route().current('opportunities.edit') || route().current('tasks.edit') || route().current('activities.edit') || route().current('notes.edit') || route().current('leads.show') || route().current('opportunities.show') || route().current('tasks.show') || route().current('activities.show') || route().current('notes.show');
const isAdminPage = route().current('dashboard.admin') || route().current('users.index') || route().current('roles.index') || route().current('permissions.index') || route().current('users.create') || route().current('roles.create') || route().current('permissions.create') || route().current('users.edit') || route().current('roles.edit') || route().current('permissions.edit') || route().current('users.show') || route().current('roles.show') || route().current('permissions.show') || route().current('users.destroy') || route().current('roles.destroy') || route().current('permissions.destroy') || route().current('user_tasks.index') || route().current('user_tasks.adminCreate') || route().current('user_tasks.adminEdit') || route().current('user_tasks.destroy');
</script>
