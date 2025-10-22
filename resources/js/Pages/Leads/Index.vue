<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 pb-20">
            <div class="bg-gradient-to-r from-violet-700 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2">
                        <p class="text-violet-200 text-sm uppercase tracking-widest">Gestión de leads</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Impulsa tus oportunidades comerciales</h1>
                        <p class="text-sm text-violet-200">Centraliza el seguimiento de leads y prepara la conversión hacia oportunidades.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <StatsCard label="Leads" :value="totalLeads" :hint="`Activos ${activeLeads}`" />
                        <StatsCard label="Conversión estimada" :value="conversionRate + '%'" :hint="`Seguidos ${followUpLeads}`" />
                        <StatsCard label="Nuevos este mes" :value="recentLeads" :hint="`Último ingreso ${latestLeadDate}`" />
                        <StatsCard label="Fuentes" :value="uniqueSources" :hint="topSourceLabel" />
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 space-y-10">
                <SectionCard title="Flujo comercial" description="Controla la transición de lead a oportunidad y asegúrate de que cada contacto tenga un siguiente paso definido.">
                    <FlowStepper :steps="flowSteps" :active-index="0" />
                </SectionCard>

                <SectionCard title="Leads" description="Filtra y gestiona tu cartera actual para planificar la conversión.">
                    <template #actions>
                        <div class="flex flex-wrap items-center gap-3">
                            <input v-model="filters.search" type="text" placeholder="Buscar por nombre o empresa" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200" />
                            <select v-model="filters.status" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                <option value="all">Todos los estados</option>
                                <option value="active">Activos</option>
                                <option value="qualified">Calificados</option>
                                <option value="inactive">Inactivos</option>
                            </select>
                            <select v-model="filters.source" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                <option value="all">Todas las fuentes</option>
                                <option v-for="source in sources" :key="source" :value="source">{{ source }}</option>
                            </select>
                            <button @click="downloadExport('csv')" type="button" class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-violet-300 hover:text-violet-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="h-4 w-4 stroke-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4m12 8H4" />
                                </svg>
                                CSV
                            </button>
                            <button @click="downloadExport('excel')" type="button" class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-violet-300 hover:text-violet-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="h-4 w-4 stroke-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h9l7 7v9a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m10 9 4 6m0-6-4 6" />
                                </svg>
                                Excel
                            </button>
                            <button @click="openImportModal" type="button" class="inline-flex items-center gap-2 rounded-full border border-violet-200 px-4 py-2 text-sm font-semibold text-violet-600 transition hover:bg-violet-600 hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="h-4 w-4 stroke-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Importar contactos
                            </button>
                            <NavLink :href="route('leads.create')" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/40 transition hover:bg-violet-500">
                                <span>Nuevo lead</span>
                                <AddIcon class="h-4 w-4 fill-white" />
                            </NavLink>
                        </div>
                    </template>

                    <div v-if="importSummary" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 text-sm text-emerald-700">
                        <p class="font-semibold text-emerald-900">Importación completada</p>
                        <p class="mt-1">Se crearon {{ importSummary.created }} leads, se actualizaron {{ importSummary.updated }} y se omitieron {{ importSummary.skipped }} registros.</p>
                    </div>
                    <div v-else-if="importError" class="rounded-2xl border border-red-200 bg-red-50 p-6 text-sm text-red-600">
                        {{ importError }}
                    </div>
                    <div v-else-if="hasError" class="rounded-2xl border border-red-200 bg-red-50 p-6 text-sm text-red-600">
                        {{ errorMessage }}
                    </div>
                    <div v-else-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="i in 4" :key="i" class="animate-pulse rounded-2xl border border-slate-200/60 bg-white/60 p-6">
                            <div class="h-4 w-24 rounded-full bg-slate-200"></div>
                            <div class="mt-4 h-6 w-3/4 rounded-full bg-slate-200"></div>
                            <div class="mt-6 space-y-2">
                                <div class="h-3 w-full rounded-full bg-slate-100"></div>
                                <div class="h-3 w-5/6 rounded-full bg-slate-100"></div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="filteredLeads.length" class="space-y-4">
                            <div v-for="lead in filteredLeads" :key="lead.id" class="rounded-2xl border border-slate-200/70 p-6 transition hover:border-violet-200 hover:shadow-lg hover:shadow-violet-200/30">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-semibold text-slate-800">{{ lead.name }}</h3>
                                            <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-violet-600">
                                                {{ lead.status ?? 'Sin estado' }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-slate-500">{{ lead.company ?? lead.email ?? 'Sin información de contacto' }}</p>
                                        <div class="mt-4 flex flex-wrap gap-3 text-xs text-slate-500">
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.414.336-.75.75-.75h4.5a.75.75 0 01.75.75v1.5a.75.75 0 01-.75.75H4.5v9h3a.75.75 0 010 1.5h-3A1.5 1.5 0 013 18.75v-12zm12 1.5a.75.75 0 01.75-.75h4.5c.414 0 .75.336.75.75v12a1.5 1.5 0 01-1.5 1.5h-3a.75.75 0 010-1.5h3v-9h-3a.75.75 0 01-.75-.75v-1.5z" />
                                                </svg>
                                                {{ lead.source ?? 'Fuente desconocida' }}
                                            </span>
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75V19.5A1.5 1.5 0 006 21h12a1.5 1.5 0 001.5-1.5V9.75M8.25 21v-6a1.5 1.5 0 011.5-1.5h4.5a1.5 1.5 0 011.5 1.5v6" />
                                                </svg>
                                                {{ lead.city ?? 'Localización no definida' }}
                                            </span>
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ formatDate(lead.created_at) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-start gap-3 sm:items-end">
                                        <div class="flex gap-2">
                                            <NavLink :href="route('leads.show', lead.id)" class="rounded-full border border-violet-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-violet-600 transition hover:bg-violet-600 hover:text-white">Ver</NavLink>
                                            <NavLink :href="route('leads.edit', lead.id)" class="rounded-full border border-amber-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-600 transition hover:bg-amber-500 hover:text-white">Editar</NavLink>
                                            <button @click="deleteLead(lead.id)" class="rounded-full border border-red-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-red-500 transition hover:bg-red-500 hover:text-white">Eliminar</button>
                                        </div>
                                        <p class="text-xs text-slate-400">Probabilidad estimada: {{ lead.probability ? `${lead.probability}%` : 'Pendiente' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <EmptyState v-else title="Aún no tienes leads" description="Registra contactos comerciales y comienza a nutrir tu pipeline.">
                            <template #action>
                                <NavLink :href="route('leads.create')" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/40 transition hover:bg-violet-500">
                                    <AddIcon class="h-4 w-4 fill-white" />
                                    Crear primer lead
                                </NavLink>
                            </template>
                        </EmptyState>
                    </div>
                </SectionCard>
            </div>
        </div>

        <Modal :show="showImportModal" @close="closeImportModal" max-width="3xl">
            <div class="p-6 sm:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Importar contactos</h2>
                        <p class="mt-1 text-sm text-slate-500">Sube un archivo CSV o Excel y asigna cada columna a los campos del lead.</p>
                    </div>
                    <button type="button" class="rounded-full bg-slate-100 p-2 text-slate-500 transition hover:bg-slate-200" @click="closeImportModal">
                        <span class="sr-only">Cerrar</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="h-4 w-4 stroke-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6m0 12L6 6" />
                        </svg>
                    </button>
                </div>

                <div class="mt-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Archivo</label>
                        <div class="mt-2 flex items-center gap-3">
                            <input ref="fileInput" type="file" accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" @change="handleFileUpload" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-full file:border-0 file:bg-violet-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-violet-600 hover:file:bg-violet-100" />
                        </div>
                        <p v-if="selectedFileName" class="mt-2 text-xs text-slate-500">{{ selectedFileName }}</p>
                    </div>

                    <div v-if="availableColumns.length" class="space-y-4">
                        <p class="text-sm font-semibold text-slate-600">Asignación de columnas</p>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div v-for="field in leadFields" :key="field.key" class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">{{ field.label }}</label>
                                <select v-model="fieldMappings[field.key]" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                    <option :value="null">Ignorar</option>
                                    <option v-for="column in availableColumns" :key="column" :value="column">{{ column }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div v-if="mappedPreview.length" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Vista previa</p>
                        <ul class="mt-3 space-y-3 text-sm text-slate-600">
                            <li v-for="(preview, index) in mappedPreview" :key="index" class="rounded-xl bg-white p-3 shadow-sm">
                                <div v-for="field in leadFields" :key="field.key" class="flex justify-between text-xs sm:text-sm">
                                    <span class="font-semibold text-slate-500">{{ field.label }}</span>
                                    <span class="max-w-[60%] truncate text-slate-700">{{ preview[field.key] ?? '—' }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div v-if="parsingError" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                        {{ parsingError }}
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <SecondaryButton type="button" @click="closeImportModal">Cancelar</SecondaryButton>
                    <PrimaryButton type="button" :disabled="!canSubmitImport || importForm.processing" @click="submitImport">
                        <span v-if="importForm.processing">Importando...</span>
                        <span v-else>Importar leads</span>
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import StatsCard from '@/Components/Crm/StatsCard.vue';
import SectionCard from '@/Components/Crm/SectionCard.vue';
import FlowStepper from '@/Components/Crm/FlowStepper.vue';
import EmptyState from '@/Components/Crm/EmptyState.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
let xlsxModulePromise = null;

const loadXLSX = async () => {
    if (!xlsxModulePromise) {
        xlsxModulePromise = import('xlsx/xlsx.mjs').then(module => module.default ?? module);
    }

    return xlsxModulePromise;
};

const props = defineProps({
    leads: {
        type: Array,
        default: () => [],
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    error: {
        type: [String, Object],
        default: null,
    },
    importSummary: {
        type: Object,
        default: null,
    },
    importError: {
        type: String,
        default: null,
    },
});

const filters = reactive({
    search: '',
    status: 'all',
    source: 'all',
});

const leads = computed(() => props.leads ?? []);
const totalLeads = computed(() => leads.value.length);
const activeLeads = computed(() => leads.value.filter(lead => ['active', 'qualified'].includes((lead.status || '').toLowerCase())).length);
const followUpLeads = computed(() => leads.value.filter(lead => ['follow_up', 'seguimiento'].includes((lead.status || '').toLowerCase())).length);
const recentLeads = computed(() => leads.value.filter(lead => {
    if (!lead.created_at) return false;
    const created = new Date(lead.created_at);
    const now = new Date();
    return created.getMonth() === now.getMonth() && created.getFullYear() === now.getFullYear();
}).length);
const latestLeadDate = computed(() => {
    if (!leads.value.length) return '—';
    const latest = [...leads.value].sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0))[0];
    return latest?.created_at ? formatDate(latest.created_at) : '—';
});
const uniqueSources = computed(() => {
    const set = new Set(leads.value.map(lead => lead.source).filter(Boolean));
    return set.size || '—';
});
const topSourceLabel = computed(() => {
    if (!leads.value.length) return 'Sin datos de fuente';
    const counts = leads.value.reduce((acc, lead) => {
        const source = lead.source ?? 'Desconocida';
        acc[source] = (acc[source] || 0) + 1;
        return acc;
    }, {});
    const [top] = Object.entries(counts).sort((a, b) => b[1] - a[1])[0] || [];
    return top ? `Principal: ${top}` : 'Sin datos de fuente';
});
const conversionRate = computed(() => {
    if (!totalLeads.value) return 0;
    const converted = leads.value.filter(lead => ['qualified', 'opportunity', 'ganado'].includes((lead.status || '').toLowerCase())).length;
    return Math.round((converted / totalLeads.value) * 100);
});

const sources = computed(() => {
    const set = new Set(leads.value.map(lead => lead.source).filter(Boolean));
    return Array.from(set);
});

const filteredLeads = computed(() => {
    return leads.value.filter(lead => {
        const matchesSearch = filters.search
            ? [lead.name, lead.company, lead.email].some(value => value?.toLowerCase().includes(filters.search.toLowerCase()))
            : true;
        const status = (lead.status || 'sin_estado').toLowerCase();
        const matchesStatus = filters.status === 'all' ? true : status === filters.status || (filters.status === 'active' && ['active', 'activo'].includes(status));
        const matchesSource = filters.source === 'all' ? true : (lead.source ?? '').toLowerCase() === filters.source.toLowerCase();
        return matchesSearch && matchesStatus && matchesSource;
    });
});

const flowSteps = computed(() => ([
    {
        label: 'Lead',
        description: 'Captación y registro del contacto',
    },
    {
        label: 'Oportunidad',
        description: 'Calificación y valoración económica',
    },
    {
        label: 'Tareas',
        description: 'Acciones de seguimiento asignadas',
    },
]));

const isLoading = computed(() => props.isLoading);
const hasError = computed(() => Boolean(props.error));
const errorMessage = computed(() => (typeof props.error === 'string' ? props.error : props.error?.message ?? 'No se pudo cargar la información.'));
const importSummary = computed(() => props.importSummary ?? null);
const importError = computed(() => props.importError ?? null);

const deleteLead = leadId => {
    if (confirm('¿Estás seguro de que deseas eliminar este lead?')) {
        Inertia.delete(route('leads.destroy', leadId));
    }
};

const formatDate = date => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString();
};

const fileInput = ref(null);
const showImportModal = ref(false);
const selectedFileName = ref('');
const availableColumns = ref([]);
const parsedRows = ref([]);
const parsingError = ref('');
const leadFields = [
    { key: 'name', label: 'Nombre' },
    { key: 'company_name', label: 'Empresa' },
    { key: 'email', label: 'Correo electrónico' },
    { key: 'phone', label: 'Teléfono' },
    { key: 'position', label: 'Cargo' },
    { key: 'source', label: 'Fuente' },
    { key: 'status', label: 'Estado' },
];
const fieldMappings = reactive({});

leadFields.forEach(field => {
    fieldMappings[field.key] = null;
});

const importForm = useForm({
    records: [],
});

const resetImportState = () => {
    selectedFileName.value = '';
    availableColumns.value = [];
    parsedRows.value = [];
    parsingError.value = '';
    leadFields.forEach(field => {
        fieldMappings[field.key] = null;
    });
    if (fileInput.value) {
        fileInput.value.value = null;
    }
};

watch(showImportModal, value => {
    if (!value) {
        resetImportState();
    }
});

const openImportModal = () => {
    showImportModal.value = true;
};

const closeImportModal = () => {
    showImportModal.value = false;
};

const normalizeHeader = header => String(header || '').toLowerCase().replace(/[^a-z0-9]/g, '');

const autoMapFields = headers => {
    const normalizedHeaders = headers.map(normalizeHeader);

    leadFields.forEach(field => {
        const normalizedFieldLabel = normalizeHeader(field.label);
        const normalizedFieldKey = normalizeHeader(field.key);
        const index = normalizedHeaders.findIndex(header => header === normalizedFieldLabel || header === normalizedFieldKey);
        fieldMappings[field.key] = index !== -1 ? headers[index] : null;
    });
};

const handleFileUpload = event => {
    const [file] = event.target.files || [];
    if (!file) {
        return;
    }

    selectedFileName.value = file.name;
    parseFile(file);
};

const parseFile = file => {
    parsingError.value = '';
    const reader = new FileReader();

    reader.onload = async e => {
        try {
            const XLSX = await loadXLSX();
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const [sheetName] = workbook.SheetNames;
            const worksheet = workbook.Sheets[sheetName];

            const rows = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: '' });

            if (!rows.length) {
                availableColumns.value = [];
                parsedRows.value = [];
                parsingError.value = 'El archivo seleccionado está vacío.';
                return;
            }

            const headers = (rows[0] || [])
                .map(cell => (cell === null || cell === undefined ? '' : String(cell).trim()))
                .filter(header => header.length);

            if (!headers.length) {
                availableColumns.value = [];
                parsedRows.value = [];
                parsingError.value = 'No se encontraron cabeceras en el archivo. Añade una fila inicial con los nombres de las columnas.';
                return;
            }

            availableColumns.value = headers;
            autoMapFields(headers);

            const dataRows = rows.slice(1)
                .map(row => {
                    const record = {};
                    headers.forEach((header, index) => {
                        record[header] = row[index] ?? '';
                    });
                    return record;
                })
                .filter(record => Object.values(record).some(value => String(value ?? '').trim().length));

            parsedRows.value = dataRows;

            if (!dataRows.length) {
                parsingError.value = 'No se encontraron registros para importar.';
            }
        } catch (error) {
            console.error(error);
            parsingError.value = 'No se pudo interpretar el archivo. Asegúrate de que tenga un formato CSV o Excel válido.';
            availableColumns.value = [];
            parsedRows.value = [];
        }
    };

    reader.onerror = () => {
        parsingError.value = 'Ocurrió un error al leer el archivo. Inténtalo nuevamente.';
    };

    reader.readAsArrayBuffer(file);
};

const mappedPreview = computed(() => {
    return parsedRows.value.slice(0, 3).map(row => {
        const preview = {};
        Object.entries(fieldMappings).forEach(([field, column]) => {
            preview[field] = column ? row[column] ?? '' : '';
        });
        return preview;
    });
});

const canSubmitImport = computed(() => {
    const hasRecords = parsedRows.value.length > 0;
    const hasIdentifiers = Boolean(fieldMappings.name || fieldMappings.email);
    const hasAnyMapping = Object.values(fieldMappings).some(Boolean);

    return hasRecords && hasIdentifiers && hasAnyMapping;
});

const submitImport = () => {
    if (!canSubmitImport.value) {
        parsingError.value = 'Selecciona al menos la columna de nombre o correo electrónico y asegúrate de que existan registros válidos.';
        return;
    }

    const records = parsedRows.value
        .map(row => {
            const record = {};
            Object.entries(fieldMappings).forEach(([field, column]) => {
                if (!column) {
                    return;
                }
                const value = row[column];
                record[field] = typeof value === 'string' ? value.trim() : value;
            });
            return record;
        })
        .filter(record => Object.values(record).some(value => (value ?? '').toString().trim().length));

    if (!records.length) {
        parsingError.value = 'No se encontraron filas con información válida después de aplicar el mapeo.';
        return;
    }

    importForm.records = records;
    importForm.post(route('leads.import'), {
        onSuccess: () => {
            showImportModal.value = false;
            resetImportState();
        },
        onError: () => {
            parsingError.value = 'No se pudo completar la importación. Revisa los datos e inténtalo nuevamente.';
        },
    });
};

const downloadExport = type => {
    const url = type === 'excel' ? route('leads.export.excel') : route('leads.export.csv');
    window.location.href = url;
};
</script>
