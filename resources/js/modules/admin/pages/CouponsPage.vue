<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Coupons</h1>
                <p class="mt-1 text-gray-500">Manage discount coupons</p>
            </div>
            <button @click="showCreateModal = true" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Create Coupon
            </button>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Discount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="coupon in coupons" :key="coupon.id">
                        <td class="px-6 py-4 font-mono font-medium text-gray-900">{{ coupon.code }}</td>
                        <td class="px-6 py-4 text-gray-900">{{ coupon.discount }}{{ coupon.type === 'percentage' ? '%' : '$' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ coupon.type }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(coupon.expires_at) }}</td>
                        <td class="px-6 py-4">
                            <button class="text-sm text-blue-600 hover:text-blue-700 mr-3">Edit</button>
                            <button class="text-sm text-red-600 hover:text-red-700">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { couponApi } from '../../../services/api';

const showCreateModal = ref(false);
const coupons = ref([]);
const loading = ref(false);
const error = ref('');

const newCoupon = ref({
    code: '',
    discount: '',
    type: 'percentage',
    expires_at: ''
});

async function fetchCoupons() {
    loading.value = true;
    try {
        const { data } = await couponApi.getAll();
        coupons.value = data.data || data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load coupons';
    } finally {
        loading.value = false;
    }
}

async function createCoupon() {
    try {
        await couponApi.create(newCoupon.value);
        showCreateModal.value = false;
        fetchCoupons();
    } catch (err) {
        alert(err.response?.data?.message || 'Failed to create coupon');
    }
}

async function deleteCoupon(id) {
    if (confirm('Delete this coupon?')) {
        try {
            await couponApi.delete(id);
            coupons.value = coupons.value.filter(c => c.id !== id);
        } catch (err) {
            alert(err.response?.data?.message || 'Failed to delete coupon');
        }
    }
}

function formatDate(date) {
    if (!date) return 'Never';
    return new Date(date).toLocaleDateString();
}

onMounted(() => {
    fetchCoupons();
});
</script>