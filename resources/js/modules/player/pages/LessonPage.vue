<template>
    <PlayerLayout>
        <div class="h-full flex">
            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                <!-- Video/Content Area -->
                <div class="flex-1 bg-black flex items-center justify-center">
                    <div v-if="loading" class="flex items-center justify-center h-full">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                    </div>
                    
                    <div v-else-if="lesson" class="w-full h-full flex flex-col">
                        <div class="aspect-video bg-gray-900 relative flex-1">
                            <!-- Video Player -->
                            <div v-if="lesson.type === 'video' && lesson.video_url" class="w-full h-full">
                                <video 
                                    ref="videoPlayer"
                                    class="w-full h-full"
                                    controls
                                    :src="lesson.video_url"
                                ></video>
                            </div>
                            
                            <!-- Text Content -->
                            <div v-else-if="lesson.type === 'text'" class="w-full h-full overflow-auto p-8 text-white">
                                <div class="max-w-4xl mx-auto prose prose-invert" v-html="lesson.content"></div>
                            </div>
                            
                            <!-- PDF Viewer -->
                            <div v-else-if="lesson.type === 'pdf'" class="w-full h-full flex items-center justify-center bg-gray-800">
                                <div class="text-center max-w-md">
                                    <svg class="w-24 h-24 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-4 text-white text-xl font-semibold">{{ lesson.title }}</p>
                                    <p class="mt-2 text-gray-400">PDF Document</p>
                                    <div class="mt-6 flex flex-col gap-3">
                                        <a v-if="lesson.resource_url" :href="lesson.resource_url" target="_blank" 
                                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                            Download PDF
                                        </a>
                                        <button @click="openPdfViewer = true" class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                                            View Online
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quiz -->
                            <div v-else-if="lesson.type === 'quiz'" class="w-full h-full flex items-center justify-center bg-gray-800">
                                <div class="text-center">
                                    <svg class="w-24 h-24 mx-auto text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    <p class="mt-4 text-white text-xl font-semibold">{{ lesson.title }}</p>
                                    <p class="mt-2 text-gray-400">Quiz</p>
                                    <router-link :to="`/student/courses/${courseId}/quiz/${lesson.id}`" 
                                        class="mt-6 inline-block px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                        Start Quiz
                                    </router-link>
                                </div>
                            </div>
                            
                            <!-- Placeholder -->
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
                        
                        <!-- Lesson Info -->
                        <div class="bg-gray-800 px-6 py-4 flex items-center justify-between">
                            <div>
                                <h2 class="text-white font-semibold">{{ lesson.title }}</h2>
                                <p class="text-gray-400 text-sm">{{ lesson.sectionTitle }}</p>
                            </div>
                            <button 
                                v-if="!lesson.is_completed"
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
                
                <!-- Navigation -->
                <div class="bg-gray-800 border-t border-gray-700 px-6 py-4 flex items-center justify-between">
                    <button @click="prevLesson" :disabled="!hasPrev" 
                        class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition">
                        Previous
                    </button>
                    <button @click="nextLesson" :disabled="!hasNext"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                        Next
                    </button>
                </div>
            </div>

            <!-- Sidebar Tabs -->
            <aside class="w-80 bg-gray-800 border-l border-gray-700 flex flex-col">
                <div class="flex border-b border-gray-700">
                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                        :class="activeTab === tab.id ? 'text-blue-400 border-b-2 border-blue-400' : 'text-gray-400 hover:text-white'"
                        class="flex-1 py-3 text-sm font-medium transition">
                        {{ tab.label }}
                    </button>
                </div>
                
                <!-- Curriculum Tab -->
                <div v-if="activeTab === 'curriculum'" class="flex-1 overflow-y-auto">
                    <div v-for="section in sections" :key="section.id" class="border-b border-gray-700">
                        <div class="px-4 py-3 bg-gray-700/50">
                            <h4 class="font-medium text-white text-sm">{{ section.title }}</h4>
                            <p class="text-xs text-gray-400 mt-0.5">{{ section.lessons?.length || 0 }} lessons</p>
                        </div>
                        <div>
                            <button v-for="lsn in section.lessons" :key="lsn.id" @click="goToLesson(lsn.id)"
                                :class="lsn.id === lessonId ? 'bg-gray-700 text-white' : 'hover:bg-gray-700/50 text-gray-300'"
                                class="w-full px-4 py-3 flex items-center gap-3 text-left text-sm transition">
                                <svg v-if="lsn.is_completed" class="w-5 h-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <svg v-else-if="lsn.type === 'video'" class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                </svg>
                                <svg v-else-if="lsn.type === 'quiz'" class="w-5 h-5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <svg v-else class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="flex-1 truncate">{{ lsn.title }}</span>
                                <span class="text-xs text-gray-500 shrink-0">{{ lsn.duration }}</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Notes Tab -->
                <div v-if="activeTab === 'notes'" class="flex-1 flex flex-col p-4">
                    <textarea v-model="noteContent" placeholder="Take notes for this lesson..."
                        class="flex-1 w-full bg-gray-700 text-white rounded-lg p-3 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                    <button @click="saveNote" :disabled="savingNote"
                        class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition">
                        {{ savingNote ? 'Saving...' : 'Save Note' }}
                    </button>
                </div>
                
                <!-- Resources Tab -->
                <div v-if="activeTab === 'resources'" class="flex-1 overflow-y-auto p-4">
                    <div v-if="resources.length > 0" class="space-y-3">
                        <a v-for="resource in resources" :key="resource.id" :href="resource.url" target="_blank"
                            class="flex items-center gap-3 p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                            <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-white text-sm truncate">{{ resource.title }}</p>
                                <p class="text-gray-400 text-xs">{{ resource.type }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                    </div>
                    <div v-else class="text-center text-gray-400 py-8">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p>No resources available</p>
                    </div>
                </div>
            </aside>
        </div>
    </PlayerLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import PlayerLayout from '@/layouts/PlayerLayout.vue';
import api from '@/services/api';

const route = useRoute();
const router = useRouter();

const courseId = computed(() => route.params.id);
const lessonId = computed(() => route.params.lessonId);

const loading = ref(true);
const lesson = ref(null);
const course = ref(null);
const activeTab = ref('curriculum');
const noteContent = ref('');
const savingNote = ref(false);
const openPdfViewer = ref(false);

const tabs = [
    { id: 'curriculum', label: 'Curriculum' },
    { id: 'notes', label: 'Notes' },
    { id: 'resources', label: 'Resources' },
];

const sections = computed(() => course.value?.sections || []);

const allLessons = computed(() => {
    if (!sections.value.length) return [];
    return sections.value.flatMap(section => 
        (section.lessons || []).map(l => ({ ...l, sectionTitle: section.title }))
    );
});

const currentIndex = computed(() => allLessons.value.findIndex(l => l.id === lessonId.value));
const hasPrev = computed(() => currentIndex.value > 0);
const hasNext = computed(() => currentIndex.value < allLessons.value.length - 1);

const resources = computed(() => {
    return lesson.value?.resources || [];
});

async function fetchData() {
    loading.value = true;
    try {
        const { data } = await api.get(`/courses/${courseId.value}`);
        course.value = data;
        
        if (lessonId.value) {
            lesson.value = allLessons.value.find(l => l.id === parseInt(lessonId.value));
        } else if (allLessons.value.length > 0) {
            lesson.value = allLessons.value[0];
            router.replace(`/student/courses/${courseId.value}/learn/${lesson.value.id}`);
        }
        
        if (lesson.value) {
            const savedNote = localStorage.getItem(`note_${lesson.value.id}`);
            if (savedNote) noteContent.value = savedNote;
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
}

async function markComplete() {
    try {
        await api.post(`/lessons/${lessonId.value}/complete`);
        if (lesson.value) lesson.value.is_completed = true;
        const lsn = allLessons.value.find(l => l.id === lessonId.value);
        if (lsn) lsn.is_completed = true;
    } catch (error) {
        console.error('Error marking complete:', error);
    }
}

function saveNote() {
    savingNote.value = true;
    try {
        localStorage.setItem(`note_${lessonId.value}`, noteContent.value);
        setTimeout(() => {
            savingNote.value = false;
        }, 500);
    } catch (error) {
        console.error('Error saving note:', error);
        savingNote.value = false;
    }
}

function goToLesson(id) {
    router.push(`/student/courses/${courseId.value}/learn/${id}`);
}

function prevLesson() {
    if (hasPrev.value) {
        goToLesson(allLessons.value[currentIndex.value - 1].id);
    }
}

function nextLesson() {
    if (hasNext.value) {
        goToLesson(allLessons.value[currentIndex.value + 1].id);
    }
}

watch(lessonId, () => {
    if (lessonId.value) {
        lesson.value = allLessons.value.find(l => l.id === parseInt(lessonId.value));
        const savedNote = localStorage.getItem(`note_${lessonId.value}`);
        noteContent.value = savedNote || '';
    }
});

onMounted(() => {
    fetchData();
});
</script>
