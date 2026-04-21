<template>
    <div>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Notice Board</h1>
                <p class="mt-1 text-gray-500">Post announcements to your students</p>
            </div>
            <button @click="showCreateModal = true" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Notice
            </button>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="p-4 border-b border-gray-200 flex flex-wrap items-center gap-4">
                <select v-model="courseFilter" class="px-4 py-2 border border-gray-200 rounded-lg">
                    <option value="">All Courses</option>
                    <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
                </select>
                <select v-model="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
        </div>

        <div v-if="loading" class="space-y-4">
            <div v-for="i in 5" :key="i" class="animate-pulse bg-white rounded-xl border border-gray-200 p-6">
                <div class="h-5 bg-gray-200 rounded w-3/4 mb-4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>

        <div v-else-if="notices.length === 0" class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No notices yet</h3>
            <p class="text-gray-500 mt-1">Post your first announcement to students</p>
            <button @click="showCreateModal = true" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Post Notice
            </button>
        </div>

        <div v-else class="space-y-4">
            <div v-for="notice in filteredNotices" :key="notice.id" 
                class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-lg">{{ notice.title }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ notice.course?.title || 'All Courses' }} • {{ formatDate(notice.created_at) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full"
                            :class="notice.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            {{ notice.status || 'draft' }}
                        </span>
                        <button @click="toggleMenu(notice.id)" class="p-1 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="prose max-w-none text-gray-600 mb-4">
                    {{ notice.content }}
                </div>
                
                <div v-if="notice.is_pinned" class="inline-flex items-center gap-1 text-xs text-blue-600 font-medium mb-4">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4H5zm0 4h6v4H5V9zm0 4h6v2H5v-2z" />
                    </svg>
                    Pinned
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span>{{ notice.views || 0 }} views</span>
                        <span>Last updated: {{ formatDate(notice.updated_at) }}</span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="editNotice(notice)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-700 hover:bg-gray-50 rounded-lg">
                            Edit
                        </button>
                        <button @click="togglePinned(notice)" 
                            class="px-3 py-1 text-sm text-gray-600 hover:text-gray-700 hover:bg-gray-50 rounded-lg">
                            {{ notice.is_pinned ? 'Unpin' : 'Pin' }}
                        </button>
                        <button @click="deleteNotice(notice)" class="px-3 py-1 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showCreateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
            <div class="bg-white rounded-xl w-full max-w-2xl max-h-[80vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
                    <h3 class="text-lg font-semibold text-gray-900">{{ editingNotice ? 'Edit Notice' : 'Post New Notice' }}</h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form @submit.prevent="saveNotice" class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input v-model="form.title" type="text" required placeholder="Notice title"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                        <select v-model="form.course_id" 
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Courses</option>
                            <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Leave empty to notify all students</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Content *</label>
                        <textarea v-model="form.content" rows="6" required placeholder="Write your notice content..."
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2">
                            <input v-model="form.is_pinned" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm text-gray-700">Pin this notice</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input v-model="form.send_email" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm text-gray-700">Send email notification</span>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="closeModal" class="px-6 py-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" :disabled="saving" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                            {{ saving ? 'Saving...' : (form.status === 'published' ? 'Publish Notice' : 'Save Draft') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { noticeApi, courseApi } from '@/services/api';

const loading = ref(true);
const notices = ref([]);
const courses = ref([]);
const showCreateModal = ref(false);
const editingNotice = ref(null);
const activeMenu = ref(null);
const saving = ref(false);
const courseFilter = ref('');
const statusFilter = ref('');

const form = reactive({
    title: '',
    course_id: '',
    content: '',
    status: 'draft',
    is_pinned: false,
    send_email: false,
});

const filteredNotices = computed(() => {
    let result = [...notices.value];
    
    if (courseFilter.value) {
        result = result.filter(n => n.course_id === Number(courseFilter.value));
    }
    
    if (statusFilter.value) {
        result = result.filter(n => n.status === statusFilter.value);
    }
    
    return result;
});

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { 
        month: 'short', day: 'numeric', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

function toggleMenu(id) {
    activeMenu.value = activeMenu.value === id ? null : id;
}

function closeModal() {
    showCreateModal.value = false;
    editingNotice.value = null;
    Object.assign(form, {
        title: '',
        course_id: '',
        content: '',
        status: 'draft',
        is_pinned: false,
        send_email: false,
    });
}

function editNotice(notice) {
    editingNotice.value = notice;
    Object.assign(form, {
        title: notice.title,
        course_id: notice.course_id,
        content: notice.content,
        status: notice.status,
        is_pinned: notice.is_pinned,
        send_email: false,
    });
    showCreateModal.value = true;
    activeMenu.value = null;
}

async function saveNotice() {
    saving.value = true;
    try {
        if (editingNotice.value) {
            await noticeApi.update(editingNotice.value.id, form);
        } else {
            await noticeApi.create(form);
        }
        closeModal();
        fetchNotices();
    } catch (error) {
        console.error('Error saving notice:', error);
    } finally {
        saving.value = false;
    }
}

async function togglePinned(notice) {
    activeMenu.value = null;
    try {
        await noticeApi.update(notice.id, { is_pinned: !notice.is_pinned });
        fetchNotices();
    } catch (error) {
        console.error('Error toggling pin:', error);
    }
}

async function deleteNotice(notice) {
    activeMenu.value = null;
    if (confirm(`Are you sure you want to delete "${notice.title}"?`)) {
        try {
            await noticeApi.delete(notice.id);
            fetchNotices();
        } catch (error) {
            console.error('Error deleting notice:', error);
        }
    }
}

async function fetchNotices() {
    loading.value = true;
    try {
        const { data } = await noticeApi.getAll({ per_page: 100 });
        notices.value = data.data || [];
    } catch (error) {
        console.error('Error fetching notices:', error);
    } finally {
        loading.value = false;
    }
}

async function fetchCourses() {
    try {
        const { data } = await courseApi.getAll({ per_page: 100 });
        courses.value = data.data || [];
    } catch (error) {
        console.error('Error fetching courses:', error);
    }
}

onMounted(() => {
    fetchNotices();
    fetchCourses();
});
</script>