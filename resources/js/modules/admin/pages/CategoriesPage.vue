<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
                <p class="mt-1 text-gray-500">Manage your course categories</p>
            </div>
            <button @click="openModal('create')" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm font-semibold hover:from-blue-700 hover:to-indigo-700 transition shadow-lg shadow-blue-600/20 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Category
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input v-model="search" type="text" placeholder="Search categories..." class="w-full pl-12 pr-4 py-2.5 bg-gray-50 border-0 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex gap-3">
                    <select v-model="filterStatus" class="px-4 py-2.5 bg-gray-50 border-0 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <select v-model="sortBy" class="px-4 py-2.5 bg-gray-50 border-0 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="name">Name A-Z</option>
                        <option value="courses">Most Courses</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="category in filteredCategories" :key="category.id" 
                 class="group bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-lg hover:border-blue-100 transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center" :style="`background-color: ${category.color}20`">
                        <span class="text-2xl">{{ category.icon }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="openModal('edit', category)" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button @click="confirmDelete(category)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ category.name }}</h3>
                    <p class="text-sm text-gray-500 line-clamp-2">{{ category.description || 'No description' }}</p>
                </div>
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            {{ category.courses_count || 0 }} courses
                        </span>
                    </div>
                    <button @click="toggleStatus(category)" 
                            class="px-3 py-1 text-xs font-semibold rounded-full transition"
                            :class="category.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        {{ category.is_active ? 'Active' : 'Inactive' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredCategories.length === 0" class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No categories found</h3>
            <p class="text-gray-500 mb-6">Get started by creating your first category</p>
            <button @click="openModal('create')" class="px-6 py-3 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition">
                Create Category
            </button>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="modalOpen" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-screen items-center justify-center p-4">
                    <div class="fixed inset-0 bg-black/50 transition-opacity" @click="closeModal"></div>
                    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900">{{ modalMode === 'create' ? 'Create Category' : 'Edit Category' }}</h3>
                        </div>
                        <form @submit.prevent="saveCategory" class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Category Name *</label>
                                <input v-model="form.name" type="text" required placeholder="Enter category name" 
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                                <textarea v-model="form.description" rows="3" placeholder="Enter description" 
                                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Icon (emoji)</label>
                                    <input v-model="form.icon" type="text" placeholder="e.g. 💻" 
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Color</label>
                                    <div class="flex items-center gap-2">
                                        <input v-model="form.color" type="color" 
                                               class="w-10 h-10 border border-gray-200 rounded-lg cursor-pointer">
                                        <input v-model="form.color" type="text" placeholder="#3b82f6" 
                                               class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Slug</label>
                                <input v-model="form.slug" type="text" placeholder="auto-generated-from-name" 
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Parent Category</label>
                                <select v-model="form.parent_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">None (Top Level)</option>
                                    <option v-for="cat in parentCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-3">
                                <input v-model="form.is_active" type="checkbox" id="is_active" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                <label for="is_active" class="text-sm text-gray-700">Active</label>
                            </div>
                            <div class="flex justify-end gap-3 pt-4">
                                <button type="button" @click="closeModal" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                                    Cancel
                                </button>
                                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition">
                                    {{ modalMode === 'create' ? 'Create' : 'Update' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Delete Confirmation -->
        <Teleport to="body">
            <div v-if="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-screen items-center justify-center p-4">
                    <div class="fixed inset-0 bg-black/50 transition-opacity" @click="deleteModalOpen = false"></div>
                    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
                        <div class="p-6 text-center">
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Category</h3>
                            <p class="text-gray-500 mb-6">Are you sure you want to delete "{{ categoryToDelete?.name }}"? This action cannot be undone.</p>
                            <div class="flex justify-center gap-3">
                                <button @click="deleteModalOpen = false" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                                    Cancel
                                </button>
                                <button @click="deleteCategory" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { categoryApi } from '@/services/api';

const search = ref('');
const filterStatus = ref('');
const sortBy = ref('newest');
const modalOpen = ref(false);
const modalMode = ref('create');
const deleteModalOpen = ref(false);
const categoryToDelete = ref(null);
const loading = ref(false);

const categories = ref([]);

async function fetchCategories() {
    loading.value = true;
    try {
        const { data } = await categoryApi.getAll();
        categories.value = data.data || data;
    } catch (err) {
        console.error('Failed to load categories:', err);
    } finally {
        loading.value = false;
    }

    // Default dummy data if API fails
    if (categories.value.length === 0) {
        categories.value = [
            { id: 1, name: 'Web Development', description: 'Learn frontend, backend, and full-stack web development', icon: '💻', color: '#3b82f6', courses_count: 45, is_active: true, parent_id: null, slug: 'web-development' },
            { id: 2, name: 'Data Science', description: 'Master data analysis, machine learning, and AI', icon: '📊', color: '#10b981', courses_count: 38, is_active: true, parent_id: null, slug: 'data-science' },
            { id: 3, name: 'Mobile Development', description: 'Build iOS and Android apps with modern frameworks', icon: '📱', color: '#8b5cf6', courses_count: 28, is_active: true, parent_id: null, slug: 'mobile-development' },
            { id: 4, name: 'Cloud Computing', description: 'AWS, Azure, Google Cloud and DevOps practices', icon: '☁️', color: '#06b6d4', courses_count: 22, is_active: true, parent_id: null, slug: 'cloud-computing' },
            { id: 5, name: 'UI/UX Design', description: 'Create beautiful and user-friendly interfaces', icon: '🎨', color: '#f59e0b', courses_count: 35, is_active: true, parent_id: null, slug: 'ui-ux-design' },
            { id: 6, name: 'Business & Management', description: 'Entrepreneurship, marketing, and leadership', icon: '💼', color: '#ec4899', courses_count: 42, is_active: true, parent_id: null, slug: 'business-management' },
            { id: 7, name: 'Cybersecurity', description: 'Network security, ethical hacking, and protection', icon: '🔒', color: '#ef4444', courses_count: 18, is_active: false, parent_id: null, slug: 'cybersecurity' },
            { id: 8, name: 'Artificial Intelligence', description: 'Deep learning, NLP, and AI applications', icon: '🤖', color: '#6366f1', courses_count: 25, is_active: true, parent_id: null, slug: 'artificial-intelligence' },
        ];
    }
}

fetchCategories();

const form = ref({
    id: null,
    name: '',
    description: '',
    icon: '📚',
    color: '#3b82f6',
    slug: '',
    parent_id: '',
    is_active: true,
});

const filteredCategories = computed(() => {
    let result = [...categories.value];
    
    if (search.value) {
        const query = search.value.toLowerCase();
        result = result.filter(c => c.name.toLowerCase().includes(query) || c.description?.toLowerCase().includes(query));
    }
    
    if (filterStatus.value) {
        result = result.filter(c => filterStatus.value === 'active' ? c.is_active : !c.is_active);
    }
    
    switch (sortBy.value) {
        case 'oldest':
            result.sort((a, b) => a.id - b.id);
            break;
        case 'name':
            result.sort((a, b) => a.name.localeCompare(b.name));
            break;
        case 'courses':
            result.sort((a, b) => (b.courses_count || 0) - (a.courses_count || 0));
            break;
        default:
            result.sort((a, b) => b.id - a.id);
    }
    
    return result;
});

const parentCategories = computed(() => {
    return categories.value.filter(c => !c.parent_id);
});

function openModal(mode, category = null) {
    modalMode.value = mode;
    if (mode === 'edit' && category) {
        form.value = { ...category };
    } else {
        form.value = {
            id: null,
            name: '',
            description: '',
            icon: '📚',
            color: '#3b82f6',
            slug: '',
            parent_id: '',
            is_active: true,
        };
    }
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
}

function saveCategory() {
    if (modalMode.value === 'create') {
        const newCategory = {
            ...form.value,
            id: Math.max(...categories.value.map(c => c.id)) + 1,
            courses_count: 0,
            slug: form.value.slug || form.value.name.toLowerCase().replace(/\s+/g, '-'),
        };
        categories.value.unshift(newCategory);
    } else {
        const index = categories.value.findIndex(c => c.id === form.value.id);
        if (index !== -1) {
            categories.value[index] = {
                ...categories.value[index],
                ...form.value,
                slug: form.value.slug || form.value.name.toLowerCase().replace(/\s+/g, '-'),
            };
        }
    }
    closeModal();
}

function confirmDelete(category) {
    categoryToDelete.value = category;
    deleteModalOpen.value = true;
}

function deleteCategory() {
    if (categoryToDelete.value) {
        categories.value = categories.value.filter(c => c.id !== categoryToDelete.value.id);
    }
    deleteModalOpen.value = false;
    categoryToDelete.value = null;
}

function toggleStatus(category) {
    category.is_active = !category.is_active;
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
