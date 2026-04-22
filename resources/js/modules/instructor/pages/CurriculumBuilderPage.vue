<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Course Curriculum</h1>
        <p class="text-gray-500 mt-1">Organize your course content</p>
      </div>
      <button @click="addSection" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700">
        Add Section
      </button>
    </div>

    <div v-if="loading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="bg-white rounded-xl border border-gray-200 p-4 animate-pulse">
        <div class="h-6 bg-gray-200 rounded w-1/3 mb-4"></div>
        <div class="space-y-2">
          <div class="h-16 bg-gray-100 rounded"></div>
          <div class="h-16 bg-gray-100 rounded"></div>
        </div>
      </div>
    </div>

    <div v-else class="space-y-4">
      <div 
        v-for="(section, sIndex) in sections" 
        :key="section.id"
        class="bg-white rounded-xl border border-gray-200 overflow-hidden"
      >
        <!-- Section Header -->
        <div class="flex items-center justify-between p-4 bg-gray-50">
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-gray-400 cursor-move" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
            </svg>
            <input
              v-model="section.title"
              @blur="updateSection(section)"
              class="bg-transparent font-semibold text-gray-900 focus:outline-none focus:border-b-2 border-blue-500"
            />
            <span class="text-sm text-gray-500">{{ section.lessons?.length || 0 }} lessons</span>
          </div>
          <div class="flex items-center gap-2">
            <button @click="addLesson(sIndex)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </button>
            <button @click="deleteSection(sIndex)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Lessons -->
        <div class="divide-y divide-gray-100">
          <div 
            v-for="(lesson, lIndex) in section.lessons" 
            :key="lesson.id"
            class="flex items-center gap-4 p-4 hover:bg-gray-50"
          >
            <svg class="w-5 h-5 text-gray-400 cursor-move" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
            </svg>
            
            <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center">
              <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="lesson.type === 'video'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            
            <div class="flex-1">
              <input
                v-model="lesson.title"
                @blur="updateLesson(lesson)"
                class="w-full bg-transparent font-medium text-gray-900 focus:outline-none"
              />
              <div class="flex items-center gap-3 mt-1">
                <span class="text-xs text-gray-500 capitalize">{{ lesson.type }}</span>
                <span v-if="lesson.duration" class="text-xs text-gray-400">{{ lesson.duration }} min</span>
              </div>
            </div>
            
            <div class="flex items-center gap-2">
              <button v-if="!lesson.is_preview" @click="togglePreview(lesson)" class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">
                Make Preview
              </button>
              <button v-else class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded">
                Preview
              </button>
              <button @click="editLesson(lesson)" class="p-2 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9m-2 2l1.586-1.586a2 2 0 012.828 0L15 10" />
                </svg>
              </button>
              <button @click="deleteLesson(sIndex, lIndex)" class="p-2 text-gray-400 hover:text-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
          
          <!-- Add Lesson Button -->
          <button @click="addLesson(sIndex)" class="w-full p-4 text-center text-gray-500 hover:text-gray-700 hover:bg-gray-50 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Lesson
          </button>
        </div>
      </div>
    </div>

    <!-- Lesson Modal -->
    <div v-if="showLessonModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold mb-4">{{ editingLesson?.id ? 'Edit' : 'Add' }} Lesson</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Title *</label>
            <input v-model="lessonForm.title" class="w-full px-4 py-2 border rounded-lg" />
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1">Lesson Type</label>
            <select v-model="lessonForm.lesson_type" class="w-full px-4 py-2 border rounded-lg">
              <option value="video">Video</option>
              <option value="text">Text</option>
              <option value="pdf">PDF</option>
              <option value="quiz">Quiz</option>
              <option value="assignment">Assignment</option>
            </select>
          </div>
          
          <div v-if="lessonForm.lesson_type === 'video'">
            <label class="block text-sm font-medium mb-1">Video Source</label>
            <select v-model="lessonForm.video_source" class="w-full px-4 py-2 border rounded-lg">
              <option value="upload">Upload Video</option>
              <option value="youtube">YouTube</option>
              <option value="vimeo">Vimeo</option>
              <option value="url">External URL</option>
            </select>
            
            <div class="mt-2" v-if="lessonForm.video_source !== 'upload'">
              <input v-model="lessonForm.video_url" :placeholder="lessonForm.video_source === 'youtube' ? 'YouTube URL' : 'Video URL'" class="w-full px-4 py-2 border rounded-lg" />
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1">Duration (minutes)</label>
            <input v-model="lessonForm.duration" type="number" class="w-full px-4 py-2 border rounded-lg" />
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1">Content</label>
            <textarea v-model="lessonForm.content" rows="6" class="w-full px-4 py-2 border rounded-lg"></textarea>
          </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6">
          <button @click="closeLessonModal" class="px-4 py-2 bg-gray-100 rounded-lg">Cancel</button>
          <button @click="saveLesson" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { instructorApi } from '@/services/api';

const route = useRoute();
const loading = ref(true);
const sections = ref([]);
const showLessonModal = ref(false);
const editingLesson = ref(null);

const lessonForm = ref({
  title: '',
  lesson_type: 'video',
  video_source: 'youtube',
  video_url: '',
  duration: 0,
  content: ''
});

onMounted(async () => {
  try {
    const { data } = await instructorApi.getCurriculum(route.params.courseId);
    sections.value = data;
  } catch (error) {
    console.error('Error:', error);
  } finally {
    loading.value = false;
  }
});

async function addSection() {
  const newSection = { title: 'New Section', lessons: [] };
  sections.value.push(newSection);
}

async function updateSection(section) {
  try {
    await instructorApi.updateCurriculum(route.params.courseId, { sections: sections.value });
  } catch (error) {
    console.error('Error:', error);
  }
}

async function deleteSection(index) {
  sections.value.splice(index, 1);
}

function addLesson(sectionIndex) {
  editingLesson.value = { sectionIndex };
  lessonForm.value = { title: '', lesson_type: 'video', video_source: 'youtube', video_url: '', duration: 0, content: '' };
  showLessonModal.value = true;
}

function editLesson(lesson) {
  editingLesson.value = lesson;
  lessonForm.value = { ...lesson };
  showLessonModal.value = true;
}

function closeLessonModal() {
  showLessonModal.value = false;
  editingLesson.value = null;
}

async function saveLesson() {
  if (editingLesson.value && editingLesson.value.sectionIndex !== undefined) {
    sections.value[editingLesson.value.sectionIndex].lessons.push({
      ...lessonForm.value,
      id: Date.now()
    });
  }
  closeLessonModal();
}

async function deleteLesson(sIndex, lIndex) {
  sections.value[sIndex].lessons.splice(lIndex, 1);
}

function togglePreview(lesson) {
  lesson.is_preview = !lesson.is_preview;
}

function updateLesson(lesson) {
  // Save lesson changes
}
</script>