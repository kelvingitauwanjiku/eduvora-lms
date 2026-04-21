<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Course Bundles</h1>
                <p class="mt-1 text-gray-500">Manage course bundles and subscriptions</p>
            </div>
            <button @click="showCreateModal = true" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Create Bundle
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="bundle in bundles" :key="bundle.id" class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition">
                <div class="aspect-video relative overflow-hidden">
                    <img :src="bundle.thumbnail || '/images/course-placeholder.jpg'" class="w-full h-full object-cover">
                    <div class="absolute top-3 right-3">
                        <span class="px-3 py-1 bg-black/70 text-white text-xs font-medium rounded-full">
                            {{ bundle.courses_count || 0 }} courses
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-gray-900">{{ bundle.title }}</h3>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ bundle.description }}</p>
                    <div class="flex items-center justify-between mt-4">
                        <div>
                            <span v-if="bundle.is_free" class="text-xl font-bold text-green-600">Free</span>
                            <span v-else class="text-xl font-bold text-gray-900">${{ bundle.price }}</span>
                            <span v-if="bundle.is_subscription" class="text-xs text-gray-500">/month</span>
                        </div>
                        <div class="flex gap-2">
                            <button class="text-sm text-blue-600 hover:text-blue-700">Edit</button>
                            <button class="text-sm text-red-600 hover:text-red-700">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Bundle Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl w-full max-w-2xl">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-xl font-bold">Create Course Bundle</h2>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bundle Title</label>
                        <input v-model="form.title" type="text" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea v-model="form.description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                            <input v-model="form.price" type="number" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 mt-6">
                                <input v-model="form.is_subscription" type="checkbox" class="w-4 h-4">
                                <span class="text-sm text-gray-700">Subscription</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Courses</label>
                        <div class="border border-gray-200 rounded-lg max-h-48 overflow-auto">
                            <label v-for="course in availableCourses" :key="course.id" class="flex items-center gap-2 p-3 hover:bg-gray-50">
                                <input type="checkbox" :value="course.id" v-model="form.course_ids" class="w-4 h-4">
                                <span>{{ course.title }}</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                    <button @click="showCreateModal = false" class="px-4 py-2 border border-gray-200 rounded-lg">Cancel</button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Create</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';

const showCreateModal = ref(false);
const bundles = ref([]);
const availableCourses = ref([]);
const form = reactive({ title: '', description: '', price: 0, is_subscription: false, course_ids: [] });

onMounted(() => {});
</script>