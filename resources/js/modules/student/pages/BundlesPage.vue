<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">My Bundles</h1>
        <p class="text-gray-500 mt-1">Access your subscribed course bundles</p>
      </div>
    </div>

    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="i in 3" :key="i" class="bg-white rounded-2xl border border-gray-200 p-6 animate-pulse">
        <div class="h-40 bg-gray-200 rounded-xl mb-4"></div>
        <div class="h-6 bg-gray-200 rounded mb-2"></div>
        <div class="h-4 bg-gray-200 rounded w-2/3"></div>
      </div>
    </div>

    <div v-else-if="bundles.length === 0" class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
      <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2m2 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-2M9 9a2 2 0 100-4 2 2 0 000 4z" />
        </svg>
      </div>
      <h3 class="text-lg font-semibold text-gray-900 mb-2">No Bundles Yet</h3>
      <p class="text-gray-500 mb-4">Subscribe to course bundles to access multiple courses</p>
      <router-link to="/bundles" class="inline-flex px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700">
        Browse Bundles
      </router-link>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="bundle in bundles" :key="bundle.id" class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all">
        <div class="relative">
          <img :src="bundle.thumbnail" class="w-full h-40 object-cover" />
          <div class="absolute top-3 right-3 px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-full">
            {{ bundle.courses_count }} Courses
          </div>
        </div>
        <div class="p-5">
          <h3 class="font-semibold text-gray-900 mb-2">{{ bundle.title }}</h3>
          <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ bundle.description }}</p>
          
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">Valid until {{ bundle.expires_at }}</span>
            <router-link :to="`/student/bundles/${bundle.slug}`" class="text-blue-600 font-medium text-sm hover:underline">
              View Courses →
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { bundleApi } from '../../../services/api';

const loading = ref(true);
const bundles = ref([]);

onMounted(async () => {
  try {
    const { data } = await bundleApi.getEnrolled();
    bundles.value = data;
  } catch (error) {
    console.error('Error loading bundles:', error);
  } finally {
    loading.value = false;
  }
});
</script>