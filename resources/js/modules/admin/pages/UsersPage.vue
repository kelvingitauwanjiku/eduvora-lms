<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Users</h1>
                <p class="mt-1 text-gray-500">Manage all users</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row gap-4">
                <input v-model="search" type="text" placeholder="Search users..." 
                    class="flex-1 px-4 py-2 border border-gray-200 rounded-lg">
                <select v-model="role" class="px-4 py-2 border border-gray-200 rounded-lg">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="instructor">Instructor</option>
                    <option value="student">Student</option>
                </select>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="user in users" :key="user.id">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img :src="user.avatar || '/images/avatar-placeholder.jpg'" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="font-medium text-gray-900">{{ user.name }}</p>
                                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <select v-model="user.role" @change="updateRole(user)" 
                                class="text-sm border-none bg-transparent">
                                <option value="student">Student</option>
                                <option value="instructor">Instructor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ formatDate(user.created_at) }}
                        </td>
                        <td class="px-6 py-4">
                            <button @click="deleteUser(user.id)" class="text-sm text-red-600 hover:text-red-700">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const search = ref('');
const role = ref('');
const users = ref([]);

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

async function fetchUsers() {
    users.value = [];
}

async function updateRole(user) {
    console.log('Update role:', user.id, user.role);
}

async function deleteUser(id) {
    if (confirm('Delete this user?')) {
        console.log('Delete user:', id);
    }
}

onMounted(() => {
    fetchUsers();
});
</script>