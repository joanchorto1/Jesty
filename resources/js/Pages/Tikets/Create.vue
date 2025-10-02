<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-fuchsia-600 via-purple-600 to-slate-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2 text-white">
                        <p class="uppercase tracking-[0.3em] text-fuchsia-200 text-xs">Venda tàctil</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold">Nou ticket TPV</h1>
                        <p class="text-sm text-fuchsia-200 max-w-2xl">
                            Cerca, afegeix i cobra articles amb un flux àgil i consistent amb la resta de vistes del mòdul.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-10">
                        <KpiCard
                            v-for="card in workspaceCards"
                            :key="card.key"
                            gradient
                            :subtitle="card.subtitle"
                            :value="card.value"
                            :description="card.description"
                        />
                    </div>
                </div>
            </div>

            <main class="max-w-7xl mx-auto px-6 -mt-16 pb-20 space-y-10">
                <section class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition"
                            :class="isVenta ? 'border-fuchsia-400 bg-fuchsia-50 text-fuchsia-600' : 'border-slate-200 text-slate-600 hover:border-fuchsia-300 hover:text-fuchsia-500'"
                            @click="setVenta(true)"
                        >
                            Venda ràpida
                        </button>
                        <button
                            class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition"
                            :class="!isVenta ? 'border-fuchsia-400 bg-fuchsia-50 text-fuchsia-600' : 'border-slate-200 text-slate-600 hover:border-fuchsia-300 hover:text-fuchsia-500'"
                            @click="setVenta(false)"
                        >
                            Gestió d'articles
                        </button>
                    </div>
                </section>

                <section class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="grid gap-8 xl:grid-cols-[2fr,1fr]">
                        <div class="space-y-8">
                            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                <label class="flex flex-col gap-2">
                                    <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">Codi de barres</span>
                                    <input
                                        v-model="codebar"
                                        id="codebar"
                                        type="text"
                                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-fuchsia-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
                                        placeholder="Escaneja o escriu el codi"
                                    />
                                </label>
                                <label class="flex flex-col gap-2">
                                    <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">Nom del producte</span>
                                    <input
                                        v-model="name"
                                        id="name"
                                        type="text"
                                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-fuchsia-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
                                        placeholder="Escriu per filtrar"
                                    />
                                </label>
                                <div class="flex items-end">
                                    <button
                                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-rose-300 hover:text-rose-500"
                                        @click="clearFilters"
                                    >
                                        Netejar filtres
                                    </button>
                                </div>
                            </div>

                            <div v-if="isVenta" class="space-y-6">
                                <div>
                                    <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Categories</h2>
                                    <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                        <button
                                            v-for="category in categories"
                                            :key="category.id"
                                            class="rounded-2xl border px-4 py-3 text-left text-sm font-semibold transition hover:border-fuchsia-300 hover:text-fuchsia-500"
                                            :class="selectedCategory?.id === category.id ? 'border-fuchsia-400 bg-fuchsia-50 text-fuchsia-600' : 'border-slate-200 text-slate-700'"
                                            @click="selectCategory(category)"
                                        >
                                            {{ category.name }}
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Productes disponibles</h2>
                                    <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                        <button
                                            v-for="product in filteredProducts"
                                            :key="product.id"
                                            class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-5 text-left text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-1 hover:border-fuchsia-300 hover:bg-white hover:shadow-md"
                                            @click="addItemToTiket(product)"
                                        >
                                            <span class="block text-base font-semibold text-slate-900">{{ product.name }}</span>
                                            <span class="text-sm text-slate-500">{{ formatCurrency(product.price) }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="space-y-4">
                                <h2 class="text-lg font-semibold text-slate-800">Articles del ticket</h2>
                                <div class="overflow-hidden rounded-2xl border border-slate-100">
                                    <table class="min-w-full divide-y divide-slate-100 text-sm text-slate-600">
                                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-widest text-slate-500">
                                            <tr>
                                                <th class="px-4 py-3 text-left">Producte</th>
                                                <th class="px-4 py-3 text-center">Quantitat</th>
                                                <th class="px-4 py-3 text-center">Preu unitari</th>
                                                <th class="px-4 py-3 text-right">Total</th>
                                                <th class="px-4 py-3 text-right">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in tiketItems" :key="item.id" class="divide-y divide-slate-100">
                                                <td class="px-4 py-3 font-semibold text-slate-800">{{ resolveProductName(item.product_id) }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <input
                                                        v-model.number="item.quantity"
                                                        type="number"
                                                        min="1"
                                                        class="w-20 rounded-xl border border-slate-200 px-2 py-1 text-center text-sm focus:border-fuchsia-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
                                                        @input="updateItemTotal(item)"
                                                    />
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <input
                                                        v-model.number="item.unit_price"
                                                        type="number"
                                                        step="0.01"
                                                        class="w-24 rounded-xl border border-slate-200 px-2 py-1 text-center text-sm focus:border-fuchsia-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
                                                        @input="updateItemTotal(item)"
                                                    />
                                                </td>
                                                <td class="px-4 py-3 text-right font-semibold text-slate-800">{{ formatCurrency(item.total) }}</td>
                                                <td class="px-4 py-3 text-right">
                                                    <button class="text-rose-500 hover:text-rose-600" @click="removeItem(item.id)">
                                                        <DeleteIcon class="h-5 w-5" />
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr v-if="!tiketItems.length">
                                                <td class="px-4 py-6 text-center text-slate-400" colspan="5">Encara no hi ha articles seleccionats.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <aside class="space-y-6">
                            <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6">
                                <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Últimes afegides</h2>
                                <ul class="mt-4 space-y-4 text-sm text-slate-600">
                                    <li v-for="(item, index) in latestItems" :key="index" class="flex items-center justify-between gap-4">
                                        <span class="flex-1 font-semibold text-slate-800">{{ resolveProductName(item.product_id) }}</span>
                                        <div class="flex items-center gap-2">
                                            <input
                                                v-model.number="item.quantity"
                                                type="number"
                                                min="1"
                                                class="w-16 rounded-xl border border-slate-200 px-2 py-1 text-center text-xs focus:border-fuchsia-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
                                                @input="updateItemTotal(item)"
                                            />
                                            <input
                                                v-model.number="item.unit_price"
                                                type="number"
                                                step="0.01"
                                                class="w-20 rounded-xl border border-slate-200 px-2 py-1 text-center text-xs focus:border-fuchsia-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
                                                @input="updateItemTotal(item)"
                                            />
                                        </div>
                                        <span class="text-right font-semibold text-slate-800">{{ formatCurrency(item.total) }}</span>
                                        <button class="text-rose-500 hover:text-rose-600" @click="removeItem(item.id)">
                                            <DeleteIcon class="h-4 w-4" />
                                        </button>
                                    </li>
                                    <li v-if="!latestItems.length" class="text-center text-slate-400">Interacciona amb la graella per veure els últims articles.</li>
                                </ul>
                            </div>

                            <div class="rounded-3xl border border-slate-100 bg-white p-6 space-y-4">
                                <label class="flex flex-col gap-2 text-sm text-slate-600">
                                    <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">IVA (%)</span>
                                    <input
                                        v-model.number="iva"
                                        type="number"
                                        min="0"
                                        class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-fuchsia-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
                                    />
                                </label>
                                <div class="space-y-2 text-sm text-slate-600">
                                    <div class="flex items-center justify-between"><span>Base imposable</span><span class="font-semibold text-slate-800">{{ formatCurrency(tiketData.base_imponible) }}</span></div>
                                    <div class="flex items-center justify-between"><span>IVA</span><span class="font-semibold text-slate-800">{{ formatCurrency(tiketData.monto_iva) }}</span></div>
                                    <div class="flex items-center justify-between text-base font-semibold text-slate-900">
                                        <span>Total</span>
                                        <span>{{ formatCurrency(total) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-3">
                                <button
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-fuchsia-500 to-purple-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-purple-500/20 transition hover:from-fuchsia-400 hover:to-purple-500 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="!tiketItems.length"
                                    @click="openCobrarModal"
                                >
                                    <PayIcon class="h-5 w-5" />
                                    Cobrar ticket
                                </button>
                                <button
                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-600 transition hover:border-rose-300 hover:bg-white"
                                    :disabled="!tiketItems.length"
                                    @click="eliminarTodo"
                                >
                                    <DeleteIcon class="h-5 w-5" />
                                    Buidar ticket
                                </button>
                            </div>
                        </aside>
                    </div>
                </section>
            </main>
        </div>

        <teleport to="body">
            <Transition name="fade">
                <div
                    v-if="isCobrarModalOpen"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 px-4"
                    @click.self="closeCobrarModal"
                >
                    <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl">
                        <h2 class="text-xl font-semibold text-slate-800">Cobrar ticket</h2>
                        <p class="text-sm text-slate-500">Introdueix l'import rebut per calcular el canvi.</p>
                        <div class="mt-4 space-y-4">
                            <label class="flex flex-col gap-2 text-sm text-slate-600">
                                <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">Import rebut</span>
                                <input
                                    v-model.number="receivedAmount"
                                    type="number"
                                    min="0"
                                    class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-fuchsia-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
                                    @input="calculateChange"
                                />
                            </label>
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                <span>Canvi a tornar</span>
                                <span class="text-base font-semibold text-slate-900">{{ formatCurrency(change) }}</span>
                            </div>
                        </div>
                        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
                            <button class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-500 hover:border-slate-300 hover:text-slate-700" @click="closeCobrarModal">Cancel·lar</button>
                            <button class="rounded-2xl bg-gradient-to-r from-fuchsia-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-purple-500/20 transition hover:from-fuchsia-400 hover:to-purple-500" @click="confirmCobro">Confirmar cobrament</button>
                        </div>
                    </div>
                </div>
            </Transition>

            <Transition name="fade">
                <div
                    v-if="isImprimirModalOpen"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 px-4"
                    @click.self="closeImprimirModal"
                >
                    <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl">
                        <h2 class="text-xl font-semibold text-slate-800">Imprimir ticket</h2>
                        <p class="text-sm text-slate-500">Vols enviar el ticket a impressió immediatament?</p>
                        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
                            <button class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-500 hover:border-slate-300 hover:text-slate-700" @click="closeImprimirModal">Més tard</button>
                            <button class="rounded-2xl bg-gradient-to-r from-fuchsia-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-purple-500/20 transition hover:from-fuchsia-400 hover:to-purple-500" @click="imprimir">Imprimir ara</button>
                        </div>
                    </div>
                </div>
            </Transition>
        </teleport>
    </AppLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import KpiCard from '@/Components/KpiCard.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import PayIcon from '@/Components/Icons/PayIcon.vue';

const props = defineProps({
    categories: Array,
    products: Array,
});

const isCobrarModalOpen = ref(false);
const isImprimirModalOpen = ref(false);
const receivedAmount = ref(0);
const change = ref(0);

const isVenta = ref(true);
const selectedCategory = ref(null);
const tiketItems = reactive([]);
const iva = ref(21);
let tiketData = reactive({
    name: '',
    base_imponible: 0,
    iva: 0,
    monto_iva: 0,
    total: 0,
    tiketItems: [],
});

const codebar = ref('');
const name = ref('');

const filteredProducts = computed(() => {
    if (codebar.value) {
        return props.products.filter(product => product.codebar === codebar.value);
    }
    if (name.value) {
        return props.products.filter(product => product.name.toLowerCase().includes(name.value.toLowerCase()));
    }
    if (!selectedCategory.value) {
        return props.products;
    }
    return props.products.filter(product => product.category_id === selectedCategory.value.id);
});

const latestItems = computed(() => tiketItems.slice(-4));

const total = computed(() => {
    const subtotal = tiketItems.reduce((sum, item) => sum + parseFloat(item.total), 0);
    const montoIva = (subtotal * iva.value) / (100 + iva.value);
    const baseImponible = subtotal - montoIva;

    tiketData.base_imponible = baseImponible.toFixed(2);
    tiketData.monto_iva = montoIva.toFixed(2);
    tiketData.total = subtotal.toFixed(2);

    return subtotal.toFixed(2);
});

const formatCurrency = value => new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(Number(value) || 0);

const workspaceCards = computed(() => [
    {
        key: 'items-count',
        subtitle: 'Articles actius',
        value: tiketItems.length,
        description: 'Elements seleccionats al ticket en curs.',
    },
    {
        key: 'ticket-total',
        subtitle: 'Total actual',
        value: formatCurrency(total.value),
        description: 'Import amb impostos inclosos.',
    },
    {
        key: 'iva-percentage',
        subtitle: 'IVA configurat',
        value: `${iva.value}%`,
        description: 'Percentatge aplicat als càlculs.',
    },
    {
        key: 'category-selected',
        subtitle: 'Categoria activa',
        value: selectedCategory.value ? selectedCategory.value.name : 'Totes',
        description: 'Filtre actual per afegir productes.',
    },
]);

const setVenta = value => {
    isVenta.value = value;
};

const selectCategory = category => {
    selectedCategory.value = category;
};

const addItemToTiket = product => {
    const existingItem = tiketItems.find(item => item.product_id === product.id);

    if (existingItem) {
        existingItem.quantity += 1;
        existingItem.total = (existingItem.quantity * existingItem.unit_price).toFixed(2);
    } else {
        tiketItems.push({
            id: product.id,
            product_id: product.id,
            name: product.name,
            quantity: 1,
            unit_price: product.price,
            total: product.price,
        });
    }

    codebar.value = '';
    name.value = '';
};

const updateItemTotal = item => {
    item.total = (item.quantity * item.unit_price).toFixed(2);
};

const removeItem = id => {
    const index = tiketItems.findIndex(item => item.id === id);
    if (index !== -1) {
        tiketItems.splice(index, 1);
    }
};

const resolveProductName = productId => {
    const product = props.products.find(prod => prod.id === productId);
    return product ? product.name : 'Producte';
};

const clearFilters = () => {
    codebar.value = '';
    name.value = '';
};

const openCobrarModal = () => {
    if (!tiketItems.length) return;
    isCobrarModalOpen.value = true;
};

const closeCobrarModal = () => {
    isCobrarModalOpen.value = false;
};

const calculateChange = () => {
    const recibido = parseFloat(receivedAmount.value);
    const totalPagar = parseFloat(total.value);
    change.value = (recibido - totalPagar).toFixed(2);
};

const confirmCobro = () => {
    closeCobrarModal();
    tiketData = {
        name: 'Ticket-' + new Date().toLocaleString(),
        base_imponible: tiketData.base_imponible,
        iva: iva.value,
        monto_iva: tiketData.monto_iva,
        total: tiketData.total,
        tiketItems: tiketItems,
    };

    Inertia.post(route('tikets.store'), tiketData);
    isImprimirModalOpen.value = true;
};

const eliminarTodo = () => {
    tiketItems.splice(0, tiketItems.length);
};

const imprimir = () => {
    Inertia.get(route('tickets.printNoID', tiketData));
    closeImprimirModal();
};

const closeImprimirModal = () => {
    tiketData = {
        name: '',
        base_imponible: 0,
        iva: 0,
        monto_iva: 0,
        total: 0,
        tiketItems: [],
    };
    tiketItems.splice(0, tiketItems.length);
    isImprimirModalOpen.value = false;
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
