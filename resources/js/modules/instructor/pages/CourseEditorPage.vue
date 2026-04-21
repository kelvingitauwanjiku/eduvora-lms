<template>
  <div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">
          {{ isEditing ? 'Edit Course' : 'Create New Course' }}
        </h1>
        <p class="text-gray-500 mt-1">
          {{ isEditing ? 'Update your course details' : 'Add a new course to your catalog' }}
        </p>
      </div>
      <div class="flex gap-3">
        <button @click="saveDraft" :disabled="saving" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 disabled:opacity-50">
          {{ saving ? 'Saving...' : 'Save Draft' }}
        </button>
        <button @click="publishCourse" :disabled="saving" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 disabled:opacity-50">
          {{ saving ? 'Publishing...' : (isEditing ? 'Update' : 'Publish') }}
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Form -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Basic Info -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Course Title *</label>
              <input
                v-model="course.title"
                type="text"
                placeholder="Enter course title"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
              <textarea
                v-model="course.short_description"
                rows="3"
                placeholder="Brief description for course card"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              ></textarea>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Full Description *</label>
              <textarea
                v-model="course.description"
                rows="6"
                placeholder="Detailed course description"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              ></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                <select
                  v-model="course.category_id"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Select category</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                <select
                  v-model="course.level"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="beginner">Beginner</option>
                  <option value="intermediate">Intermediate</option>
                  <option value="advanced">Advanced</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Media -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Media</h2>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Image</label>
              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors cursor-pointer" @click="uploadThumbnail">
                <img v-if="course.thumbnail" :src="course.thumbnail" class="w-full h-48 object-cover rounded-lg mb-2" />
                <div v-else class="py-4">
                  <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-gray-500 text-sm">Click to upload or drag and drop</p>
                  <p class="text-gray-400 text-xs mt-1">PNG, JPG up to 2MB</p>
                </div>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Preview Video</label>
              <input
                v-model="course.video_url"
                type="text"
                placeholder="YouTube or Vimeo URL"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <p class="text-gray-400 text-xs mt-1">Enter YouTube or Vimeo video URL for course preview</p>
            </div>
          </div>
        </div>

        <!-- Pricing -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Pricing</h2>
          
          <div class="space-y-4">
            <div class="flex items-center gap-4">
              <label class="flex items-center">
                <input v-model="course.is_free" type="checkbox" class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500" />
                <span class="ml-2 text-gray-700">Free Course</span>
              </label>
            </div>
            
            <div v-if="!course.is_free" class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Regular Price</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                  <input
                    v-model="course.price"
                    type="number"
                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Discounted Price</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                  <input
                    v-model="course.discounted_price"
                    type="number"
                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Status -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
          <h3 class="font-semibold text-gray-900 mb-4">Course Status</h3>
          
          <div class="space-y-3">
            <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
              <span class="text-gray-700">Published</span>
              <input v-model="course.status" type="radio" value="active" class="text-blue-600" />
            </label>
            <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
              <span class="text-gray-700">Draft</span>
              <input v-model="course.status" type="radio" value="draft" class="text-blue-600" />
            </label>
            <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer">
              <span class="text-gray-700">Private</span>
              <input v-model="course.status" type="radio" value="private" class="text-blue-600" />
            </label>
          </div>
        </div>

        <!-- Features -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
          <h3 class="font-semibold text-gray-900 mb-4">Course Features</h3>
          
          <div class="space-y-3">
            <label class="flex items-center">
              <input v-model="course.features" type="checkbox" value="certificate" class="w-4 h-4 text-blue-600 rounded" />
              <span class="ml-2 text-sm text-gray-700">Certificate of Completion</span>
            </label>
            <label class="flex items-center">
              <input v-model="course.features" type="checkbox" value="lifetime" class="w-4 h-4 text-blue-600 rounded" />
              <span class="ml-2 text-sm text-gray-700">Lifetime Access</span>
            </label>
            <label class="flex items-center">
              <input v-model="course.features" type="checkbox" value="downloadable" class="w-4 h-4 text-blue-600 rounded" />
              <span class="ml-2 text-sm text-gray-700">Downloadable Resources</span>
            </label>
            <label class="flex items-center">
              <input v-model="course.features" type="checkbox" value="forum" class="w-4 h-4 text-blue-600 rounded" />
              <span class="ml-2 text-sm text-gray-700">Discussion Forum</span>
            </label>
          </div>
        </div>

        <!-- Requirements -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
          <h3 class="font-semibold text-gray-900 mb-4">Requirements</h3>
          
          <div class="space-y-2">
            <div v-for="(req, index) in course.requirements" :key="index" class="flex gap-2">
              <input
                v-model="course.requirements[index]"
                type="text"
                placeholder="Enter requirement"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
              />
              <button @click="removeRequirement(index)" class="p-2 text-gray-400 hover:text-red-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <button @click="addRequirement" class="text-sm text-blue-600 hover:underline">+ Add requirement</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { courseApi, categoryApi } from '../../../services/api';

const route = useRoute();
const router = useRouter();
const isEditing = computed(() => !!route.params.id);
const saving = ref(false);

const course = ref({
  title: '',
  short_description: '',
  description: '',
  category_id: '',
  level: 'beginner',
  thumbnail: '',
  video_url: '',
  is_free: false,
  price: 0,
  discounted_price: 0,
  status: 'draft',
  features: [],
  requirements: ['']
});

const categories = ref([]);

onMounted(async () => {
  try {
    const { data } = await categoryApi.getAll();
    categories.value = data;
    
    if (isEditing.value) {
      const { data: courseData } = await courseApi.getById(route.params.id);
      course.value = { ...courseData, features: courseData.features || [] };
    }
  } catch (error) {
    console.error('Error:', error);
  }
});

function addRequirement() {
  course.value.requirements.push('');
}

function removeRequirement(index) {
  course.value.requirements.splice(index, 1);
}

async function saveDraft() {
  saving.value = true;
  try {
    if (isEditing.value) {
      await courseApi.update(route.params.id, course.value);
    } else {
      await courseApi.create(course.value);
    }
    router.push({ name: 'instructor-courses' });
  } catch (error) {
    console.error('Error:', error);
  } finally {
    saving.value = false;
  }
}

async function publishCourse() {
  course.value.status = 'active';
  await saveDraft();
}

function uploadThumbnail() {
  // File upload logic here
}
</script>