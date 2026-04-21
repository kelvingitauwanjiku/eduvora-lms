<template>
    <div>
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <router-link to="/instructor/courses" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </router-link>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ isEditing ? 'Edit Course' : 'Create Course' }}</h1>
                    <p class="mt-1 text-gray-500">{{ isEditing ? 'Update your course details' : 'Add a new course to your catalog' }}</p>
                </div>
            </div>
        </div>

        <div v-if="!isEditing" class="mb-8">
            <nav class="flex items-center justify-center" aria-label="Progress">
                <ol class="flex items-center space-x-4">
                    <li v-for="(step, index) in steps" :key="step.id">
                        <div class="flex items-center">
                            <button @click="currentStep = index" :disabled="index > savedStep && !isEditing"
                                class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-colors"
                                :class="index < currentStep ? 'bg-green-600 text-white' : index === currentStep ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600 disabled:opacity-50'">
                                <svg v-if="index < currentStep" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span v-else>{{ index + 1 }}</span>
                            </button>
                            <span v-if="index < steps.length - 1" class="hidden sm:block w-16 h-0.5 bg-gray-200 mx-2" 
                                :class="index < currentStep ? 'bg-green-600' : 'bg-gray-200'"></span>
                        </div>
                    </li>
                </ol>
            </nav>
            <div class="mt-4 text-center">
                <p class="text-sm font-medium text-gray-900">{{ steps[currentStep].name }}</p>
                <p class="text-sm text-gray-500">{{ steps[currentStep].description }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form @submit.prevent="handleSubmit">
                <div v-show="currentStep === 0 || isEditing">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h3>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Course Title *</label>
                            <input v-model="form.title" type="text" required placeholder="e.g., Introduction to Web Development"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-sm text-gray-500">Choose a clear, descriptive title (min 10 characters)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input v-model="form.slug" type="text" placeholder="auto-generated from title"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-sm text-gray-500">URL-friendly version of the title</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Short Description *</label>
                            <textarea v-model="form.short_description" rows="3" placeholder="Brief summary displayed in course cards (max 150 chars)"
                                maxlength="150" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            <p class="mt-1 text-sm text-gray-500">{{ form.short_description?.length || 0 }}/150 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea v-model="form.description" rows="6" placeholder="Detailed course description - what students will learn"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </div>

                <div v-show="currentStep === 1 || isEditing" class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Category & Pricing</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                            <select v-model="form.category_id" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select category</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subcategory</label>
                            <select v-model="form.subcategory_id"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select subcategory</option>
                                <option v-for="sub in subcategories" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price ($) *</label>
                            <input v-model="form.price" type="number" step="0.01" min="0" placeholder="0 for free" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :disabled="form.is_free">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount Price ($)</label>
                            <input v-model="form.discount_price" type="number" step="0.01" min="0" placeholder="Optional"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :disabled="form.is_free">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                            <select v-model="form.level"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="all">All Levels</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                        <input v-model="form.is_free" type="checkbox" id="is_free" class="w-5 h-5 text-blue-600 rounded">
                        <label for="is_free" class="text-sm font-medium text-gray-700">This is a free course</label>
                    </div>
                </div>

                <div v-show="currentStep === 2 || isEditing" class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Media</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Course Thumbnail</label>
                        <div class="mt-2 flex items-center gap-6">
                            <div class="w-64 h-36 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center overflow-hidden bg-gray-50">
                                <img v-if="thumbnailPreview" :src="thumbnailPreview" class="w-full h-full object-cover">
                                <div v-else class="text-gray-400 text-center p-4">
                                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs">1280x720 recommended</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="file" @change="handleThumbnail" accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-2 text-xs text-gray-500">PNG, JPG, GIF up to 2MB. Recommended size: 1280x720px</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preview Video URL</label>
                        <input v-model="form.video_url" type="url" placeholder="https://youtube.com/watch?v=..."
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">YouTube or Vimeo URL for course preview (max 30 seconds recommended)</p>
                    </div>
                </div>

                <div v-show="currentStep === 3 || isEditing" class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Course Details</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
                        <textarea v-model="form.requirements" rows="4" placeholder="What students need to know before taking this course (one per line)"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        <p class="mt-1 text-sm text-gray-500">List prerequisites or requirements</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Outcomes</label>
                        <textarea v-model="form.outcomes" rows="4" placeholder="What students will be able to do after completing (one per line)"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        <p class="mt-1 text-sm text-gray-500">List learning outcomes or skills students will gain</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instructor Notes</label>
                        <textarea v-model="form.instructor_notes" rows="3" placeholder="Private notes for your reference (not visible to students)"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        <p class="mt-1 text-sm text-gray-500">Only visible to you</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Course Language</label>
                            <select v-model="form.language"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="en">English</option>
                                <option value="es">Spanish</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                                <option value="hi">Hindi</option>
                                <option value="pt">Portuguese</option>
                                <option value="ar">Arabic</option>
                                <option value="zh">Chinese</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration (hours)</label>
                            <input v-model="form.duration" type="number" min="0" step="0.5" placeholder="Estimated hours"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between items-center">
                    <div>
                        <button v-if="!isEditing && currentStep > 0" type="button" @click="currentStep--"
                            class="px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50">
                            Previous
                        </button>
                    </div>
                    <div class="flex gap-4">
                        <router-link to="/instructor/courses" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50">
                            Cancel
                        </router-link>
                        <button v-if="!isEditing && currentStep < steps.length - 1" type="button" @click="nextStep"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Next Step
                        </button>
                        <button v-if="!isEditing && currentStep === steps.length - 1" type="submit" :disabled="saving"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
                            {{ saving ? 'Creating...' : 'Create Course' }}
                        </button>
                        <button v-if="isEditing" type="submit" :disabled="saving"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                            {{ saving ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { categoryApi, courseApi } from '@/services/api';

const router = useRouter();
const route = useRoute();

const isEditing = computed(() => !!route.params.id);
const currentStep = ref(0);
const savedStep = ref(0);
const saving = ref(false);
const categories = ref([]);
const thumbnailPreview = ref('');

const steps = [
    { id: 1, name: 'Basic Info', description: 'Course title and description' },
    { id: 2, name: 'Pricing', description: 'Category and pricing' },
    { id: 3, name: 'Media', description: 'Thumbnail and preview video' },
    { id: 4, name: 'Details', description: 'Requirements and outcomes' },
];

const form = reactive({
    title: '',
    slug: '',
    short_description: '',
    description: '',
    category_id: '',
    subcategory_id: '',
    price: 0,
    discount_price: 0,
    level: 'beginner',
    is_free: false,
    thumbnail: null,
    video_url: '',
    requirements: '',
    outcomes: '',
    instructor_notes: '',
    language: 'en',
    duration: 0,
    status: 'draft',
});

const subcategories = computed(() => {
    if (!form.category_id) return [];
    const cat = categories.value.find(c => c.id === Number(form.category_id));
    return cat?.subcategories || [];
});

watch(() => form.title, (val) => {
    if (!isEditing.value && val) {
        form.slug = val.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
    }
});

watch(() => form.is_free, (val) => {
    if (val) {
        form.price = 0;
        form.discount_price = 0;
    }
});

async function fetchCategories() {
    try {
        const { data } = await categoryApi.getAll();
        categories.value = data.data || [];
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
}

async function fetchCourse() {
    if (!route.params.id) return;
    try {
        const { data } = await courseApi.getById(route.params.id);
        Object.keys(form).forEach(key => {
            if (data[key] !== undefined) {
                form[key] = data[key];
            }
        });
        thumbnailPreview.value = data.thumbnail || '';
    } catch (error) {
        console.error('Error fetching course:', error);
    }
}

function handleThumbnail(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            return;
        }
        form.thumbnail = file;
        thumbnailPreview.value = URL.createObjectURL(file);
    }
}

function nextStep() {
    if (currentStep.value < steps.length - 1) {
        savedStep.value = Math.max(savedStep.value, currentStep.value);
        currentStep.value++;
    }
}

async function handleSubmit() {
    saving.value = true;
    try {
        const formData = new FormData();
        Object.keys(form).forEach(key => {
            if (form[key] !== null && form[key] !== undefined) {
                if (key === 'thumbnail' && form[key] instanceof File) {
                    formData.append(key, form[key]);
                } else if (key === 'is_free') {
                    formData.append(key, form[key] ? '1' : '0');
                } else {
                    formData.append(key, form[key]);
                }
            }
        });
        
        if (isEditing.value) {
            await courseApi.update(route.params.id, formData);
        } else {
            const { data } = await courseApi.create(formData);
            router.push(`/instructor/courses/${data.id}/edit`);
            return;
        }
        router.push('/instructor/courses');
    } catch (error) {
        console.error('Error saving course:', error);
        alert(error.response?.data?.message || 'Error saving course');
    } finally {
        saving.value = false;
    }
}

onMounted(() => {
    fetchCategories();
    if (route.params.id) {
        fetchCourse();
        currentStep.value = 0;
    }
});
</script>
