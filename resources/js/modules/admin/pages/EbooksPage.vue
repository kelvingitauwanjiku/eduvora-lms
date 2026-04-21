<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Ebooks</h1>
                <p class="mt-1 text-gray-500">Manage digital books</p>
            </div>
            <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Add Ebook</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="ebook in ebooks" :key="ebook.id" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="aspect-[3/4] bg-gray-100 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900">{{ ebook.title }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ ebook.author }}</p>
                    <div class="flex items-center justify-between mt-3">
                        <span class="font-bold">${{ ebook.price }}</span>
                        <button class="text-sm text-blue-600">Edit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
const ebooks = ref([]);
</script>
