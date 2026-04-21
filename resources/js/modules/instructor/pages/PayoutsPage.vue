<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Payouts</h1>
            <p class="mt-1 text-gray-500">Manage your earnings and withdrawals</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-green-100 text-sm">Available Balance</p>
                        <p class="text-3xl font-bold">${{ formatNumber(stats.available) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 1v1m0-1H9m3 1h.01" />
                        </svg>
                    </div>
                </div>
                <button @click="requestPayout" :disabled="stats.available <= 0" 
                    class="w-full mt-2 px-4 py-2 bg-white text-green-600 rounded-lg font-medium hover:bg-green-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Request Payout
                </button>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600">${{ formatNumber(stats.pending) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Processing payout</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Earned</p>
                        <p class="text-2xl font-bold text-gray-900">${{ formatNumber(stats.total) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">All time earnings</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Payout History</h2>
            <div v-if="loading" class="space-y-4">
                <div v-for="i in 5" :key="i" class="animate-pulse flex items-center justify-between py-4 border-b border-gray-100">
                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                    <div class="h-4 bg-gray-200 rounded w-16"></div>
                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                </div>
            </div>
            <div v-else-if="payouts.length === 0" class="text-center py-8 text-gray-500">
                No payout history yet
            </div>
            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="payout in payouts" :key="payout.id">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(payout.created_at) }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ payout.amount }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ payout.method }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full"
                                :class="{
                                    'bg-green-100 text-green-800': payout.status === 'completed',
                                    'bg-yellow-100 text-yellow-800': payout.status === 'pending',
                                    'bg-red-100 text-red-800': payout.status === 'failed',
                                }">
                                {{ payout.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ payout.reference || '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Payout Methods</h2>
                <div class="space-y-4">
                    <div v-for="method in payoutMethods" :key="method.id" 
                        class="flex items-center justify-between p-4 border border-gray-200 rounded-lg"
                        :class="{ 'border-blue-500 bg-blue-50': method.is_default }">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg v-if="method.type === 'bank'" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <svg v-else class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ method.type }}</p>
                                <p class="text-sm text-gray-500">{{ method.details }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span v-if="method.is_default" class="text-xs text-blue-600 font-medium">Default</span>
                            <button @click="editPayoutMethod(method)" class="text-sm text-blue-600 hover:text-blue-700">Edit</button>
                        </div>
                    </div>
                </div>
                <button @click="showAddMethodModal = true" class="mt-4 w-full px-4 py-2 border border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-gray-400 hover:text-gray-600">
                    + Add Payout Method
                </button>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Payout Schedule</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">Minimum Payout</p>
                            <p class="text-sm text-gray-500">Minimum amount to request payout</p>
                        </div>
                        <span class="font-bold text-gray-900">$50</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">Processing Time</p>
                            <p class="text-sm text-gray-500">Time to process payout</p>
                        </div>
                        <span class="font-bold text-gray-900">3-5 days</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">Currency</p>
                            <p class="text-sm text-gray-500">Payout currency</p>
                        </div>
                        <span class="font-bold text-gray-900">USD</span>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showPayoutModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="showPayoutModal = false">
            <div class="bg-white rounded-xl w-full max-w-md">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Request Payout</h3>
                    <button @click="showPayoutModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form @submit.prevent="submitPayoutRequest" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <input v-model="payoutForm.amount" type="number" step="0.01" :max="stats.available" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        <p class="mt-1 text-sm text-gray-500">Available: ${{ stats.available }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payout Method</label>
                        <select v-model="payoutForm.method_id" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="">Select method</option>
                            <option v-for="method in payoutMethods" :key="method.id" :value="method.id">{{ method.type }}</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showPayoutModal = false" 
                            class="px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" :disabled="requesting"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                            {{ requesting ? 'Processing...' : 'Request Payout' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { payoutApi } from '@/services/api';

const loading = ref(true);
const stats = ref({ available: 0, pending: 0, total: 0 });
const payouts = ref([]);
const payoutMethods = ref([]);
const showPayoutModal = ref(false);
const showAddMethodModal = ref(false);
const requesting = ref(false);

const payoutForm = ref({
    amount: 0,
    method_id: '',
});

function formatNumber(num) {
    return (num || 0).toFixed(2);
}

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function requestPayout() {
    payoutForm.value.amount = stats.value.available;
    showPayoutModal.value = true;
}

async function submitPayoutRequest() {
    requesting.value = true;
    try {
        await payoutApi.request(payoutForm.value);
        showPayoutModal.value = false;
        fetchStats();
        fetchPayouts();
    } catch (error) {
        console.error('Error requesting payout:', error);
    } finally {
        requesting.value = false;
    }
}

function editPayoutMethod(method) {
    console.log('Edit method:', method);
}

async function fetchStats() {
    try {
        const { data } = await payoutApi.getStats();
        stats.value = data;
    } catch (error) {
        console.error('Error fetching stats:', error);
    }
}

async function fetchPayouts() {
    loading.value = true;
    try {
        const { data } = await payoutApi.getAll({ per_page: 50 });
        payouts.value = data.data || [];
    } catch (error) {
        console.error('Error fetching payouts:', error);
    } finally {
        loading.value = false;
    }
}

async function fetchPayoutMethods() {
    try {
        const { data } = await payoutApi.getMethods();
        payoutMethods.value = data.data || [];
    } catch (error) {
        console.error('Error fetching payout methods:', error);
    }
}

onMounted(() => {
    fetchStats();
    fetchPayouts();
    fetchPayoutMethods();
});
</script>