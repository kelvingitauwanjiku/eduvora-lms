<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Payouts</h1>
            <p class="mt-1 text-gray-500">Manage instructor payouts</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Pending Payouts</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instructor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="payout in pendingPayouts" :key="payout.id">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img :src="payout.user?.avatar || '/images/avatar-placeholder.jpg'" class="w-10 h-10 rounded-full">
                                <span class="font-medium text-gray-900">{{ payout.user?.name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">${{ payout.amount }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ payout.method }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(payout.created_at) }}</td>
                        <td class="px-6 py-4">
                            <button @click="approvePayout(payout.id)" class="text-sm text-green-600 hover:text-green-700 mr-3">Approve</button>
                            <button @click="rejectPayout(payout.id)" class="text-sm text-red-600 hover:text-red-700">Reject</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Payout History</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instructor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="payout in payoutHistory" :key="payout.id">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ payout.user?.name }}</td>
                        <td class="px-6 py-4">${{ payout.amount }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full"
                                :class="payout.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                {{ payout.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(payout.created_at) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { payoutApi } from '@/services/api';

const loading = ref(true);
const pendingPayouts = ref([]);
const payoutHistory = ref([]);

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

async function fetchPayouts() {
    try {
        loading.value = true;
        const { data } = await payoutApi.getAllAdmin();
        const allPayouts = data.data || [];
        pendingPayouts.value = allPayouts.filter(p => p.status === 'pending');
        payoutHistory.value = allPayouts.filter(p => p.status !== 'pending');
    } catch (error) {
        console.error('Error fetching payouts:', error);
    } finally {
        loading.value = false;
    }
}

async function approvePayout(id) {
    try {
        await payoutApi.approve(id);
        await fetchPayouts();
    } catch (error) {
        console.error('Error approving payout:', error);
        alert('Failed to approve payout');
    }
}

async function rejectPayout(id) {
    try {
        await payoutApi.reject(id);
        await fetchPayouts();
    } catch (error) {
        console.error('Error rejecting payout:', error);
        alert('Failed to reject payout');
    }
}

onMounted(() => {
    fetchPayouts();
});
</script>