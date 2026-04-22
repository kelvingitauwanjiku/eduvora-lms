<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Instructors</h1>
                <p class="mt-1 text-gray-500">Manage instructor applications and approvals</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <input v-model="search" type="text" placeholder="Search instructors..." 
                    class="w-full max-w-md px-4 py-2 border border-gray-200 rounded-lg">
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instructor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Courses</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="instructor in instructors" :key="instructor.id">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img :src="instructor.avatar || '/images/avatar-placeholder.jpg'" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="font-medium text-gray-900">{{ instructor.name }}</p>
                                    <p class="text-sm text-gray-500">{{ instructor.email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full"
                                :class="instructor.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                {{ instructor.status || 'pending' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ instructor.total_courses || 0 }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ instructor.total_revenue || 0 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <router-link :to="`/admin/instructors/${instructor.id}`" class="text-sm text-blue-600 hover:text-blue-700">View</router-link>
                                <button v-if="instructor.status === 'pending'" @click="approveInstructor(instructor.id)" class="text-sm text-green-600 hover:text-green-700">Approve</button>
                                <button class="text-sm text-red-600 hover:text-red-700">Reject</button>
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
import { instructorApi } from '@/services/api';

const search = ref('');
const instructors = ref([]);
const loading = ref(false);

async function fetchInstructors() {
    loading.value = true;
    try {
        const { data } = await instructorApi.getAll({ search: search.value });
        instructors.value = data.data || data;
    } catch (err) {
        console.error('Failed to load instructors:', err);
    } finally {
        loading.value = false;
    }
}

async function updateStatus(instructor, status) {
    try {
        await instructorApi.updateProfile({ id: instructor.id, status });
        instructor.status = status;
    } catch (err) {
        alert(err.response?.data?.message || 'Failed to update');
    }
}

onMounted(() => { fetchInstructors(); });
</script>