<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Team Training</h1>
                <p class="mt-1 text-gray-500">Corporate training management</p>
            </div>
            <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">New Request</button>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seats</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="item in items" :key="item.id">
                        <td class="px-6 py-4">{{ item.company }}</td>
                        <td class="px-6 py-4">{{ item.course }}</td>
                        <td class="px-6 py-4">{{ item.seats }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full" :class="item.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">{{ item.status }}</span>
                        </td>
                        <td class="px-6 py-4 font-medium">${{ item.amount }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { teamTrainingApi } from '@/services/api';

const loading = ref(true);
const items = ref([]);

async function fetchItems() {
    try {
        loading.value = true;
        const { data } = await teamTrainingApi.getAll();
        items.value = data.data || [];
    } catch (error) {
        console.error('Error fetching team training:', error);
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    fetchItems();
});
</script>
