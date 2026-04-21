<template>
    <div>
        <HeroBanner 
            title="Blog & Articles"
            subtitle="Latest news, insights, and learning tips from our expert community"
            search-placeholder="Search articles..."
            :show-stats="false"
        />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Category Filter -->
            <div class="flex gap-2 mb-8 overflow-x-auto pb-2">
                <button @click="selectedCategory = ''"
                    :class="selectedCategory === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    class="px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap">
                    All Articles
                </button>
                <button v-for="cat in categories" :key="cat" @click="selectedCategory = cat"
                    :class="selectedCategory === cat ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    class="px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap">
                    {{ cat }}
                </button>
            </div>

            <!-- Featured Post -->
            <div v-if="featuredPost && !selectedCategory" class="mb-12">
                <router-link :to="`/blog/${featuredPost.id}`" class="group block">
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-xl transition">
                        <div class="grid grid-cols-1 lg:grid-cols-2">
                            <div class="aspect-video lg:aspect-auto">
                                <img :src="featuredPost.image || '/images/blog-placeholder.jpg'" :alt="featuredPost.title"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            </div>
                            <div class="p-8 flex flex-col justify-center">
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-4 w-fit">Featured</span>
                                <h2 class="text-2xl font-bold text-gray-900 mb-4 group-hover:text-blue-600 transition">{{ featuredPost.title }}</h2>
                                <p class="text-gray-600 mb-4 line-clamp-3">{{ featuredPost.excerpt || featuredPost.description }}</p>
                                <div class="flex items-center gap-4">
                                    <img :src="featuredPost.user?.avatar || '/images/avatar-placeholder.jpg'" :alt="featuredPost.user?.name"
                                        class="w-10 h-10 rounded-full object-cover">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ featuredPost.user?.name }}</p>
                                        <p class="text-sm text-gray-500">{{ formatDate(featuredPost.created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </router-link>
            </div>

            <!-- Blog Grid -->
            <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="i in 6" :key="i" class="animate-pulse">
                    <div class="bg-gray-200 h-48 rounded-xl"></div>
                    <div class="mt-4 h-5 bg-gray-200 rounded w-3/4"></div>
                    <div class="mt-2 h-4 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>

            <div v-else-if="filteredPosts.length === 0" class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3h.01M9 21h.01M9 9h.01M9 9h.01M9 9h.01" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">No articles found</h3>
                <p class="text-gray-500 mt-1">Check back later for new content</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <router-link v-for="post in filteredPosts" :key="post.id" :to="`/blog/${post.id}`" class="group">
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition duration-300 h-full flex flex-col">
                        <div class="aspect-video relative overflow-hidden flex-shrink-0">
                            <img :src="post.image || '/images/blog-placeholder.jpg'" :alt="post.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <span v-if="post.is_featured" class="absolute top-3 left-3 px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-full">Featured</span>
                        </div>
                        <div class="p-5 flex flex-col flex-1">
                            <div class="flex items-center gap-3 text-sm text-gray-500 mb-3">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ formatDate(post.created_at) }}
                                </span>
                                <span v-if="post.category" class="px-2 py-0.5 bg-gray-100 rounded-full">{{ post.category }}</span>
                            </div>
                            <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition line-clamp-2 flex-1">{{ post.title }}</h3>
                            <p class="text-sm text-gray-500 mt-2 line-clamp-3">{{ post.excerpt || post.description }}</p>
                            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                                <img :src="post.user?.avatar || '/images/avatar-placeholder.jpg'" :alt="post.user?.name"
                                    class="w-8 h-8 rounded-full object-cover">
                                <span class="text-sm text-gray-600">{{ post.user?.name }}</span>
                            </div>
                        </div>
                    </div>
                </router-link>
            </div>

            <!-- Pagination -->
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
import { ref, computed, onMounted, watch } from 'vue';
import { blogApi } from '@/services/api';
import HeroBanner from '../../../components/public/HeroBanner.vue';

const loading = ref(true);
const posts = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const selectedCategory = ref('');
const searchQuery = ref('');

const categories = ['Technology', 'Business', 'Design', 'Marketing', 'Development'];

const featuredPost = computed(() => posts.value.find(p => p.is_featured));

const filteredPosts = computed(() => {
    let result = posts.value.filter(p => !p.is_featured);
    if (selectedCategory.value) {
        result = result.filter(p => p.category === selectedCategory.value);
    }
    return result;
});

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

async function fetchBlogs() {
    loading.value = true;
    try {
        const params = { page: currentPage.value, per_page: 9 };
        const { data } = await blogApi.getAll(params);
        posts.value = data.data || [];
        currentPage.value = data.current_page || 1;
        totalPages.value = data.last_page || 1;
    } catch (error) {
        console.error('Error fetching blogs:', error);
    } finally {
        loading.value = false;
    }
}

function changePage(page) {
    currentPage.value = page;
    fetchBlogs();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

watch(selectedCategory, () => {
    currentPage.value = 1;
});

onMounted(() => {
    fetchBlogs();
});
</script>