<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Purchase History</h1>
            <p class="mt-1 text-gray-500">View your past course purchases</p>
        </div>

        <div v-if="loading" class="space-y-4">
            <div v-for="i in 5" :key="i" class="animate-pulse bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex gap-6">
                    <div class="w-32 h-20 bg-gray-200 rounded-lg"></div>
                    <div class="flex-1">
                        <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="purchases.length === 0" class="text-center py-16">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No purchases yet</h3>
            <p class="text-gray-500 mt-1">Your course purchases will appear here</p>
            <router-link to="/courses" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Browse Courses
            </router-link>
        </div>

        <div v-else class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="purchase in purchases" :key="purchase.id">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <img :src="purchase.course?.thumbnail || '/images/course-placeholder.jpg'" 
                                    class="w-20 h-12 rounded-lg object-cover">
                                <div>
                                    <router-link :to="`/courses/${purchase.course?.id}`" class="font-medium text-gray-900 hover:text-blue-600">
                                        {{ purchase.course?.title }}
                                    </router-link>
                                    <p class="text-sm text-gray-500">{{ purchase.course?.instructor?.name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ formatDate(purchase.created_at) }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            ${{ purchase.amount || '0' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full"
                                :class="purchase.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                {{ purchase.status || 'completed' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-3">
                                <button @click="downloadInvoice(purchase.id)" class="text-sm text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Invoice
                                </button>
                                <router-link v-if="purchase.course?.id" :to="`/student/courses/${purchase.course.id}/learn`" class="text-sm text-green-600 hover:text-green-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    </svg>
                                    Learn
                                </router-link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { paymentApi } from '@/services/api';

const loading = ref(true);
const purchases = ref([]);

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

async function fetchPurchases() {
    loading.value = true;
    try {
        const { data } = await paymentApi.getAll();
        purchases.value = data.data || [];
    } catch (error) {
        console.error('Error fetching purchases:', error);
    } finally {
        loading.value = false;
    }
}

async function downloadInvoice(id) {
    try {
        const response = await paymentApi.getReceipt(id);
        if (response.data.url) {
            window.open(response.data.url, '_blank');
        } else if (response.data.download_url) {
            window.open(response.data.download_url, '_blank');
        } else {
            alert('Invoice download not available');
        }
    } catch (error) {
        console.error('Error downloading invoice:', error);
    }
}

onMounted(() => {
    fetchPurchases();
});
</script>