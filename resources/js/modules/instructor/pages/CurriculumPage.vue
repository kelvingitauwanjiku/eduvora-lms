<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <router-link :to="`/instructor/courses/${courseId}/edit`" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </router-link>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Curriculum Builder</h1>
                    <p class="mt-1 text-gray-500">{{ sections.length }} sections, {{ totalLessons }} lessons</p>
                </div>
            </div>
            <div class="flex gap-3">
                <router-link :to="`/instructor/courses/${courseId}/edit`" class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50">
                    Back to Course
                </router-link>
                <button @click="saveCurriculum" :disabled="saving" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2">
                    <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ saving ? 'Saving...' : 'Save Curriculum' }}
                </button>
            </div>
        </div>

        <div v-if="loading" class="animate-pulse">
            <div v-for="i in 3" :key="i" class="bg-gray-200 h-32 rounded-xl mb-4"></div>
        </div>

        <div v-else class="space-y-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <button @click="addSection" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Section
                    </button>
                    <span class="text-sm text-gray-500">Drag sections to reorder</span>
                </div>
                <div class="text-sm text-gray-500">
                    Total Duration: {{ formatDuration(totalDuration) }}
                </div>
            </div>

            <div v-if="sections.length === 0" class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No curriculum yet</h3>
                <p class="text-gray-500 mb-4">Start building your course by adding sections and lessons</p>
                <button @click="addSection" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Add First Section
                </button>
            </div>

            <div v-else class="space-y-4">
                <div v-for="(section, sIndex) in sections" :key="section.id || sIndex" 
                    class="bg-white rounded-xl border border-gray-200 overflow-hidden"
                    :class="{ 'border-blue-500 ring-2 ring-blue-100': dragOverSection === sIndex }">
                    <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between cursor-move"
                        @dragstart="onSectionDragStart(sIndex)" @dragover.prevent="onSectionDragOver(sIndex)" @drop="onSectionDrop(sIndex)" @dragend="onDragEnd">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                            <button @click="toggleSection(sIndex)" class="p-1 hover:bg-gray-200 rounded transition">
                                <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-90': section.expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <input v-model="section.title" type="text" placeholder="Section title" 
                                class="font-semibold text-gray-900 bg-transparent border-none focus:ring-0 text-lg w-full max-w-md"
                                @blur="updateSection(section)">
                            <span class="text-sm text-gray-500">({{ section.lessons?.length || 0 }} lessons)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="openDripModal(sIndex)" class="text-sm px-3 py-1 text-blue-600 hover:bg-blue-50 rounded-lg">
                                Drip Settings
                            </button>
                            <button @click="addLesson(sIndex)" class="text-sm px-3 py-1 text-green-600 hover:bg-green-50 rounded-lg flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Lesson
                            </button>
                            <button @click="deleteSection(sIndex)" class="text-sm px-3 py-1 text-red-600 hover:bg-red-50 rounded-lg">
                                Delete
                            </button>
                        </div>
                    </div>
                    
                    <div v-if="section.expanded" class="p-4">
                        <div v-if="!section.lessons || section.lessons.length === 0" class="text-center py-8 text-gray-500">
                            No lessons yet. Click "Add Lesson" to create one.
                        </div>
                        <div v-else class="space-y-2">
                            <div v-for="(lesson, lIndex) in section.lessons" :key="lesson.id || lIndex"
                                class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                                :class="{ 'border-l-4 border-blue-500': selectedLesson?.sIndex === sIndex && selectedLesson?.lIndex === lIndex }"
                                @click="selectLesson(sIndex, lIndex)">
                                <svg class="w-5 h-5 text-gray-400 cursor-move" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    @dragstart="onLessonDragStart(sIndex, lIndex)" @dragover.prevent="onLessonDragOver(sIndex, lIndex)" @drop="onLessonDrop(sIndex, lIndex)">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                </svg>
                                <div class="flex-1 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                        :class="{
                                            'bg-blue-100 text-blue-600': lesson.type === 'video',
                                            'bg-green-100 text-green-600': lesson.type === 'article',
                                            'bg-purple-100 text-purple-600': lesson.type === 'quiz',
                                            'bg-orange-100 text-orange-600': lesson.type === 'assignment'
                                        }">
                                        <svg v-if="lesson.type === 'video'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                                        </svg>
                                        <svg v-else-if="lesson.type === 'article'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                        </svg>
                                        <svg v-else-if="lesson.type === 'quiz'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd" />
                                        </svg>
                                        <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input v-model="lesson.title" type="text" placeholder="Lesson title" 
                                        class="flex-1 bg-transparent border-none focus:ring-0 font-medium"
                                        @blur="updateLesson(section, lesson)">
                                </div>
                                <div class="flex items-center gap-2">
                                    <select v-model="lesson.type" class="text-sm border-gray-200 rounded px-2 py-1 bg-white" @change="updateLesson(section, lesson)">
                                        <option value="video">Video</option>
                                        <option value="article">Article</option>
                                        <option value="quiz">Quiz</option>
                                        <option value="assignment">Assignment</option>
                                    </select>
                                    <span v-if="lesson.duration" class="text-xs text-gray-500">{{ formatDuration(lesson.duration) }}</span>
                                    <button @click="editLesson(sIndex, lIndex)" class="p-1 text-gray-400 hover:text-blue-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteLesson(sIndex, lIndex)" class="p-1 text-gray-400 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showDripModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showDripModal = false">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Drip Content Settings</h3>
                    <button @click="showDripModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Release Type</label>
                        <select v-model="dripSettings.release_type" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="immediate">Immediate</option>
                            <option value="scheduled">Scheduled (Date/Time)</option>
                            <option value="days_after_enrollment">Days After Enrollment</option>
                        </select>
                    </div>
                    <div v-if="dripSettings.release_type === 'scheduled'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Release Date</label>
                        <input v-model="dripSettings.release_date" type="datetime-local" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div v-if="dripSettings.release_type === 'days_after_enrollment'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Days After Enrollment</label>
                        <input v-model="dripSettings.days_after" type="number" min="1" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="showDripModal = false" class="px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50">Cancel</button>
                        <button @click="saveDripSettings" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { courseApi } from '@/services/api';

