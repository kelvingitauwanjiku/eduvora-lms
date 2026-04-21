<template>
    <PlayerLayout>
        <div class="h-full flex flex-col">
            <!-- Video Player Area -->
            <div class="flex-1 bg-black flex items-center justify-center">
                <div v-if="loading" class="flex items-center justify-center h-full">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
                
                <div v-else-if="currentLesson" class="w-full h-full flex flex-col">
                    <div class="aspect-video bg-gray-900 relative flex-1">
                        <!-- Video Player -->
                        <div v-if="currentLesson.type === 'video' && currentLesson.video_url" class="w-full h-full">
                            <video 
                                ref="videoPlayer"
                                class="w-full h-full"
                                controls
                                :src="currentLesson.video_url"
                                @ended="handleVideoEnded"
                                @timeupdate="handleTimeUpdate"
                            ></video>
                        </div>
                        
                        <!-- Text/Article Content -->
                        <div v-else-if="currentLesson.type === 'text'" class="w-full h-full overflow-auto p-8 text-white">
                            <div class="max-w-4xl mx-auto prose prose-invert" v-html="currentLesson.content"></div>
                        </div>
                        
                        <!-- PDF Viewer -->
                        <div v-else-if="currentLesson.type === 'pdf'" class="w-full h-full flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-24 h-24 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-4 text-gray-400">PDF Document</p>
                                <a v-if="currentLesson.resource_url" :href="currentLesson.resource_url" target="_blank" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Download PDF
                                </a>
                            </div>
                        </div>
                        
                        <!-- Quiz Link -->
                        <div v-else-if="currentLesson.type === 'quiz'" class="w-full h-full flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-24 h-24 mx-auto text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <p class="mt-4 text-white text-xl font-semibold">{{ currentLesson.title }}</p>
                                <router-link :to="`/student/courses/${courseId}/quiz/${currentLesson.id}`" class="mt-6 inline-block px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                    Start Quiz
                                </router-link>
                            </div>
                        </div>
                        
                        <!-- Placeholder for other types -->
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <div class="text-center text-gray-400">
                                <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-4">Content not available</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lesson Info Bar -->
                    <div class="bg-gray-800 px-6 py-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-white font-semibold">{{ currentLesson.title }}</h2>
                            <p class="text-gray-400 text-sm">{{ currentSection?.title }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span v-if="progressPercentage > 0" class="text-gray-400 text-sm">
                                {{ Math.round(progressPercentage) }}% watched
                            </span>
                            <button 
                                v-if="!currentLesson.is_completed"
                                @click="markComplete"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            >
                                Mark Complete
                            </button>
                            <span v-else class="flex items-center gap-2 text-green-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Completed
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Navigation -->
            <div class="bg-gray-800 border-t border-gray-700 px-6 py-4 flex items-center justify-between">
                <button 
                    @click="prevLesson"
                    :disabled="!hasPrevLesson"
                    class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </button>
                
                <div class="flex items-center gap-4">
                    <router-link :to="`/student/courses/${courseId}`" class="text-gray-400 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </router-link>
                    <router-link :to="`/student/courses/${courseId}/notes`" class="text-gray-400 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </router-link>
                    <router-link :to="`/student/courses/${courseId}/resources`" class="text-gray-400 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </router-link>
                </div>
                
                <button 
                    @click="nextLesson"
                    :disabled="!hasNextLesson"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition"
                >
                    Next
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </PlayerLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import PlayerLayout from '@/layouts/PlayerLayout.vue';
import api from '@/services/api';

const route = useRoute();
const router = useRouter();

const courseId = computed(() => route.params.id);
const loading = ref(true);
const course = ref(null);
const currentLessonId = ref(null);
const progressPercentage = ref(0);
const videoPlayer = ref(null);

const allLessons = computed(() => {
    if (!course.value?.sections) return [];
    return course.value.sections.flatMap(section => 
        (section.lessons || []).map(lesson => ({ ...lesson, sectionTitle: section.title }))
    );
});

const currentLessonIndex = computed(() => {
    return allLessons.value.findIndex(l => l.id === currentLessonId.value);
});

const currentLesson = computed(() => {
    return allLessons.value.find(l => l.id === currentLessonId.value);
});

const currentSection = computed(() => {
    if (!currentLesson.value) return null;
    return course.value?.sections?.find(s => 
        s.lessons?.some(l => l.id === currentLessonId.value)
    );
});

const hasPrevLesson = computed(() => currentLessonIndex.value > 0);
const hasNextLesson = computed(() => currentLessonIndex.value < allLessons.value.length - 1);

async function fetchCourse() {
    loading.value = true;
    try {
        const { data } = await api.get(`/courses/${courseId.value}`);
        course.value = data;
        
        if (allLessons.value.length > 0) {
            const lastWatched = localStorage.getItem(`course_${courseId.value}_last_lesson`);
            currentLessonId.value = lastWatched ? parseInt(lastWatched) : allLessons.value[0].id;
        }
    } catch (error) {
        console.error('Error fetching course:', error);
    } finally {
        loading.value = false;
    }
}

function selectLesson(lessonId) {
    currentLessonId.value = lessonId;
    localStorage.setItem(`course_${courseId.value}_last_lesson`, lessonId);
    progressPercentage.value = 0;
}

async function markComplete() {
    try {
        await api.post(`/lessons/${currentLessonId.value}/complete`);
        const lesson = allLessons.value.find(l => l.id === currentLessonId.value);
        if (lesson) lesson.is_completed = true;
    } catch (error) {
        console.error('Error marking complete:', error);
    }
}

function handleVideoEnded() {
    if (!currentLesson.value?.is_completed) {
        markComplete();
    }
}

function handleTimeUpdate() {
    if (videoPlayer.value) {
        const percent = (videoPlayer.value.currentTime / videoPlayer.value.duration) * 100;
        progressPercentage.value = percent;
        
        if (percent >= 90 && !currentLesson.value?.is_completed) {
            markComplete();
        }
    }
}

function prevLesson() {
    if (hasPrevLesson.value) {
        selectLesson(allLessons.value[currentLessonIndex.value - 1].id);
    }
}

function nextLesson() {
    if (hasNextLesson.value) {
        selectLesson(allLessons.value[currentLessonIndex.value + 1].id);
    }
}

onMounted(() => {
    fetchCourse();
});

defineExpose({ selectLesson });
</script>
