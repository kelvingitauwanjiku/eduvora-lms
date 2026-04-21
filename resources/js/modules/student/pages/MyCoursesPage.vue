<template>
    <div>
        <div class="bg-white border-b border-gray-200 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900">My Courses</h1>
                <p class="mt-1 text-gray-500">Courses you've enrolled in</p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="i in 6" :key="i" class="animate-pulse">
                    <div class="bg-gray-200 h-48 rounded-xl"></div>
                </div>
            </div>
            <div v-else-if="courses.length === 0" class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">No courses yet</h3>
                <p class="text-gray-500 mt-1">Start learning by enrolling in a course</p>
                <router-link to="/courses" class="inline-block mt-4 px-6 py-3 bg-blue-600 text-white font-medium rounded-xl">Browse Courses</router-link>
            </div>
            <div v-else class="space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex gap-2">
                        <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-medium">All</button>
                        <button @click="filter = 'in_progress'" :class="filter === 'in_progress' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-medium">In Progress</button>
                        <button @click="filter = 'completed'" :class="filter === 'completed' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-medium">Completed</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="course in filteredCourses" :key="course.id" class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition">
                        <router-link :to="`/student/courses/${course.id}/learn`">
                            <div class="aspect-video relative overflow-hidden">
                                <img :src="course.thumbnail || '/images/course-placeholder.jpg'" class="w-full h-full object-cover">
                                <div v-if="course.progress === 100" class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">Completed</div>
                            </div>
                        </router-link>
                        <div class="p-5">
                            <router-link :to="`/student/courses/${course.id}/learn`">
                                <h3 class="font-semibold text-gray-900 hover:text-blue-600 transition line-clamp-2">{{ course.title }}</h3>
                            </router-link>
                            <p class="text-sm text-gray-500 mt-1">{{ course.instructor?.name }}</p>
                            <div class="mt-3 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-600 rounded-full" :style="{ width: (course.progress || 0) + '%' }"></div>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <p class="text-xs text-gray-500">{{ course.progress || 0 }}% complete</p>
                                <span class="text-xs text-gray-500">{{ course.completed_lessons || 0 }}/{{ course.total_lessons || 0 }} lessons</span>
                            </div>
                            <router-link :to="`/student/courses/${course.id}/learn`" class="mt-4 block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                {{ course.progress === 0 ? 'Start Learning' : 'Continue Learning' }}
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { enrollmentApi } from '@/services/api';

const loading = ref(true);
const courses = ref([]);
const filter = ref('all');

const filteredCourses = computed(() => {
    if (filter.value === 'all') return courses.value;
    if (filter.value === 'in_progress') return courses.value.filter(c => c.progress > 0 && c.progress < 100);
    if (filter.value === 'completed') return courses.value.filter(c => c.progress === 100);
    return courses.value;
});

async function fetchCourses() {
    try {
        loading.value = true;
        const { data } = await enrollmentApi.getAll();
        courses.value = data.data || [];
    } catch (error) {
        console.error('Error fetching courses:', error);
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    fetchCourses();
});
</script>