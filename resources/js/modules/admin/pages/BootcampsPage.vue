<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Bootcamps</h1>
                <p class="mt-1 text-gray-500">Manage intensive bootcamp programs</p>
            </div>
            <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create Bootcamp</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="bootcamp in bootcamps" :key="bootcamp.id" class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg">
                <div class="aspect-video relative">
                    <img :src="bootcamp.thumbnail || '/images/course-placeholder.jpg'" class="w-full h-full object-cover">
                    <div class="absolute top-3 right-3">
                        <span class="px-3 py-1 bg-black/70 text-white text-xs rounded-full">{{ bootcamp.duration }}</span>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-gray-900">{{ bootcamp.title }}</h3>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ bootcamp.description }}</p>
                    <div class="flex items-center justify-between mt-4">
                        <span class="font-bold text-gray-900">${{ bootcamp.price }}</span>
                        <span class="text-sm text-gray-500">{{ bootcamp.enrolled }} enrolled</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
const bootcamps = ref([]);
onMounted(() => {});
</script>
