<template>
    <div>
        <HeroBanner 
            title="Course Bundles"
            subtitle="Save big with bundled courses. Get multiple premium courses at a fraction of the individual cost."
            search-placeholder="Search bundles..."
            :show-stats="false"
        />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Bundle Types -->
            <div class="flex gap-4 mb-8 overflow-x-auto pb-2">
                <button 
                    v-for="type in bundleTypes" 
                    :key="type.value"
                    @click="selectedType = type.value"
                    :class="selectedType === type.value ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                    class="px-6 py-2 rounded-full font-medium transition whitespace-nowrap"
                >
                    {{ type.label }}
                </button>
            </div>

            <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="i in 6" :key="i" class="animate-pulse">
                    <div class="bg-gray-200 h-80 rounded-xl"></div>
                </div>
            </div>

            <div v-else-if="bundles.length === 0" class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6M7 7h10m0 0h6m-6 0v6m0-6h10" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">No bundles found</h3>
                <p class="text-gray-500 mt-1">Check back later for new bundles</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <router-link v-for="bundle in bundles" :key="bundle.id" :to="`/bundles/${bundle.id}`" class="group">
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition h-full flex flex-col">
                        <div class="aspect-video relative overflow-hidden">
                            <img :src="bundle.thumbnail || '/images/course-placeholder.jpg'" 
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute top-3 right-3 flex gap-2">
                                <span v-if="bundle.is_featured" class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-full">Featured</span>
                                <span v-if="bundle.is_subscription" class="px-3 py-1 bg-purple-600 text-white text-xs font-medium rounded-full">Subscription</span>
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ bundle.title }}</h3>
                            <p class="text-sm text-gray-500 mt-1 flex-1 line-clamp-2">{{ bundle.description }}</p>
                            
                            <div class="flex items-center gap-4 mt-4 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    {{ bundle.courses_count || 0 }} courses
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354l3.12 3.333-3.12.833V4.354l3.12.833V2.688L6 4.354l6 1.833zm0 15.292V14.52l-3.12-.833v3.957L6 16.687l3.12-1.833 3.12.833V17.52l3.12.833v1.833L6 18.521l-3.12 1.833V20.52l3.12-.833v1.833L12 20.146l-3.12-.833v-3.957L3 17.52l6-1.833z" transform="scale(0.5) translate(24,24)" />
                                    </svg>
                                    {{ bundle.students || 0 }} students
                                </span>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                                <div>
                                    <span v-if="bundle.is_subscription" class="text-xl font-bold text-gray-900">${{ bundle.price }}<span class="text-sm text-gray-500">/mo</span></span>
                                    <span v-else class="text-xl font-bold text-gray-900">${{ bundle.price }}</span>
                                </div>
                                <span class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg group-hover:bg-blue-700 transition">
                                    View Bundle
                                </span>
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
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { bundleApi } from '@/services/api';
import HeroBanner from '../../../components/public/HeroBanner.vue';

const route = useRoute();

const loading = ref(true);
const bundles = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const selectedType = ref('');

const bundleTypes = [
    { value: '', label: 'All Bundles' },
    { value: 'one-time', label: 'One-Time Purchase' },
    { value: 'subscription', label: 'Subscriptions' },
];

async function fetchBundles() {
    loading.value = true;
    try {
        const params = { page: currentPage.value, per_page: 9 };
        if (selectedType.value) params.type = selectedType.value;
        
        const { data } = await bundleApi.getAll(params);
        bundles.value = data.data || [];
        currentPage.value = data.current_page || 1;
        totalPages.value = data.last_page || 1;
    } catch (error) {
        console.error('Error fetching bundles:', error);
    } finally {
        loading.value = false;
    }
}

function changePage(page) {
    currentPage.value = page;
    fetchBundles();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

watch(selectedType, () => {
    currentPage.value = 1;
    fetchBundles();
});

onMounted(() => {
    fetchBundles();
});
</script>
