<template>
    <div class="relative min-h-screen bg-slate-50 text-slate-900">
        <head>
            <title>POPERP by JCTAgency</title>
            <meta
                name="description"
                content="Un ERP online desarrollado para medianas empresas y pymes que contiene módulos de facturación, contabilidad, TPV, CRM, Gestión de clientes, Gestión de productos..."
            >
        </head>
        <div class="relative z-10 flex min-h-screen flex-col">
            <nav class="border-b border-slate-200 bg-white/90 text-slate-900 shadow-sm backdrop-blur">
                <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-6 py-4">
                    <a href="/dashboard" class="inline-flex items-center gap-3">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 shadow-inner shadow-slate-200">
                            <img src="/storage/JCTLogo.jpeg" alt="Logo de POPERP" class="h-9 w-9 rounded-xl object-cover">
                        </span>
                        <span class="flex flex-col">
                            <span class="text-lg font-semibold tracking-wide">JCTAgency</span>
                            <span class="text-xs uppercase tracking-[0.35em] text-slate-500">Business Control Suite</span>
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
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="space-y-6">
                                <div>
                                    <p class="text-[0.65rem] font-semibold uppercase tracking-[0.35em] text-slate-500">Menú</p>
                                    <p class="mt-2 text-sm text-slate-500">Accedeix ràpidament als mòduls que tens actius.</p>
                                </div>

                                <div class="space-y-6">
                                    <div class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">General</p>
                                        <NewNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                    <MenuHomeIcon class="icon-accent h-4 w-4" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Inici</p>
                                                    <p class="text-xs text-slate-500">Resum general de l'activitat</p>
                                                </div>
                                            </div>
                                        </NewNavLink>
                                    </div>

                                    <div v-if="hasFeature('Facturación')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Facturació</p>
                                        <NewNavLink :href="route('dashboard.billing')" :active="route().current('dashboard.billing')">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                    <MenuBillingIcon class="icon-accent h-4 w-4" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Facturació</p>
                                                    <p class="text-xs text-slate-500">Gestió de pressupostos i factures</p>
                                                </div>
                                            </div>
                                            <DropdownIcon class="h-3 w-3 text-slate-400" />
                                        </NewNavLink>
                                        <div v-if="isBillingPage" class="ml-4 space-y-2 border-l border-slate-200 pl-4">
                                            <NewNavLink :href="route('invoices.index')" :active="route().current('invoices.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuInvoiceIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Factures</span>
                                                </div>
                                            </NewNavLink>
                                            <NewNavLink :href="route('budgets.index')" :active="route().current('budgets.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuBudgetIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Pressupostos</span>
                                                </div>
                                            </NewNavLink>
                                            <NewNavLink :href="route('parts.index')" :active="route().current('parts.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuPartIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Partes</span>
                                                </div>
                                            </NewNavLink>
                                        </div>
                                    </div>

                                    <div v-if="hasFeature('Inventario')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Magatzem</p>
                                        <NewNavLink :href="route('dashboard.products')" :active="route().current('dashboard.products')">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                    <MenuInventoryIcon class="icon-accent h-4 w-4" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Almacén</p>
                                                    <p class="text-xs text-slate-500">Control d'estoc i categories</p>
                                                </div>
                                            </div>
                                            <DropdownIcon class="h-3 w-3 text-slate-400" />
                                        </NewNavLink>
                                        <div v-if="isProductsPage" class="ml-4 space-y-2 border-l border-slate-200 pl-4">
                                            <NewNavLink :href="route('products.index')" :active="route().current('products.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuProductIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Productes</span>
                                                </div>
                                            </NewNavLink>
                                            <NewNavLink :href="route('suppliers.index')" :active="route().current('suppliers.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuClientsIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Proveïdors</span>
                                                </div>
                                            </NewNavLink>
                                            <NewNavLink :href="route('stockEntries.index')" :active="route().current('stockEntries.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <AddProductIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Entrades de stock</span>
                                                </div>
                                            </NewNavLink>
                                            <NewNavLink :href="route('categories.index')" :active="route().current('categories.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuCategoryIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Categories</span>
                                                </div>
                                            </NewNavLink>
                                        </div>
                                    </div>

                                    <div v-if="hasFeature('Clientes')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Clients</p>
                                        <NewNavLink :href="route('dashboard.clients')" :active="route().current('dashboard.clients')">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                    <MenuClientsIcon class="icon-accent h-4 w-4" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Clients</p>
                                                    <p class="text-xs text-slate-500">Gestió de comptes i relacions</p>
                                                </div>
                                            </div>
                                        </NewNavLink>
                                    </div>

                                    <div v-if="hasFeature('Contabilidad')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Finances</p>
                                        <NewNavLink :href="route('dashboard.accounting')" :active="route().current('dashboard.accounting')">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                    <MenuAccountingIcon class="icon-accent h-4 w-4" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Comptabilitat</p>
                                                    <p class="text-xs text-slate-500">Informes i seguiment econòmic</p>
                                                </div>
                                            </div>
                                            <DropdownIcon class="h-3 w-3 text-slate-400" />
                                        </NewNavLink>
                                        <div v-if="isAccountingPage" class="ml-4 space-y-2 border-l border-slate-200 pl-4">
                                            <NewNavLink :href="route('expenses.report')" :active="route().current('expenses.report')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuReportIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Informes</span>
                                                </div>
                                            </NewNavLink>
                                            <NewNavLink :href="route('incomes.index')" :active="route().current('incomes.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <IncomeIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Ingressos</span>
                                                </div>
                                            </NewNavLink>
                                            <NewNavLink :href="route('expenses.index')" :active="route().current('expenses.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuExpenseIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Despeses</span>
                                                </div>
                                            </NewNavLink>
                                            <NewNavLink :href="route('expenseCategories.index')" :active="route().current('expenseCategories.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuCategoryIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Categories</span>
                                                </div>
                                            </NewNavLink>
                                            <NewNavLink :href="route('paymentMethods.index')" :active="route().current('paymentMethods.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuPaymentIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Mètodes de pagament</span>
                                                </div>
                                            </NewNavLink>
                                        </div>
                                    </div>

                                    <div v-if="hasFeature('Agenda')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Agenda</p>
                                        <NewNavLink :href="route('appointments.index')" :active="route().current('appointments.index')">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                    <MenuCalendarIcon class="icon-accent h-4 w-4" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Cites</p>
                                                    <p class="text-xs text-slate-500">Agenda i reunions</p>
                                                </div>
                                            </div>
                                        </NewNavLink>
                                    </div>

                                    <div v-if="hasFeature('CRM')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">CRM</p>
                                        <NewNavLink :href="route('dashboard.crm')" :active="route().current('dashboard.crm')">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                    <MenuCRMIcon class="icon-accent h-4 w-4" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">CRM</p>
                                                    <p class="text-xs text-slate-500">Oportunitats i seguiment comercial</p>
                                                </div>
                                            </div>
                                            <DropdownIcon class="h-3 w-3 text-slate-400" />
                                        </NewNavLink>
                                        <div v-if="isCrmPage" class="ml-4 space-y-2 border-l border-slate-200 pl-4">
                                            <NewNavLink :href="route('leads.index')" :active="route().current('leads.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuClientsIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Leads</span>
                                                </div>
                                            </NewNavLink>
                                            <NewNavLink :href="route('opportunities.index')" :active="route().current('opportunities.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuCategoryIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Oportunitats</span>
                                                </div>
                                            </NewNavLink>
                                        </div>
                                    </div>

                                    <div v-if="hasFeature('Administradores')" class="space-y-3">
                                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Administració</p>
                                        <NewNavLink :href="route('dashboard.admin')" :active="route().current('dashboard.admin')">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                    <MenuReportIcon class="icon-accent h-4 w-4" />
                                                </span>
                                                <div class="text-left">
                                                    <p class="text-sm font-semibold">Administració</p>
                                                    <p class="text-xs text-slate-500">Usuaris i rols del sistema</p>
                                                </div>
                                            </div>
                                            <DropdownIcon class="h-3 w-3 text-slate-400" />
                                        </NewNavLink>
                                        <div v-if="isAdminPage" class="ml-4 space-y-2 border-l border-slate-200 pl-4">
                                            <NewNavLink :href="route('users.index')" :active="route().current('users.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuClientsIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Usuaris</span>
                                                </div>
                                            </NewNavLink>
                                            <NewNavLink :href="route('user_tasks.index')" :active="route().current('user_tasks.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuInvoiceIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Tasques</span>
                                                </div>
                                            </NewNavLink>
                                            <NewNavLink :href="route('roles.index')" :active="route().current('roles.index')" variant="sub">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200">
                                                        <MenuCategoryIcon class="icon-accent h-4 w-4 text-slate-500" />
                                                    </span>
                                                    <span class="text-sm">Rols</span>
                                                </div>
                                            </NewNavLink>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>
  <main class="flex-1 min-w-0">
                        <div class="app-section">
                            <div class="app-card text-slate-700">
                                <slot />
                            </div>
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
import MenuPartIcon from "@/Components/Icons/MenuPartIcon.vue";
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
import AddProductIcon from "@/Components/Icons/AddProductIcon.vue";
import IncomeIcon from "@/Components/Icons/IncomeIcon.vue";
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import MenuHomeIcon from "@/Components/Icons/MenuHomeIcon.vue";
import MenuCalendarIcon from "@/Components/Icons/MenuCalendarIcon.vue";

