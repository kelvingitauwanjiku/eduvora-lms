<template>
    <div>
        <div v-if="loading" class="animate-pulse">
            <div class="bg-gray-200 h-64"></div>
        </div>

        <template v-else-if="instructor">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white">
                            <img :src="instructor.avatar || '/images/avatar-placeholder.jpg'" :alt="instructor.name"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="text-center md:text-left">
                            <h1 class="text-3xl font-bold text-white">{{ instructor.name }}</h1>
                            <p class="text-blue-100 mt-1">{{ instructor.title || 'Instructor' }}</p>
                            <p v-if="instructor.bio" class="text-blue-50 mt-3 max-w-2xl">{{ instructor.bio }}</p>
                            <div class="flex flex-wrap justify-center md:justify-start gap-6 mt-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-white">{{ instructor.total_courses || 0 }}</div>
                                    <div class="text-blue-200 text-sm">Courses</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-white">{{ instructor.total_students || 0 }}</div>
                                    <div class="text-blue-200 text-sm">Students</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-white">{{ instructor.average_rating || '0.0' }}</div>
                                    <div class="text-blue-200 text-sm">Average Rating</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Courses by {{ instructor.name }}</h2>
                </div>

                <div v-if="coursesLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="i in 6" :key="i" class="animate-pulse">
                        <div class="bg-gray-200 h-48 rounded-xl"></div>
                        <div class="mt-4 h-5 bg-gray-200 rounded w-3/4"></div>
                    </div>
                </div>

                <div v-else-if="courses.length === 0" class="text-center py-12">
                    <p class="text-gray-500">No courses available yet.</p>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <router-link v-for="course in courses" :key="course.id" :to="`/courses/${course.id}`" class="group">
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition duration-300">
                            <div class="aspect-video relative overflow-hidden">
                                <img :src="course.thumbnail || '/images/course-placeholder.jpg'" :alt="course.title"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </div>
                            <div class="p-5">
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition line-clamp-2">
                                    {{ course.title }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">{{ course.instructor?.name }}</p>
                                <div class="flex items-center gap-2 mt-3">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-sm font-medium">{{ course.rating || '0.0' }}</span>
                                    </div>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-sm text-gray-500">{{ course.total_enrolled || 0 }} students</span>
                                </div>
                                <div class="mt-3">
                                    <span v-if="course.is_free" class="text-xl font-bold text-green-600">Free</span>
                                    <span v-else class="text-xl font-bold text-gray-900">${{ course.price || '0' }}</span>
                                </div>
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
        </template>

        <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h2 class="text-2xl font-bold text-gray-900">Instructor Not Found</h2>
            <router-link to="/instructors" class="text-blue-600 hover:underline mt-2 inline-block">
                Back to Instructors
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { instructorApi, courseApi } from '@/services/api';

const route = useRoute();

const loading = ref(true);
const instructor = ref(null);
const coursesLoading = ref(true);
const courses = ref([]);

async function fetchInstructor() {
    loading.value = true;
    try {
        const { data } = await instructorApi.getById(route.params.id);
        instructor.value = data;
    } catch (error) {
        console.error('Error fetching instructor:', error);
        instructor.value = null;
    } finally {
        loading.value = false;
    }
}

async function fetchCourses() {
    coursesLoading.value = true;
    try {
        const { data } = await instructorApi.getCourses(route.params.id, { per_page: 12 });
        courses.value = data.data || [];
    } catch (error) {
        console.error('Error fetching courses:', error);
    } finally {
        coursesLoading.value = false;
    }
}

onMounted(() => {
    fetchInstructor();
    fetchCourses();
});
</script>