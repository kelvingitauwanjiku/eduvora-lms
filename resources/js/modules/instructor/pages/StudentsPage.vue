<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Students</h1>
            <p class="mt-1 text-gray-500">View enrolled students</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <input v-model="search" type="text" placeholder="Search students..." 
                    class="w-full max-w-md px-4 py-2 border border-gray-200 rounded-lg">
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrolled</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="student in students" :key="student.id">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img :src="student.user?.avatar || '/images/avatar-placeholder.jpg'" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="font-medium text-gray-900">{{ student.user?.name }}</p>
                                    <p class="text-sm text-gray-500">{{ student.user?.email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ student.course?.title }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-gray-200 rounded-full">
                                    <div class="h-full bg-blue-600 rounded-full" :style="{ width: (student.progress || 0) + '%' }"></div>
                                </div>
                                <span class="text-sm text-gray-500">{{ student.progress || 0 }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ formatDate(student.created_at) }}
                        </td>
                        <td class="px-6 py-4">
                            <button class="text-sm text-blue-600 hover:text-blue-700">Message</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const search = ref('');
const students = ref([]);

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

async function fetchStudents() {
    students.value = [];
}

onMounted(() => {
    fetchStudents();
});
</script>