const features = computed(() => usePage().props.features);

const hasFeature = (featureName) => features.value?.some((feature) => feature.name === featureName);

const isBillingPage = route().current('dashboard.billing') || route().current('budgets.index') || route().current('invoices.index') || route().current('parts.index') || route().current('parts.create');
const isProductsPage = route().current('products.index') || route().current('categories.index') || route().current('dashboard.products') || route().current('suppliers.index') || route().current('stockEntries.index');
const isAccountingPage = route().current('expenses.index') || route().current('expenses.report') || route().current('dashboard.accounting') || route().current('expenseCategories.index') || route().current('paymentMethods.index') || route().current('incomes.index');
const isCrmPage = route().current('dashboard.crm') || route().current('leads.index') || route().current('opportunities.index') || route().current('tasks.index') || route().current('activities.index') || route().current('notes.index') || route().current('leads.create') || route().current('opportunities.create') || route().current('tasks.create') || route().current('activities.create') || route().current('notes.create') || route().current('leads.edit') || route().current('opportunities.edit') || route().current('tasks.edit') || route().current('activities.edit') || route().current('notes.edit') || route().current('leads.show') || route().current('opportunities.show') || route().current('tasks.show') || route().current('activities.show') || route().current('notes.show');
const isAdminPage = route().current('dashboard.admin') || route().current('users.index') || route().current('roles.index') || route().current('permissions.index') || route().current('users.create') || route().current('roles.create') || route().current('permissions.create') || route().current('users.edit') || route().current('roles.edit') || route().current('permissions.edit') || route().current('users.show') || route().current('roles.show') || route().current('permissions.show') || route().current('users.destroy') || route().current('roles.destroy') || route().current('permissions.destroy') || route().current('user_tasks.index') || route().current('user_tasks.adminCreate') || route().current('user_tasks.adminEdit') || route().current('user_tasks.destroy');
</script>

<style scoped>
.app-section {
    @apply h-full w-full rounded-2xl border border-slate-200 bg-white/70 px-4 py-6 shadow-sm backdrop-blur lg:px-8 lg:py-10;
}

.app-card {
    @apply rounded-2xl border border-slate-200/70 bg-white p-6 shadow-sm;
}
</style>
