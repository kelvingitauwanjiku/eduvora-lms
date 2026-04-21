<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Customer Support</h1>
                <p class="mt-1 text-gray-500">Manage support tickets</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Open</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.open }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.pending }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">In Progress</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.in_progress }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Resolved</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.resolved }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <input v-model="search" type="text" placeholder="Search tickets..." class="w-full max-w-md px-4 py-2 border border-gray-200 rounded-lg">
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="ticket in tickets" :key="ticket.id">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ ticket.subject }}</p>
                                <p class="text-sm text-gray-500">{{ formatDate(ticket.created_at) }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-900">{{ ticket.user?.name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full"
                                :class="ticket.priority === 'high' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'">
                                {{ ticket.priority }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full"
                                :class="ticket.status === 'open' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'">
                                {{ ticket.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <router-link :to="`/admin/support/${ticket.id}`" class="text-sm text-blue-600 hover:text-blue-700">View</router-link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';

const search = ref('');
const tickets = ref([]);
const stats = reactive({ open: 0, pending: 0, in_progress: 0, resolved: 0 });

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
}

onMounted(() => {});
</script>