<template>
    <div>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Courses</h1>
                <p class="mt-1 text-gray-500">Manage your courses</p>
            </div>
            <router-link to="/instructor/courses/create" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Course
            </router-link>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="p-4 border-b border-gray-200 flex flex-wrap items-center gap-4">
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input v-model="searchQuery" type="text" placeholder="Search courses..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <select v-model="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="pending">Pending</option>
                </select>
                <select v-model="sortBy" class="px-4 py-2 border border-gray-200 rounded-lg">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="title">Title A-Z</option>
                    <option value="students">Most Students</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                </select>
            </div>
        </div>

        <div v-if="loading" class="space-y-4">
            <div v-for="i in 5" :key="i" class="animate-pulse bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex gap-6">
                    <div class="w-48 h-28 bg-gray-200 rounded-lg"></div>
                    <div class="flex-1">
                        <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="filteredCourses.length === 0" class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No courses found</h3>
            <p class="text-gray-500 mt-1">{{ searchQuery ? 'Try adjusting your search' : 'Create your first course to start teaching' }}</p>
            <router-link v-if="!searchQuery" to="/instructor/courses/create" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Create Course
            </router-link>
        </div>

        <div v-else class="space-y-4">
            <div v-for="course in filteredCourses" :key="course.id" class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex gap-6">
                    <router-link :to="`/courses/${course.id}`" class="shrink-0">
                        <img :src="course.thumbnail || '/images/course-placeholder.jpg'" class="w-48 h-28 rounded-lg object-cover hover:opacity-80 transition">
                    </router-link>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div class="min-w-0">
                                <router-link :to="`/courses/${course.id}`" class="font-semibold text-gray-900 hover:text-blue-600 text-lg">
                                    {{ course.title }}
                                </router-link>
                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ course.short_description || course.description || 'No description' }}</p>
                                <div class="flex items-center gap-6 mt-3 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        {{ course.rating || '0.0' }} ({{ course.total_reviews || 0 }})
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ course.total_enrolled || 0 }} students
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 1v1m0-1H9m3 1h.01" />
                                        </svg>
                                        ${{ course.price || 0 }}
                                    </span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-green-100 text-green-800': course.status === 'published',
                                            'bg-yellow-100 text-yellow-800': course.status === 'draft',
                                            'bg-blue-100 text-blue-800': course.status === 'pending'
                                        }">
                                        {{ course.status || 'draft' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <button @click="toggleMenu(course.id)" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                                <div v-if="activeMenu === course.id" class="absolute bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-10 min-w-[160px]">
                                    <router-link :to="`/instructor/courses/${course.id}/edit`" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Edit Course
                                    </router-link>
                                    <router-link :to="`/instructor/curriculum/${course.id}`" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Curriculum
                                    </router-link>
                                    <router-link :to="`/instructor/students?course=${course.id}`" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Students
                                    </router-link>
                                    <router-link :to="`/courses/${course.id}`" target="_blank" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Preview
                                    </router-link>
                                    <hr class="my-2 border-gray-100">
                                    <button @click="duplicateCourse(course)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Duplicate
                                    </button>
                                    <button @click="deleteCourse(course)" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="totalPages > 1" class="mt-6 flex justify-center">
            <nav class="flex items-center gap-2">
                <button @click="currentPage--" :disabled="currentPage === 1" 
                    class="px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <span class="px-4 py-2 text-sm text-gray-600">
                    Page {{ currentPage }} of {{ totalPages }}
                </span>
                <button @click="currentPage++" :disabled="currentPage === totalPages" 
                    class="px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </nav>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { courseApi } from '@/services/api';

const router = useRouter();

const loading = ref(true);
const courses = ref([]);
const searchQuery = ref('');
const statusFilter = ref('');
const sortBy = ref('newest');
const currentPage = ref(1);
const perPage = 10;
const activeMenu = ref(null);

const filteredCourses = computed(() => {
    let result = [...courses.value];
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(c => 
            c.title?.toLowerCase().includes(query) ||
            c.short_description?.toLowerCase().includes(query)
        );
    }
    
    if (statusFilter.value) {
        result = result.filter(c => c.status === statusFilter.value);
    }
    
    switch (sortBy.value) {
        case 'oldest':
            result.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            break;
        case 'title':
            result.sort((a, b) => a.title.localeCompare(b.title));
            break;
        case 'students':
            result.sort((a, b) => (b.total_enrolled || 0) - (a.total_enrolled || 0));
            break;
        case 'price_low':
            result.sort((a, b) => (a.price || 0) - (b.price || 0));
            break;
        case 'price_high':
            result.sort((a, b) => (b.price || 0) - (a.price || 0));
            break;
        default:
            result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }
    
    return result;
});

const totalPages = computed(() => Math.ceil(filteredCourses.value.length / perPage));

async function fetchCourses() {
    loading.value = true;
    try {
        const { data } = await courseApi.getAll({ per_page: 100 });
        courses.value = data.data || [];
    } catch (error) {
        console.error('Error fetching courses:', error);
    } finally {
        loading.value = false;
    }
}

function toggleMenu(courseId) {
    activeMenu.value = activeMenu.value === courseId ? null : courseId;
}

async function duplicateCourse(course) {
    activeMenu.value = null;
    try {
        const formData = new FormData();
        formData.append('title', `${course.title} (Copy)`);
        formData.append('short_description', course.short_description);
        formData.append('description', course.description);
        formData.append('category_id', course.category_id);
        formData.append('price', course.price);
        formData.append('level', course.level);
        formData.append('status', 'draft');
        
        const { data } = await courseApi.create(formData);
        router.push(`/instructor/courses/${data.id}/edit`);
    } catch (error) {
        console.error('Error duplicating course:', error);
    }
}

async function deleteCourse(course) {
    activeMenu.value = null;
    if (confirm(`Are you sure you want to delete "${course.title}"? This action cannot be undone.`)) {
        try {
            await courseApi.delete(course.id);
            courses.value = courses.value.filter(c => c.id !== course.id);
        } catch (error) {
            console.error('Error deleting course:', error);
        }
    }
}

function handleClickOutside(event) {
    if (!event.target.closest('.relative')) {
        activeMenu.value = null;
    }
}

onMounted(() => {
    fetchCourses();
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
