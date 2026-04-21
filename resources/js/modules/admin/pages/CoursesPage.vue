<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Courses</h1>
            <p class="mt-1 text-gray-500">Manage all courses</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <input v-model="search" type="text" placeholder="Search courses..." 
                    class="w-full max-w-md px-4 py-2 border border-gray-200 rounded-lg">
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instructor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="course in courses" :key="course.id">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img :src="course.thumbnail || '/images/course-placeholder.jpg'" class="w-12 h-8 rounded-lg object-cover">
                                <span class="font-medium text-gray-900">{{ course.title }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ course.instructor?.name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full"
                                :class="course.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                {{ course.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button @click="deleteCourse(course.id)" class="text-sm text-red-600 hover:text-red-700">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const search = ref('');
const courses = ref([]);

async function deleteCourse(id) {
    if (confirm('Delete this course?')) {
        console.log('Delete course:', id);
    }
}
</script>