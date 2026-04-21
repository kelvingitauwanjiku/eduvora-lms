<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Revenue</h1>
                <p class="mt-1 text-gray-500">Track your earnings and sales</p>
            </div>
            <button @click="exportReport" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export Report
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">This Month</p>
                <p class="text-2xl font-bold text-gray-900">${{ formatNumber(stats.this_month) }}</p>
                <p class="text-xs mt-1" :class="stats.this_month_change >= 0 ? 'text-green-600' : 'text-red-600'">
                    {{ stats.this_month_change >= 0 ? '+' : '' }}{{ stats.this_month_change || 0 }}% from last month
                </p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Last Month</p>
                <p class="text-2xl font-bold text-gray-900">${{ formatNumber(stats.last_month) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">This Year</p>
                <p class="text-2xl font-bold text-gray-900">${{ formatNumber(stats.this_year) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Total Revenue</p>
                <p class="text-2xl font-bold text-gray-900">${{ formatNumber(stats.total) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Earnings Chart</h2>
                    <select v-model="chartPeriod" @change="fetchChartData" class="text-sm border-gray-200 rounded-lg px-3 py-1">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>
                </div>
                <div class="h-80">
                    <LineChart v-if="chartData.labels?.length" :data="chartData" />
                    <div v-else class="flex items-center justify-center h-full text-gray-400">No data available</div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Top Courses</h2>
                <div class="space-y-4">
                    <div v-for="(course, index) in topCourses" :key="course.id" class="flex items-center gap-3">
                        <span class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center text-xs font-medium text-gray-600">
                            {{ index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ course.title }}</p>
                            <p class="text-sm text-gray-500">{{ course.sales }} sales</p>
                        </div>
                        <span class="font-semibold text-gray-900">${{ formatNumber(course.revenue) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Sales by Course</h2>
                <div class="flex gap-2">
                    <input v-model="searchQuery" type="text" placeholder="Search courses..." 
                        class="px-4 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
            </div>
            <div v-if="loading" class="space-y-4">
                <div v-for="i in 5" :key="i" class="animate-pulse flex items-center gap-4 py-4 border-b border-gray-100">
                    <div class="h-4 bg-gray-200 rounded w-48"></div>
                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                </div>
            </div>
            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sales</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">% of Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="course in filteredCourses" :key="course.id">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img :src="course.thumbnail || '/images/course-placeholder.jpg'" class="w-12 h-8 rounded object-cover">
                                <span class="font-medium text-gray-900">{{ course.title }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">${{ course.price }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ course.sales }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ formatNumber(course.revenue) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-2 bg-gray-200 rounded-full">
                                    <div class="h-full bg-blue-600 rounded-full" :style="{ width: (course.revenue / stats.total * 100) + '%' }"></div>
                                </div>
                                <span>{{ Math.round(course.revenue / stats.total * 100) }}%</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { analyticsApi } from '@/services/api';
import LineChart from '@/components/charts/LineChart.vue';

const loading = ref(true);
const stats = ref({ this_month: 0, last_month: 0, this_year: 0, total: 0 });
const courses = ref([]);
const topCourses = ref([]);
const chartPeriod = ref('30');
const searchQuery = ref('');

const chartData = ref({
    labels: [],
    datasets: [{
        label: 'Revenue',
        data: [],
        borderColor: '#3B82F6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        fill: true,
        tension: 0.4,
    }]
});

const filteredCourses = computed(() => {
    if (!searchQuery.value) return courses.value;
    const query = searchQuery.value.toLowerCase();
    return courses.value.filter(c => c.title?.toLowerCase().includes(query));
});

function formatNumber(num) {
    return (num || 0).toFixed(2);
}

async function fetchStats() {
    try {
        const { data } = await analyticsApi.getRevenue();
        stats.value = { ...stats.value, ...data.stats };
    } catch (error) {
        console.error('Error fetching stats:', error);
    }
}

async function fetchCourses() {
    loading.value = true;
    try {
        const { data } = await analyticsApi.getCourses({ per_page: 100 });
        courses.value = data.data || [];
        topCourses.value = [...courses.value]
            .sort((a, b) => b.revenue - a.revenue)
            .slice(0, 5);
    } catch (error) {
        console.error('Error fetching courses:', error);
    } finally {
        loading.value = false;
    }
}

async function fetchChartData() {
    try {
        const { data } = await analyticsApi.getRevenue({ days: chartPeriod.value });
        chartData.value.labels = data.labels || [];
        chartData.value.datasets[0].data = data.values || [];
    } catch (error) {
        console.error('Error fetching chart data:', error);
    }
}

function exportReport() {
    const csv = [
        ['Course', 'Price', 'Sales', 'Revenue'].join(','),
        ...courses.value.map(c => [
            c.title,
            c.price,
            c.sales,
            c.revenue,
        ].join(','))
    ].join('\n');
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `revenue-report-${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
}

onMounted(() => {
    fetchStats();
    fetchCourses();
    fetchChartData();
});
</script>