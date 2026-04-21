<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Knowledge Base</h1>
                <p class="mt-1 text-gray-500">Help articles and documentation</p>
            </div>
            <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Add Article</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div v-for="category in categories" :key="category.id" class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">{{ category.name }}</h3>
                <ul class="space-y-2">
                    <li v-for="article in category.articles" :key="article.id">
                        <a href="#" class="text-sm text-gray-600 hover:text-blue-600">{{ article.title }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
const categories = ref([
    { id: 1, name: 'Getting Started', articles: [{id:1,title:'How to enroll'},{id:2,title:'Account setup'}] },
    { id: 2, name: 'Courses', articles: [{id:3,title:'Taking quizzes'},{id:4,title:'Getting certificates'}] },
    { id: 3, name: 'Billing', articles: [{id:5,title:'Payment methods'},{id:6,title:'Refunds'}] },
]);
</script>
