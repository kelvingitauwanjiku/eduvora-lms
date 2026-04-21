<template>
    <div>
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-white">Our Instructors</h1>
                <p class="mt-2 text-blue-100">Learn from industry experts</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="mb-8">
                <div class="relative max-w-md">
                    <input v-model="filters.search" @input="applyFilters" type="text" placeholder="Search instructors..." 
                        class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="i in 8" :key="i" class="animate-pulse">
                    <div class="bg-gray-200 h-72 rounded-xl"></div>
                    <div class="mt-4 h-5 bg-gray-200 rounded w-3/4"></div>
                    <div class="mt-2 h-4 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>

            <div v-else-if="instructors.length === 0" class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">No instructors found</h3>
                <p class="text-gray-500 mt-1">Try adjusting your search</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <router-link v-for="instructor in instructors" :key="instructor.id" :to="`/instructors/${instructor.id}`" 
                    class="group bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition duration-300">
                    <div class="p-6 text-center">
                        <div class="w-24 h-24 mx-auto mb-4 rounded-full overflow-hidden">
                            <img :src="instructor.avatar || '/images/avatar-placeholder.jpg'" :alt="instructor.name"
                                class="w-full h-full object-cover">
                        </div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ instructor.name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ instructor.title || 'Instructor' }}</p>
                        <div class="flex items-center justify-center gap-4 mt-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                {{ instructor.total_courses || 0 }} Courses
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354l3.12 3.333-3.12.833V4.354l3.12.833V2.688L6 4.354l6 1.833zm0 15.292V14.52l-3.12-.833v3.957L6 16.687l3.12-1.833 3.12.833V17.52l3.12.833v1.833L6 18.521l-3.12 1.833V20.52l3.12-.833v1.833L12 20.146l-3.12-.833v-3.957L3 17.52l6-1.833z" transform="scale(0.5) translate(24,24)" />
                                </svg>
                                {{ instructor.total_students || 0 }} Students
                            </span>
                        </div>
                    </div>
                </router-link>
            </div>

            <div v-if="totalPages > 1" class="mt-12 flex justify-center gap-2">
                <button v-for="page in totalPages" :key="page" @click="changePage(page)"
                    :class="page === currentPage ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    {{ page }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { instructorApi } from '@/services/api';

const router = useRouter();

const loading = ref(true);
const instructors = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

const filters = ref({
    search: '',
    page: 1,
});

async function fetchInstructors() {
    loading.value = true;
    try {
        const params = { page: filters.value.page, per_page: 12 };
        if (filters.value.search) params.search = filters.value.search;

        const { data } = await instructorApi.getAll(params);
        instructors.value = data.data || [];
        currentPage.value = data.current_page || 1;
        totalPages.value = data.last_page || 1;
    } catch (error) {
        console.error('Error fetching instructors:', error);
    } finally {
        loading.value = false;
    }
}

function applyFilters() {
    filters.value.page = 1;
    fetchInstructors();
}

function changePage(page) {
    filters.value.page = page;
    fetchInstructors();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

onMounted(() => {
    fetchInstructors();
});
</script>