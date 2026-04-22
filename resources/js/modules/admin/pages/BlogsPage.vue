<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Blogs</h1>
            <p class="mt-1 text-gray-500">Manage blog posts</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="blog in blogs" :key="blog.id">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ blog.title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ blog.user?.name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full"
                                :class="blog.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                {{ blog.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button class="text-sm text-blue-600 hover:text-blue-700 mr-3">Edit</button>
                            <button class="text-sm text-red-600 hover:text-red-700">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { blogApi } from '@/services/api';

const loading = ref(true);
const blogs = ref([]);

async function fetchBlogs() {
    try {
        loading.value = true;
        const { data } = await blogApi.getAllAdmin();
        blogs.value = data.data || [];
    } catch (error) {
        console.error('Error fetching blogs:', error);
    } finally {
        loading.value = false;
    }
}

async function deleteBlog(id) {
    if (confirm('Are you sure you want to delete this blog?')) {
        try {
            await blogApi.deleteAdmin(id);
            blogs.value = blogs.value.filter(b => b.id !== id);
        } catch (error) {
            console.error('Error deleting blog:', error);
            alert('Failed to delete blog');
        }
    }
}

onMounted(() => {
    fetchBlogs();
});
</script>