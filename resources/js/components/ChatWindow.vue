<template>
    <div class="bg-white rounded-xl border border-gray-200 flex flex-col h-[600px]">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <span class="text-blue-600 font-medium">{{ user?.name?.[0] || 'U' }}</span>
                </div>
                <div>
                    <h3 class="font-medium">{{ user?.name || 'User' }}</h3>
                    <p class="text-xs text-gray-500">{{ online ? 'Online' : 'Offline' }}</p>
                </div>
            </div>
        </div>
        
        <div ref="messagesRef" class="flex-1 overflow-y-auto p-4 space-y-4">
            <div v-for="msg in messages" :key="msg.id" :class="['flex', msg.sender_id === currentUserId ? 'justify-end' : 'justify-start']">
                <div :class="['max-w-[70%] rounded-2xl px-4 py-2', msg.sender_id === currentUserId ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900']">
                    <p>{{ msg.message }}</p>
                    <p :class="['text-xs mt-1', msg.sender_id === currentUserId ? 'text-blue-200' : 'text-gray-500']">{{ formatTime(msg.created_at) }}</p>
                </div>
            </div>
        </div>
        
        <div class="p-4 border-t border-gray-200">
            <form @submit.prevent="send" class="flex gap-2">
                <input v-model="newMessage" type="text" placeholder="Type a message..." 
                    class="flex-1 px-4 py-2 border border-gray-200 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick, onMounted, watch } from 'vue';

const props = defineProps({
    user: Object,
    currentUserId: [String, Number],
    receiverId: [String, Number],
    initialMessages: { type: Array, default: () => [] }
});

const emit = defineEmits(['message']);
const messages = ref(props.initialMessages);
const newMessage = ref('');
const messagesRef = ref(null);
const online = ref(false);

function formatTime(date) {
    return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

async function send() {
    if (!newMessage.value.trim()) return;
    const msg = {
        id: Date.now(),
        message: newMessage.value,
        sender_id: props.currentUserId,
        receiver_id: props.receiverId,
        created_at: new Date().toISOString()
    };
    messages.value.push(msg);
    newMessage.value = '';
    emit('message', msg);
    await nextTick();
    scrollToBottom();
}

function scrollToBottom() {
    if (messagesRef.value) {
        messagesRef.value.scrollTop = messagesRef.value.scrollHeight;
    }
}

watch(() => props.initialMessages, (val) => {
    messages.value = val;
    nextTick(scrollToBottom);
}, { deep: true });

onMounted(() => { scrollToBottom(); });
</script>