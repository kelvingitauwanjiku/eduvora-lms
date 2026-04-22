<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Enrollments</h1>
            <p class="mt-1 text-gray-500">Manage course enrollments</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="enrollment in enrollments" :key="enrollment.id">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img :src="enrollment.user?.avatar || '/images/avatar-placeholder.jpg'" class="w-10 h-10 rounded-full">
                                <span class="font-medium text-gray-900">{{ enrollment.user?.name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-900">{{ enrollment.course?.title }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">${{ enrollment.amount }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(enrollment.created_at) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                {{ enrollment.status }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { enrollmentApi } from '@/services/api';

const enrollments = ref([]);
const loading = ref(false);

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
}

async function fetchEnrollments() {
    loading.value = true;
    try {
        const { data } = await enrollmentApi.getAll();
        enrollments.value = data.data || data;
    } catch (err) {
        console.error('Failed to load enrollments:', err);
    } finally {
        loading.value = false;
    }
}

onMounted(() => { fetchEnrollments(); });
</script>