<template>
    <div class="flex items-center justify-center h-full">
        <div class="text-center">
            <div v-if="loading" class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <div v-else>
                <div v-if="lesson.type === 'video'" class="aspect-video w-full max-w-4xl bg-black rounded-lg overflow-hidden">
                    <video v-if="lesson.video_url" :src="lesson.video_url" controls class="w-full h-full"></video>
                    <div v-else class="w-full h-full flex items-center justify-center text-white">Video Player</div>
                </div>
                <div v-else-if="lesson.type === 'text'" class="prose max-w-4xl mx-auto bg-white p-8 rounded-lg">
                    <div v-html="lesson.content"></div>
                </div>
                <div v-else-if="lesson.type === 'quiz'" class="max-w-2xl mx-auto bg-white p-8 rounded-lg">
                    <h2 class="text-2xl font-bold mb-6">Quiz</h2>
                    <div v-for="(question, qIndex) in quiz.questions" :key="qIndex" class="mb-6">
                        <p class="font-medium mb-3">{{ qIndex + 1 }}. {{ question.question }}</p>
                        <div v-for="(option, oIndex) in question.options" :key="oIndex" class="mb-2">
                            <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" :name="`q${qIndex}`" class="w-4 h-4">
                                <span>{{ option }}</span>
                            </label>
                        </div>
                    </div>
                    <button class="mt-4 px-6 py-3 bg-blue-600 text-white rounded-lg">Submit Quiz</button>
                </div>
                <div v-else class="text-gray-500">Unsupported lesson type</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
const loading = ref(false);
const lesson = ref({ type: 'video', video_url: '', content: '' });
const quiz = reactive({ questions: [] });
</script>
