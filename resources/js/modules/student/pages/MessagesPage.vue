<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Messages</h1>
            <p class="mt-1 text-gray-500">Chat with instructors and support</p>
        </div>

        <div v-if="loading" class="animate-pulse">
            <div class="h-96 bg-gray-200 rounded-xl"></div>
        </div>

        <div v-else class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 min-h-[600px]">
                <div class="border-r border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="font-semibold">Conversations</h2>
                    </div>
                    
                    <div v-if="conversations.length === 0" class="p-4 text-center text-gray-500">
                        <p>No conversations yet</p>
                    </div>

                    <div v-else class="divide-y divide-gray-100">
                        <button v-for="conv in conversations" :key="conv.id" @click="selectConversation(conv)"
                            class="w-full p-4 text-left hover:bg-gray-50 transition"
                            :class="selectedConversation?.id === conv.id ? 'bg-blue-50' : ''">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <img v-if="conv.user?.avatar" :src="conv.user.avatar" class="w-10 h-10 rounded-full object-cover">
                                    <span v-else class="text-sm font-medium text-gray-600">{{ conv.user?.name?.charAt(0) || 'U' }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="font-medium text-gray-900 truncate">{{ conv.user?.name || 'User' }}</p>
                                        <span v-if="conv.last_message" class="text-xs text-gray-400">{{ formatTime(conv.last_message.created_at) }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate">{{ conv.last_message?.message || 'No messages' }}</p>
                                </div>
                                <span v-if="conv.unread > 0" class="w-5 h-5 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center">
                                    {{ conv.unread }}
                                </span>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="md:col-span-2 flex flex-col">
                    <div v-if="!selectedConversation" class="flex-1 flex items-center justify-center text-gray-500">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p>Select a conversation to start messaging</p>
                        </div>
                    </div>

                    <template v-else>
                        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <img v-if="selectedConversation.user?.avatar" :src="selectedConversation.user.avatar" class="w-10 h-10 rounded-full object-cover">
                                    <span v-else class="text-sm font-medium text-gray-600">{{ selectedConversation.user?.name?.charAt(0) || 'U' }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ selectedConversation.user?.name }}</p>
                                    <p class="text-xs text-gray-500">{{ selectedConversation.user?.role }}</p>
                                </div>
                            </div>
                        </div>

                        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
                            <div v-for="msg in messages" :key="msg.id"
                                class="flex"
                                :class="msg.sender_id === currentUserId ? 'justify-end' : 'justify-start'">
                                <div class="max-w-[70%] rounded-xl px-4 py-2"
                                    :class="msg.sender_id === currentUserId ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900'">
                                    <p class="text-sm">{{ msg.message }}</p>
                                    <p class="text-xs mt-1" :class="msg.sender_id === currentUserId ? 'text-blue-200' : 'text-gray-400'">
                                        {{ formatTime(msg.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 border-t border-gray-200">
                            <form @submit.prevent="sendMessage" class="flex gap-2">
                                <input v-model="newMessage" type="text" placeholder="Type a message..."
                                    class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button type="submit" :disabled="!newMessage.trim()"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useAuthStore } from '@/stores/auth';
import api from '@/services/api';

const authStore = useAuthStore();

const loading = ref(true);
const conversations = ref([]);
const selectedConversation = ref(null);
const messages = ref([]);
const newMessage = ref('');
const messagesContainer = ref(null);

const currentUserId = ref(null);

async function fetchConversations() {
    loading.value = true;
    try {
        const { data } = await api.get('/messages/conversations');
        conversations.value = data.data || [];
    } catch (error) {
        console.error('Error fetching conversations:', error);
    } finally {
        loading.value = false;
    }
}

async function selectConversation(conv) {
    selectedConversation.value = conv;
    await fetchMessages(conv.id);
}

async function fetchMessages(conversationId) {
    try {
        const { data } = await api.get(`/messages/conversation/${conversationId}`);
        messages.value = data.data || [];
        await nextTick();
        scrollToBottom();
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
}

async function sendMessage() {
    if (!newMessage.value.trim() || !selectedConversation.value) return;
    
    try {
        const { data } = await api.post('/messages/send', {
            receiver_id: selectedConversation.value.user_id,
            message: newMessage.value
        });
        messages.value.push(data);
        newMessage.value = '';
        await nextTick();
        scrollToBottom();
    } catch (error) {
        console.error('Error sending message:', error);
    }
}

function scrollToBottom() {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
}

function formatTime(date) {
    if (!date) return '';
    const d = new Date(date);
    const now = new Date();
    const diff = now - d;
    
    if (diff < 60000) return 'Just now';
    if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
    if (diff < 86400000) return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

onMounted(() => {
    currentUserId.value = authStore.user?.id;
    fetchConversations();
});
</script>