<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Reports</h1>
            <p class="mt-1 text-gray-500">View system reports and analytics</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Total Revenue</p>
                <p class="text-2xl font-bold text-gray-900">${{ stats.revenue }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Total Sales</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.sales }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">New Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.new_users }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">New Courses</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.new_courses }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Sales Over Time</h2>
            <div class="h-64 flex items-center justify-center text-gray-500">
                Chart placeholder
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const stats = ref({
    revenue: 0,
    sales: 0,
    new_users: 0,
    new_courses: 0,
});
</script>