<script setup>
import { computed, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import CrudStatCard from '@/Components/Crud/CrudStatCard.vue';
import CrudFilterBar from '@/Components/Crud/CrudFilterBar.vue';
import CrudTable from '@/Components/Crud/CrudTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import MenuRRHHIcon from '@/Components/Icons/MenuRRHHIcon.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import InfoIcon from '@/Components/Icons/InfoIcon.vue';

const props = defineProps({
    employees: {
        type: Array,
        default: () => [],
    },
    departments: {
        type: Array,
        default: () => [],
    },
});

const filters = reactive({
    search: '',
    department_id: '',
    status: '',
});

const filteredEmployees = computed(() => {
    const searchTerm = filters.search.trim().toLowerCase();

    return props.employees.filter((employee) => {
        const matchesSearch =
            !searchTerm ||
            employee.name?.toLowerCase().includes(searchTerm) ||
            employee.job_title?.toLowerCase().includes(searchTerm);
        const matchesDepartment = !filters.department_id || Number(filters.department_id) === Number(employee.department_id);
        const matchesStatus = !filters.status || employee.status === filters.status;

        return matchesSearch && matchesDepartment && matchesStatus;
    });
});

const totalEmployees = computed(() => filteredEmployees.value.length);
const activeEmployees = computed(() => filteredEmployees.value.filter((employee) => employee.status === 'activo').length);
const inactiveEmployees = computed(() => filteredEmployees.value.filter((employee) => employee.status === 'inactivo').length);

const clearFilters = () => {
    filters.search = '';
    filters.department_id = '';
    filters.status = '';
};

const deleteEmployee = (employeeId) => {
    if (confirm('Segur que vols eliminar aquest empleat?')) {
        Inertia.delete(route('employees.destroy', employeeId));
    }
};

const departmentName = (departmentId) =>
    props.departments.find((department) => Number(department.id) === Number(departmentId))?.name ?? '—';
</script>

<template>
    <AppLayout title="Equip humà">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Gestió d'empleats"
                    description="Filtra i administra el talent de l'organització amb una vista clara i coherent."
                    :icon="MenuRRHHIcon"
                >
                    <template #actions>
                        <Link
                            :href="route('employees.create')"
                            class="inline-flex items-center gap-2 rounded-full bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200 transition hover:bg-sky-500"
                        >
                            <AddIcon class="h-5 w-5" />
                            Nou empleat
                        </Link>
                    </template>
                </CrudPageHeader>

                <div class="grid gap-6 md:grid-cols-3">
                    <CrudStatCard
                        label="Total plantilla"
                        :value="totalEmployees"
                        :icon="MenuRRHHIcon"
                        icon-background="bg-indigo-500/10 text-indigo-600"
                    />
                    <CrudStatCard
                        label="Actius"
                        :value="activeEmployees"
                        :icon="AddIcon"
                        icon-background="bg-emerald-500/10 text-emerald-600"
                    />
                    <CrudStatCard
                        label="Inactius"
                        :value="inactiveEmployees"
                        :icon="InfoIcon"
                        icon-background="bg-amber-500/10 text-amber-600"
                    />
                </div>

                <CrudFilterBar>
                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="employee-search" value="Cerca" />
                        <TextInput
                            id="employee-search"
                            v-model="filters.search"
                            type="search"
                            class="block w-full"
                            placeholder="Nom o càrrec"
                        />
                    </div>

                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="employee-department" value="Departament" />
                        <SelectInput id="employee-department" v-model="filters.department_id" class="block w-full">
                            <option value="">Tots</option>
                            <option v-for="department in departments" :key="department.id" :value="department.id">
                                {{ department.name }}
                            </option>
                        </SelectInput>
                    </div>

                    <div class="flex flex-1 flex-col gap-2">
                        <InputLabel for="employee-status" value="Estat" />
                        <SelectInput id="employee-status" v-model="filters.status" class="block w-full">
                            <option value="">Tots</option>
                            <option value="activo">Actiu</option>
                            <option value="inactivo">Inactiu</option>
                        </SelectInput>
                    </div>

                    <template #actions>
                        <SecondaryButton type="button" @click="clearFilters">
                            Netejar filtres
                        </SecondaryButton>
                    </template>
                </CrudFilterBar>

                <CrudTable>
                    <template #head>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nom</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Càrrec</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Departament</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Estat</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Accions</th>
                        </tr>
                    </template>

                    <tr v-for="employee in filteredEmployees" :key="employee.id" class="hover:bg-slate-50/60">
                        <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ employee.name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ employee.job_title }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ departmentName(employee.department_id) }}</td>
                        <td class="px-6 py-4 text-sm font-semibold" :class="employee.status === 'activo' ? 'text-emerald-600' : 'text-amber-600'">
                            {{ employee.status === 'activo' ? 'Actiu' : 'Inactiu' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="route('employees.show', employee.id)"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-500/10 text-sky-600 transition hover:bg-sky-500/20"
                                >
                                    <InfoIcon class="h-5 w-5" />
                                </Link>
                                <Link
                                    :href="route('employees.edit', employee.id)"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-amber-500/10 text-amber-600 transition hover:bg-amber-500/20"
                                >
                                    <EditIcon class="h-5 w-5" />
                                </Link>
                                <button
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-500/10 text-rose-600 transition hover:bg-rose-500/20"
                                    @click="deleteEmployee(employee.id)"
                                >
                                    <DeleteIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </td>
                    </tr>

                    <template v-if="!filteredEmployees.length">
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
                                Cap empleat compleix els filtres actuals.
                            </td>
                        </tr>
                    </template>
                </CrudTable>
            </div>
        </div>
    </AppLayout>
</template>
