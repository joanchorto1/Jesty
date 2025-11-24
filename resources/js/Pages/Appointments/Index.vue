<template>
    <AppLayout title="Agenda">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">Agenda</h2>
        </template>

        <div class="space-y-6">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-blue-500/20">
                <h3 class="text-lg font-semibold text-white">Nova cita</h3>
                <p class="text-sm text-blue-100/70">Programa una reunió amb data, hora i responsable assignat.</p>

                <form class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2" @submit.prevent="submit">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-blue-100" for="scheduled_date">Data</label>
                        <input
                            id="scheduled_date"
                            v-model="form.scheduled_date"
                            type="date"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-blue-100/60 focus:border-blue-400 focus:outline-none"
                        />
                        <p v-if="form.errors.scheduled_date" class="text-sm text-red-400">{{ form.errors.scheduled_date }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-blue-100" for="scheduled_time">Hora</label>
                        <input
                            id="scheduled_time"
                            v-model="form.scheduled_time"
                            type="time"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-blue-100/60 focus:border-blue-400 focus:outline-none"
                            step="3600"
                        />
                        <p v-if="form.errors.scheduled_time" class="text-sm text-red-400">{{ form.errors.scheduled_time }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-blue-100" for="user_id">Responsable</label>
                        <select
                            id="user_id"
                            v-model="form.user_id"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-blue-400 focus:outline-none"
                        >
                            <option disabled value="">Selecciona un usuari</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                        <p v-if="form.errors.user_id" class="text-sm text-red-400">{{ form.errors.user_id }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-blue-100" for="guest_name">Amb qui ens reunim?</label>
                        <input
                            id="guest_name"
                            v-model="form.guest_name"
                            type="text"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-blue-100/60 focus:border-blue-400 focus:outline-none"
                            placeholder="Nom de la persona o empresa"
                        />
                        <p v-if="form.errors.guest_name" class="text-sm text-red-400">{{ form.errors.guest_name }}</p>
                    </div>

                    <div class="space-y-2 lg:col-span-2">
                        <label class="text-sm font-medium text-blue-100" for="subject">Tema</label>
                        <input
                            id="subject"
                            v-model="form.subject"
                            type="text"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-blue-100/60 focus:border-blue-400 focus:outline-none"
                            placeholder="Assumpte principal de la reunió"
                        />
                        <p v-if="form.errors.subject" class="text-sm text-red-400">{{ form.errors.subject }}</p>
                    </div>

                    <div class="space-y-2 lg:col-span-2">
                        <label class="text-sm font-medium text-blue-100" for="notes">Notes</label>
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-blue-100/60 focus:border-blue-400 focus:outline-none"
                            rows="3"
                            placeholder="Detalls addicionals de la cita"
                        ></textarea>
                        <p v-if="form.errors.notes" class="text-sm text-red-400">{{ form.errors.notes }}</p>
                    </div>

                    <div class="flex flex-col gap-3 lg:col-span-2">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500"
                            :disabled="form.processing"
                        >
                            Guardar cita
                        </button>

                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-sm font-semibold text-white">Disponibilitat per a {{ form.scheduled_date || 'selecciona una data' }}</p>
                            <p v-if="loadingAvailability" class="text-sm text-blue-100/80">Carregant franges...</p>
                            <div v-else class="mt-2 grid grid-cols-2 gap-4 lg:grid-cols-4">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-blue-200/70">Lliures</p>
                                    <div class="mt-1 flex flex-wrap gap-2 text-sm text-emerald-200">
                                        <span v-if="availability.available.length === 0" class="text-blue-100/70">Sense franges lliures</span>
                                        <span
                                            v-for="slot in availability.available"
                                            :key="`free-${slot}`"
                                            class="rounded-lg bg-emerald-600/30 px-2 py-1"
                                        >
                                            {{ slot }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-blue-200/70">Ocupades</p>
                                    <div class="mt-1 flex flex-wrap gap-2 text-sm text-amber-200">
                                        <span v-if="availability.busy.length === 0" class="text-blue-100/70">Sense franges ocupades</span>
                                        <span
                                            v-for="slot in availability.busy"
                                            :key="`busy-${slot}`"
                                            class="rounded-lg bg-amber-600/30 px-2 py-1"
                                        >
                                            {{ slot }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-blue-500/20">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Cites programades</h3>
                    <p class="text-sm text-blue-100/70">{{ appointments.length }} cites</p>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead>
                            <tr class="text-left text-sm text-blue-100/70">
                                <th class="px-4 py-3">Data</th>
                                <th class="px-4 py-3">Hora</th>
                                <th class="px-4 py-3">Responsable</th>
                                <th class="px-4 py-3">Amb qui</th>
                                <th class="px-4 py-3">Tema</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="appointment in appointments" :key="appointment.id" class="text-sm text-blue-50/90">
                                <td class="px-4 py-3">{{ formatDate(appointment.scheduled_date) }}</td>
                                <td class="px-4 py-3">{{ appointment.scheduled_time.substring(0,5) }}</td>
                                <td class="px-4 py-3">{{ appointment.user?.name }}</td>
                                <td class="px-4 py-3">{{ appointment.guest_name }}</td>
                                <td class="px-4 py-3">{{ appointment.subject }}</td>
                            </tr>
                            <tr v-if="appointments.length === 0">
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-blue-100/70">Encara no hi ha cap cita programada.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    appointments: Array,
    users: Array,
});

const form = useForm({
    scheduled_date: '',
    scheduled_time: '',
    user_id: '',
    guest_name: '',
    subject: '',
    notes: '',
});

const availability = ref({ available: [], busy: [] });
const loadingAvailability = ref(false);

const fetchAvailability = async () => {
    if (!form.scheduled_date) {
        availability.value = { available: [], busy: [] };
        return;
    }

    loadingAvailability.value = true;
    try {
        const { data } = await axios.get('/api/appointments/availability', {
            params: { date: form.scheduled_date },
        });
        availability.value = data;
    } catch (error) {
        availability.value = { available: [], busy: [] };
    } finally {
        loadingAvailability.value = false;
    }
};

watch(() => form.scheduled_date, fetchAvailability);

const submit = () => {
    form.post(route('appointments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('scheduled_time', 'guest_name', 'subject', 'notes');
            fetchAvailability();
        },
    });
};

const formatDate = (date) => new Date(date).toLocaleDateString('ca-ES');
</script>
