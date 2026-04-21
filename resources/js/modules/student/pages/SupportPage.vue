<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Support</h1>
            <p class="mt-1 text-gray-500">Get help with your learning experience</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Submit a Ticket</h2>
                    <form @submit.prevent="submitTicket" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input v-model="form.subject" type="text" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select v-model="form.category" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <option value="">Select category</option>
                                <option value="technical">Technical Issue</option>
                                <option value="payment">Payment Issue</option>
                                <option value="course">Course Content</option>
                                <option value="certificate">Certificate</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea v-model="form.message" rows="5" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        <button type="submit" :disabled="loading" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition">
                            {{ loading ? 'Sending...' : 'Submit Ticket' }}
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">My Tickets</h2>
                    
                    <div v-if="loadingTickets" class="space-y-4">
                        <div v-for="i in 3" :key="i" class="animate-pulse border border-gray-200 rounded-xl p-4">
                            <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                        </div>
                    </div>

                    <div v-else-if="tickets.length === 0" class="text-center py-8">
                        <p class="text-gray-500">No tickets submitted yet</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="ticket in tickets" :key="ticket.id" class="border border-gray-200 rounded-xl p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-medium text-gray-900">{{ ticket.subject }}</h3>
                                        <span class="px-2 py-0.5 text-xs rounded-full"
                                            :class="{
                                                'bg-yellow-100 text-yellow-700': ticket.status === 'open',
                                                'bg-blue-100 text-blue-700': ticket.status === 'in_progress',
                                                'bg-green-100 text-green-700': ticket.status === 'closed'
                                            }">
                                            {{ ticket.status || 'open' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500 line-clamp-2">{{ ticket.message }}</p>
                                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                                        <span>{{ formatDate(ticket.created_at) }}</span>
                                        <span>{{ ticket.category }}</span>
                                    </div>
                                </div>
                                <button @click="viewTicket(ticket)" class="ml-4 text-blue-600 hover:text-blue-700 text-sm">
                                    View
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">FAQ</h2>
                <div class="space-y-4">
                    <div v-for="faq in faqs" :key="faq.question" class="border-b border-gray-100 pb-4">
                        <button @click="toggleFaq(faq.question)" class="w-full text-left flex items-center justify-between">
                            <span class="font-medium text-gray-900 text-sm">{{ faq.question }}</span>
                            <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === faq.question }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <p v-if="openFaq === faq.question" class="mt-2 text-sm text-gray-600">{{ faq.answer }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { supportApi } from '@/services/api';

const loading = ref(false);
const loadingTickets = ref(false);
const openFaq = ref(null);
const tickets = ref([]);

const form = reactive({
    subject: '',
    category: '',
    message: '',
});

const faqs = ref([
    { question: 'How do I reset my progress?', answer: 'Go to the course player and click on the reset progress button in the settings menu.' },
    { question: 'Can I get a refund?', answer: 'Yes, contact support within 30 days of purchase for a full refund.' },
    { question: 'How do I download my certificate?', answer: 'Go to Certificates page and click the download button next to any completed course.' },
    { question: 'How do I contact an instructor?', answer: 'You can use the Messages feature to contact your instructors directly.' },
]);

function toggleFaq(question) {
    openFaq.value = openFaq.value === question ? null : question;
}

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

async function fetchTickets() {
    loadingTickets.value = true;
    try {
        const { data } = await supportApi.getTickets();
        tickets.value = data.data || [];
    } catch (error) {
        console.error('Error fetching tickets:', error);
    } finally {
        loadingTickets.value = false;
    }
}

async function submitTicket() {
    loading.value = true;
    try {
        await supportApi.createTicket(form);
        form.subject = '';
        form.category = '';
        form.message = '';
        await fetchTickets();
    } catch (error) {
        console.error('Error submitting ticket:', error);
    } finally {
        loading.value = false;
    }
}

function viewTicket(ticket) {
    console.log('View ticket:', ticket);
}

onMounted(() => {
    fetchTickets();
});
</script>