<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Courses</h1>
            <p class="mt-1 text-gray-500">Manage all courses</p>
        </div>

        <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-600">
            {{ error }}
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex gap-4">
                <input v-model="search" @input="fetchCourses" type="text" placeholder="Search courses..." 
                    class="w-full max-w-md px-4 py-2 border border-gray-200 rounded-lg">
                <button @click="fetchCourses" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Search
                </button>
            </div>
            
            <div v-if="loading" class="p-8 text-center">
                <div class="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full mx-auto"></div>
                <p class="mt-2 text-gray-500">Loading courses...</p>
            </div>
            
            <table v-else class="min-w-full divide-y divide-gray-200">
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
                        <td class="px-6 py-4 text-sm text-gray-500">{{ course.instructor?.name || course.user?.name }}</td>
                        <td class="px-6 py-4">
                            <select :value="course.status" @change="updateStatus(course, $event.target.value)"
                                class="px-2 py-1 text-xs font-medium rounded-full border-0 cursor-pointer"
                                :class="course.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </td>
                        <td class="px-6 py-4">
                            <button @click="deleteCourse(course.id)" class="text-sm text-red-600 hover:text-red-700">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div v-if="!loading && courses.length === 0" class="p-8 text-center text-gray-500">
                No courses found.
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { courseAdminApi } from '../../../services/api';

const search = ref('');
const courses = ref([]);
const loading = ref(false);
const error = ref('');

async function fetchCourses() {
    loading.value = true;
    error.value = '';
    try {
        const { data } = await courseAdminApi.getAll({ search: search.value });
        courses.value = data.data || data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load courses';
    } finally {
        loading.value = false;
    }
}

async function deleteCourse(id) {
    if (confirm('Are you sure you want to delete this course?')) {
        try {
            await courseAdminApi.delete(id);
            courses.value = courses.value.filter(c => c.id !== id);
        } catch (err) {
            alert(err.response?.data?.message || 'Failed to delete course');
        }
    }
}

async function updateStatus(course, status) {
    try {
        await courseAdminApi.update(course.id, { status });
        course.status = status;
    } catch (err) {
        alert(err.response?.data?.message || 'Failed to update status');
    }
}

onMounted(() => {
    fetchCourses();
});
</script>