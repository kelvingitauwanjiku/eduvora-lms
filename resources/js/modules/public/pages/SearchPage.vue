<template>
    <div>
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 py-12">
            <div class="max-w-4xl mx-auto px-4">
                <div class="relative">
                    <input v-model="query" @input="debouncedSearch" type="text" 
                        placeholder="Search courses, instructors, categories..." 
                        class="w-full px-6 py-4 pl-14 text-lg border-0 rounded-xl focus:ring-2 focus:ring-white shadow-lg">
                    <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <div class="flex gap-2 mt-4">
                    <button v-for="filter in filters" :key="filter.value" @click="selectedFilter = filter.value"
                        :class="selectedFilter === filter.value ? 'bg-white text-blue-600' : 'bg-white/20 text-white hover:bg-white/30'"
                        class="px-4 py-2 rounded-full text-sm font-medium transition">
                        {{ filter.label }}
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 py-8">
            <!-- Results Count -->
            <div v-if="query" class="mb-6">
                <p class="text-gray-500">
                    <span v-if="loading">Searching...</span>
                    <span v-else>
                        Found {{ totalResults }} results for "{{ query }}"
                    </span>
                </p>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="space-y-4">
                <div v-for="i in 6" :key="i" class="animate-pulse flex gap-4 p-4 bg-white rounded-xl">
                    <div class="w-32 h-20 bg-gray-200 rounded-lg"></div>
                    <div class="flex-1">
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
            </div>

            <!-- Search Suggestions -->
            <div v-else-if="!query" class="py-12">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Popular Searches</h2>
                <div class="flex flex-wrap gap-2">
                    <button v-for="term in popularSearches" :key="term" @click="query = term; search()"
                        class="px-4 py-2 bg-gray-100 text-gray-600 rounded-full hover:bg-gray-200 transition">
                        {{ term }}
                    </button>
                </div>
            </div>

            <!-- Results -->
            <div v-else-if="totalResults > 0">
                <!-- Courses Section -->
                <div v-if="results.courses?.length" class="mb-8">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Courses ({{ results.courses.length }})
                    </h2>
                    <div class="space-y-4">
                        <router-link v-for="course in results.courses" :key="course.id" :to="`/courses/${course.id}`" 
                            class="flex gap-4 p-4 bg-white rounded-xl border hover:shadow-lg hover:border-blue-100 transition group">
                            <div class="w-32 h-20 rounded-lg overflow-hidden shrink-0">
                                <img :src="course.thumbnail || '/images/course-placeholder.jpg'" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition truncate">{{ course.title }}</h3>
                                <p class="text-sm text-gray-500">{{ course.instructor?.name }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        {{ course.rating || '0.0' }}
                                    </span>
                                    <span>{{ course.total_enrolled || 0 }} students</span>
                                    <span v-if="course.is_free" class="text-green-600 font-medium">Free</span>
                                    <span v-else class="font-medium">${{ course.price }}</span>
                                </div>
                            </div>
                        </router-link>
                    </div>
                </div>

                <!-- Instructors Section -->
                <div v-if="results.instructors?.length" class="mb-8">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Instructors ({{ results.instructors.length }})
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <router-link v-for="inst in results.instructors" :key="inst.id" :to="`/instructors/${inst.id}`" 
                            class="flex gap-4 p-4 bg-white rounded-xl border hover:shadow-lg hover:border-blue-100 transition group">
                            <img :src="inst.avatar || '/images/avatar-placeholder.jpg'" class="w-16 h-16 rounded-full object-cover">
                            <div>
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ inst.name }}</h3>
                                <p class="text-sm text-gray-500">{{ inst.title || 'Instructor' }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                    <span>{{ inst.total_courses || 0 }} courses</span>
                                    <span>{{ inst.total_students || 0 }} students</span>
                                </div>
                            </div>
                        </router-link>
                    </div>
                </div>

                <!-- Categories Section -->
                <div v-if="results.categories?.length" class="mb-8">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Categories ({{ results.categories.length }})
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <router-link v-for="cat in results.categories" :key="cat.id" :to="`/courses?category=${cat.slug}`" 
                            class="p-4 bg-white rounded-xl border hover:shadow-lg hover:border-blue-100 transition group">
                            <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ cat.name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ cat.courses_count || 0 }} courses</p>
                        </router-link>
                    </div>
                </div>

                <!-- Bundles Section -->
                <div v-if="results.bundles?.length" class="mb-8">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Bundles ({{ results.bundles.length }})
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <router-link v-for="bundle in results.bundles" :key="bundle.id" :to="`/bundles/${bundle.id}`" 
                            class="flex gap-4 p-4 bg-white rounded-xl border hover:shadow-lg hover:border-blue-100 transition group">
                            <div class="w-24 h-16 rounded-lg overflow-hidden shrink-0">
                                <img :src="bundle.thumbnail || '/images/course-placeholder.jpg'" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ bundle.title }}</h3>
                                <p class="text-sm text-gray-500">{{ bundle.courses_count || 0 }} courses</p>
                                <span class="text-lg font-bold">${{ bundle.price }}</span>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>

            <!-- No Results -->
            <div v-else-if="query && !loading" class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">No results found</h3>
                <p class="text-gray-500 mt-1">Try different keywords or browse our categories</p>
                <router-link to="/courses" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Browse Courses
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { searchApi } from '@/services/api';

const route = useRoute();
const router = useRouter();

const query = ref(route.query.q || '');
const loading = ref(false);
const selectedFilter = ref('all');
const results = reactive({ courses: [], instructors: [], categories: [], bundles: [] });

const filters = [
    { value: 'all', label: 'All' },
    { value: 'courses', label: 'Courses' },
    { value: 'instructors', label: 'Instructors' },
    { value: 'bundles', label: 'Bundles' },
];

const popularSearches = [
    'Web Development',
    'Data Science',
    'Machine Learning',
    'React',
    'Python',
    'JavaScript',
];

const totalResults = computed(() => {
    return (results.courses?.length || 0) + 
           (results.instructors?.length || 0) + 
           (results.categories?.length || 0) + 
           (results.bundles?.length || 0);
});

let debounceTimer = null;

function debouncedSearch() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        search();
    }, 300);
}

async function search() {
    if (!query.value.trim()) {
        results.courses = [];
        results.instructors = [];
        results.categories = [];
        results.bundles = [];
        return;
    }

    loading.value = true;
    try {
        const params = { q: query.value };
        if (selectedFilter.value !== 'all') {
            params.type = selectedFilter.value;
        }
        
        const { data } = await searchApi.global(params);
        results.courses = data.courses || [];
        results.instructors = data.instructors || [];
        results.categories = data.categories || [];
        results.bundles = data.bundles || [];
    } catch (error) {
        console.error('Search error:', error);
    } finally {
        loading.value = false;
    }
}

watch(selectedFilter, () => {
    if (query.value) search();
});

watch(() => route.query.q, (newQ) => {
    query.value = newQ || '';
    if (query.value) search();
});

if (query.value) search();
</script>