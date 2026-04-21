<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <router-link to="/instructor/courses" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </router-link>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Course</h1>
                    <p class="mt-1 text-gray-500">{{ course.title || 'Loading...' }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button @click="previewCourse" class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Preview
                </button>
                <button @click="saveCourse" :disabled="saving" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2">
                    <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ saving ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </div>

        <div v-if="loading" class="animate-pulse">
            <div class="bg-gray-200 h-96 rounded-xl"></div>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Course Title</label>
                            <input v-model="form.title" type="text" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                            <textarea v-model="form.short_description" rows="2" maxlength="150"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            <p class="mt-1 text-sm text-gray-500">{{ form.short_description?.length || 0 }}/150 characters</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea v-model="form.description" rows="6" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Pricing</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                            <input v-model="form.price" type="number" step="0.01" min="0" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :disabled="form.is_free">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount Price ($)</label>
                            <input v-model="form.discount_price" type="number" step="0.01" min="0" 
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
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg mt-4">
                        <input v-model="form.is_free" type="checkbox" id="is_free" class="w-5 h-5 text-blue-600 rounded">
                        <label for="is_free" class="text-sm font-medium text-gray-700">This course is free</label>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Media</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Course Thumbnail</label>
                            <div class="mt-2 flex items-center gap-6">
                                <div class="w-48 h-28 rounded-lg overflow-hidden border border-gray-200">
                                    <img :src="thumbnailPreview || '/images/course-placeholder.jpg'" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <input type="file" @change="handleThumbnail" accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="mt-2 text-xs text-gray-500">PNG, JPG up to 2MB</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Preview Video URL</label>
                            <input v-model="form.video_url" type="url" placeholder="https://youtube.com/watch?v=..." 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Additional Details</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
                            <textarea v-model="form.requirements" rows="3" placeholder="One requirement per line"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Outcomes</label>
                            <textarea v-model="form.outcomes" rows="3" placeholder="One outcome per line"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Course Settings</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select v-model="form.category_id" 
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                            <select v-model="form.language"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="en">English</option>
                                <option value="es">Spanish</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Course Status</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">Current Status</p>
                                <span class="px-2 py-1 text-xs font-medium rounded-full mt-1 inline-block"
                                    :class="{
                                        'bg-green-100 text-green-800': form.status === 'published',
                                        'bg-yellow-100 text-yellow-800': form.status === 'draft',
                                        'bg-blue-100 text-blue-800': form.status === 'pending'
                                    }">
                                    {{ form.status || 'draft' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input v-model="form.is_featured" type="checkbox" id="is_featured" class="w-4 h-4 text-blue-600 rounded">
                            <label for="is_featured" class="text-sm text-gray-700">Featured course</label>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Quick Actions</h2>
                    <div class="space-y-3">
                        <router-link :to="`/instructor/curriculum/${course.id}`" 
                            class="flex items-center gap-3 w-full px-4 py-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" />
                            </svg>
                            <span class="text-gray-700">Manage Curriculum</span>
                        </router-link>
                        <router-link :to="`/instructor/quizzes?course=${course.id}`" 
                            class="flex items-center gap-3 w-full px-4 py-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                            </svg>
                            <span class="text-gray-700">Manage Quizzes</span>
                        </router-link>
                        <router-link :to="`/instructor/students?course=${course.id}`" 
                            class="flex items-center gap-3 w-full px-4 py-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-gray-700">View Students</span>
                        </router-link>
                        <button v-if="form.status !== 'published'" @click="publishCourse" 
                            class="flex items-center gap-3 w-full px-4 py-3 text-left bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Publish Course</span>
                        </button>
                        <button v-else @click="unpublishCourse"
                            class="flex items-center gap-3 w-full px-4 py-3 text-left bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Unpublish Course</span>
                        </button>
                    </div>
                </div>

                <div class="bg-red-50 rounded-xl border border-red-200 p-6">
                    <h2 class="text-lg font-semibold text-red-900 mb-4">Danger Zone</h2>
                    <p class="text-sm text-red-700 mb-4">Once you delete a course, there is no going back.</p>
                    <button @click="deleteCourse" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Delete Course
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { courseApi, categoryApi } from '@/services/api';

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const saving = ref(false);
const course = ref({});
const categories = ref([]);
const thumbnailPreview = ref('');

const form = reactive({
    title: '',
    short_description: '',
    description: '',
    category_id: '',
    subcategory_id: '',
    price: 0,
    discount_price: 0,
    level: 'beginner',
    is_free: false,
    is_featured: false,
    thumbnail: null,
    video_url: '',
    requirements: '',
    outcomes: '',
    language: 'en',
    status: 'draft',
});

const subcategories = computed(() => {
    if (!form.category_id) return [];
    const cat = categories.value.find(c => c.id === Number(form.category_id));
    return cat?.subcategories || [];
});

watch(() => form.is_free, (val) => {
    if (val) {
        form.price = 0;
        form.discount_price = 0;
    }
});

async function fetchCourse() {
    loading.value = true;
    try {
        const { data } = await courseApi.getById(route.params.id);
        course.value = data;
        Object.keys(form).forEach(key => {
            if (data[key] !== undefined) {
                form[key] = data[key];
            }
        });
        thumbnailPreview.value = data.thumbnail || '';
    } catch (error) {
        console.error('Error fetching course:', error);
    } finally {
        loading.value = false;
    }
}

async function fetchCategories() {
    try {
        const { data } = await categoryApi.getAll();
        categories.value = data.data || [];
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
}

function handleThumbnail(event) {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            return;
        }
        form.thumbnail = file;
        thumbnailPreview.value = URL.createObjectURL(file);
    }
}

async function saveCourse() {
    saving.value = true;
    try {
        const formData = new FormData();
        Object.keys(form).forEach(key => {
            if (key === 'thumbnail' && form[key] instanceof File) {
                formData.append(key, form[key]);
            } else if (key === 'is_free' || key === 'is_featured') {
                formData.append(key, form[key] ? '1' : '0');
            } else {
                formData.append(key, form[key]);
            }
        });
        
        await courseApi.update(route.params.id, formData);
        await fetchCourse();
        alert('Course saved successfully!');
    } catch (error) {
        console.error('Error saving course:', error);
        alert(error.response?.data?.message || 'Error saving course');
    } finally {
        saving.value = false;
    }
}

function previewCourse() {
    window.open(`/courses/${course.value.id}`, '_blank');
}

async function publishCourse() {
    form.status = 'published';
    await saveCourse();
}

async function unpublishCourse() {
    form.status = 'draft';
    await saveCourse();
}

async function deleteCourse() {
    if (confirm(`Are you sure you want to delete "${course.value.title}"? This action cannot be undone.`)) {
        try {
            await courseApi.delete(route.params.id);
            router.push('/instructor/courses');
        } catch (error) {
            console.error('Error deleting course:', error);
            alert('Error deleting course');
        }
    }
}

onMounted(() => {
    fetchCourse();
    fetchCategories();
});
</script>
