<template>
    <header class="h-16 bg-gray-900 flex items-center justify-between px-4">
        <div class="flex items-center gap-4">
            <router-link :to="`/student/courses/${courseId}`" class="text-white hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </router-link>
            <h1 class="text-white font-medium">{{ courseTitle }}</h1>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-gray-400 text-sm">{{ currentLesson }}/{{ totalLessons }}</span>
        </div>
    </header>
</template>

<script setup>
defineProps({
    courseId: String,
    courseTitle: String,
    currentLesson: Number,
    totalLessons: Number,
});
</script>