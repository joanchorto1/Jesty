<template>
    <AdminLAyout>
    <div class="p-6 bg-gray-100 min-h-screen">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-blue-600">Admin Dashboard</h1>

            <!-- Botones para crear compañías y usuarios -->
            <div class="space-x-4">
                <NavLink :href="route('companies.create')"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg"
                >
                    Crear Compañía
                </NavLink>
                <NavLink :href="route('users.adminCreate')"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg"
                >
                    Crear Usuario
                </NavLink>
            </div>
        </div>

        <!-- Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Total Companies Widget -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold">Total Companies</h2>
                <p class="text-4xl font-bold text-blue-500">{{ companiesLength }}</p>
            </div>

            <!-- Total Users Widget -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold">Total Users</h2>
                <p class="text-4xl font-bold text-blue-500">{{ userLength }}</p>
            </div>

            <!-- Active Users Widget -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold">Active Users</h2>
                <p class="text-4xl font-bold text-green-500">{{ activeUsersCount }}</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <!-- Companies per Month Bar Chart -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4">Companies Registered per Month</h2>
                <canvas id="companiesBarChart"></canvas>
            </div>

            <!-- Users Activity Pie Chart -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4">User Activity Breakdown</h2>
                <canvas id="userPieChart"></canvas>
            </div>
        </div>
    </div>
    </AdminLAyout>
</template>

<script setup>
import { defineProps, computed, onMounted } from 'vue'
import Chart from 'chart.js/auto'
import AdminLAyout from "@/Layouts/AdminLAyout.vue";
import {Inertia} from "@inertiajs/inertia";
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    companies: Array,
    users: Array,
})

const userLength = props.users.length
const companiesLength = props.companies.length

// Computed property for active users count
const activeUsersCount = computed(() => props.users.filter(user => user.isActive).length)

// Function to handle company creation


// Function to handle user creation
const createUser = () => {
return Inertia.get(route('user.createAdmin'))}

onMounted(() => {
    // Bar Chart for Companies per Month
    const companiesCtx = document.getElementById('companiesBarChart').getContext('2d')
    const companiesData = props.companies.reduce((acc, company) => {
        const month = new Date(company.registrationDate).toLocaleString('default', { month: 'short' })
        acc[month] = (acc[month] || 0) + 1
        return acc
    }, {})

    new Chart(companiesCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(companiesData),
            datasets: [{
                label: 'Companies',
                data: Object.values(companiesData),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    })

    // Pie Chart for Users Activity
    const userActivityData = props.users.reduce((acc, user) => {
        acc[user.status] = (acc[user.status] || 0) + 1
        return acc
    }, {})

    const userCtx = document.getElementById('userPieChart').getContext('2d')
    new Chart(userCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(userActivityData),
            datasets: [{
                label: 'Users',
                data: Object.values(userActivityData),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                ],
            }]
        },
        options: {
            responsive: true,
        },
    })
})
</script>

<style scoped>
/* Custom styles for widgets and charts */
</style>
