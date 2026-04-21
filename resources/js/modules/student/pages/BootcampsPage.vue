<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">My Bootcamps</h1>
        <p class="text-gray-500 mt-1">Your enrolled bootcamp programs</p>
      </div>
    </div>

    <div v-if="loading" class="space-y-4">
      <div v-for="i in 2" :key="i" class="bg-white rounded-2xl border border-gray-200 p-6 animate-pulse">
        <div class="flex gap-6">
          <div class="w-48 h-32 bg-gray-200 rounded-xl"></div>
          <div class="flex-1 space-y-3">
            <div class="h-6 bg-gray-200 rounded w-1/2"></div>
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            <div class="h-2 bg-gray-200 rounded w-full"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="bootcamps.length === 0" class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
      <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
      </div>
      <h3 class="text-lg font-semibold text-gray-900 mb-2">No Bootcamps Enrolled</h3>
      <p class="text-gray-500 mb-4">Join our intensive bootcamp programs</p>
      <router-link to="/bootcamps" class="inline-flex px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700">
        Browse Bootcamps
      </router-link>
    </div>

    <div v-else class="space-y-4">
      <div v-for="bootcamp in bootcamps" :key="bootcamp.id" class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-lg transition-all">
        <div class="flex flex-col md:flex-row gap-6">
          <img :src="bootcamp.thumbnail" class="w-full md:w-48 h-32 object-cover rounded-xl" />
          <div class="flex-1">
            <div class="flex items-start justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ bootcamp.title }}</h3>
                <p class="text-gray-500 text-sm mt-1">{{ bootcamp.instructor }}</p>
              </div>
              <span :class="[
                'px-3 py-1 rounded-full text-sm font-medium',
                bootcamp.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'
              ]">
                {{ bootcamp.status }}
              </span>
            </div>
            
            <p class="text-gray-600 mt-2 line-clamp-2">{{ bootcamp.description }}</p>
            
            <div class="flex items-center gap-6 mt-4 text-sm text-gray-500">
              <span>{{ bootcamp.modules_count }} Modules</span>
              <span>{{ bootcamp.live_classes_count }} Live Classes</span>
              <span>Progress: {{ bootcamp.progress }}%</span>
            </div>
            
            <div class="mt-4">
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full" :style="{ width: `${bootcamp.progress}%` }"></div>
              </div>
            </div>
            
            <div class="flex gap-3 mt-4">
              <router-link :to="`/student/bootcamps/${bootcamp.slug}/learn`" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                Continue Learning
              </router-link>
              <router-link :to="`/student/bootcamps/${bootcamp.slug}/resources`" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">
                Resources
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { bootcampApi } from '../../../services/api';

const loading = ref(true);
const bootcamps = ref([]);

onMounted(async () => {
  try {
    const { data } = await bootcampApi.getMyEnrollments();
    bootcamps.value = data;
  } catch (error) {
    console.error('Error:', error);
  } finally {
    loading.value = false;
  }
});
</script>