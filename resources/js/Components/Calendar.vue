<template>
    <div class="calendar">
        <div class="calendar-header flex justify-between items-center mb-4">
            <button @click="prevMonth" class="bg-blue-500 text-white p-2 rounded">Anterior</button>
            <h2 class="text-lg font-bold text-gray-700">{{ formattedCurrentMonth }}</h2>
            <button @click="nextMonth" class="bg-blue-500 text-white p-2 rounded">Siguiente</button>
        </div>

        <div class="grid grid-cols-7 gap-2 text-center">
            <div v-for="day in weekDays" :key="day" class="font-semibold text-gray-700">{{ day }}</div>

            <div v-for="(day, index) in daysInMonth" :key="index" class="p-2 border rounded-lg text-center">
                <span
                    :class="{
                        'bg-blue-500 text-white rounded-full p-2': isToday(day),
                        'cursor-pointer hover:bg-blue-100': !isToday(day),
                    }"
                    @click="selectDay(day)"
                >
                    {{ day.getDate() }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { format, addMonths, subMonths, startOfMonth, endOfMonth, startOfWeek, endOfWeek, eachDayOfInterval, isSameDay, isToday as checkIsToday } from 'date-fns';

const today = new Date();
const currentMonth = ref(new Date());

const weekDays = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

const formattedCurrentMonth = computed(() => format(currentMonth.value, 'MMMM yyyy'));

const daysInMonth = computed(() => {
    const start = startOfWeek(startOfMonth(currentMonth.value), { weekStartsOn: 0 });
    const end = endOfWeek(endOfMonth(currentMonth.value), { weekStartsOn: 0 });
    return eachDayOfInterval({ start, end });
});

const isToday = (day) => checkIsToday(day);

const selectDay = (day) => {
    alert(`Seleccionaste el día: ${format(day, 'dd MMMM yyyy')}`);
};

const nextMonth = () => {
    currentMonth.value = addMonths(currentMonth.value, 1);
};

const prevMonth = () => {
    currentMonth.value = subMonths(currentMonth.value, 1);
};
</script>

<style scoped>
.calendar-header h2 {
    text-transform: capitalize;
}
.calendar div {
    transition: background-color 0.2s;
}
</style>