const route = useRoute();
const router = useRouter();
const courseId = route.params.courseId;

const loading = ref(true);
const saving = ref(false);
const sections = ref([]);
const dragOverSection = ref(-1);
const draggedSectionIndex = ref(-1);
const showDripModal = ref(false);
const dripSectionIndex = ref(-1);
const dripSettings = reactive({
    release_type: 'immediate',
    release_date: '',
    days_after: 1,
});
const selectedLesson = ref(null);

const totalLessons = computed(() => sections.value.reduce((sum, s) => sum + (s.lessons?.length || 0), 0));
const totalDuration = computed(() => sections.value.reduce((sum, s) => {
    return sum + (s.lessons || []).reduce((lessonSum, l) => lessonSum + (l.duration || 0), 0);
}, 0));

function formatDuration(seconds) {
    if (!seconds) return '0m';
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    if (hours > 0) return `${hours}h ${minutes}m`;
    return `${minutes}m`;
}

function toggleSection(index) {
    sections.value[index].expanded = !sections.value[index].expanded;
}

function addSection() {
    sections.value.push({ 
        title: '', 
        expanded: true, 
        lessons: [],
        drip_settings: { release_type: 'immediate' }
    });
}

function deleteSection(index) {
    if (confirm('Delete this section and all its lessons?')) {
        sections.value.splice(index, 1);
    }
}

function addLesson(sectionIndex) {
    sections.value[sectionIndex].lessons.push({ 
        title: '', 
        type: 'video',
        duration: 0,
        content: '',
    });
}

function deleteLesson(sectionIndex, lessonIndex) {
    sections.value[sectionIndex].lessons.splice(lessonIndex, 1);
}

function selectLesson(sIndex, lIndex) {
    selectedLesson.value = { sIndex, lIndex };
}

function editLesson(sIndex, lIndex) {
    const lesson = sections.value[sIndex].lessons[lIndex];
    router.push(`/instructor/curriculum/${courseId}/lesson/${lesson.id || 'new'}`);
}

function updateSection(section) {
    // Auto-save on blur
}

function updateLesson(section, lesson) {
    // Auto-save on blur
}

function openDripModal(sectionIndex) {
    dripSectionIndex.value = sectionIndex;
    Object.assign(dripSettings, sections.value[sectionIndex].drip_settings || { release_type: 'immediate' });
    showDripModal.value = true;
}

function saveDripSettings() {
    sections.value[dripSectionIndex.value].drip_settings = { ...dripSettings };
    showDripModal.value = false;
}

function onSectionDragStart(index) {
    draggedSectionIndex.value = index;
}

function onSectionDragOver(index) {
    dragOverSection.value = index;
}

function onSectionDrop(targetIndex) {
    if (draggedSectionIndex.value !== -1 && draggedSectionIndex.value !== targetIndex) {
        const [removed] = sections.value.splice(draggedSectionIndex.value, 1);
        sections.value.splice(targetIndex, 0, removed);
    }
    onDragEnd();
}

function onLessonDragStart(sIndex, lIndex) {
    draggedSectionIndex.value = { section: sIndex, lesson: lIndex };
}

function onLessonDragOver(sIndex, lIndex) {
    // Visual feedback
}

function onLessonDrop(targetIndex) {
    if (draggedSectionIndex.value && draggedSectionIndex.value.section !== undefined) {
        const { section: sourceSection, lesson: sourceLesson } = draggedSectionIndex.value;
        const [removed] = sections.value[sourceSection].lessons.splice(sourceLesson, 1);
        sections.value[targetIndex].lessons.splice(targetIndex, 0, removed);
    }
    onDragEnd();
}

function onDragEnd() {
    draggedSectionIndex.value = -1;
    dragOverSection.value = -1;
}

async function saveCurriculum() {
    saving.value = true;
    try {
        const curriculumData = {
            sections: sections.value.map((s, sIndex) => ({
                id: s.id,
                title: s.title,
                order: sIndex,
                drip_settings: s.drip_settings,
                lessons: (s.lessons || []).map((l, lIndex) => ({
                    id: l.id,
                    title: l.title,
                    type: l.type,
                    duration: l.duration,
                    order: lIndex,
                })),
            })),
        };
        
        await courseApi.updateCurriculum(courseId, curriculumData);
        alert('Curriculum saved successfully!');
    } catch (error) {
        console.error('Error saving curriculum:', error);
    } finally {
        saving.value = false;
    }
}

async function fetchCurriculum() {
    loading.value = true;
    try {
        const { data } = await courseApi.getCurriculum(courseId);
        sections.value = data.sections || [];
        sections.value.forEach(s => s.expanded = true);
    } catch (error) {
        console.error('Error fetching curriculum:', error);
        sections.value = [];
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    fetchCurriculum();
});
</script>
