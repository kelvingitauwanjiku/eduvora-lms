<template>
    <div>
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-4xl font-bold text-white mb-4">Frequently Asked Questions</h1>
                <p class="text-xl text-blue-100">Find answers to common questions about Eduvora</p>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Category Tabs -->
            <div class="flex gap-2 mb-8 overflow-x-auto pb-2">
                <button v-for="cat in categories" :key="cat" @click="selectedCategory = cat"
                    :class="selectedCategory === cat ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    class="px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap">
                    {{ cat }}
                </button>
            </div>

            <div class="space-y-4">
                <div v-for="(faq, index) in filteredFaqs" :key="index" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button @click="toggleFaq(index)" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                        <span class="font-medium text-gray-900 pr-4">{{ faq.question }}</span>
                        <svg class="w-5 h-5 text-gray-500 shrink-0 transition-transform" :class="{ 'rotate-180': openIndex === index }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div v-if="openIndex === index" class="px-6 pb-4">
                        <p class="text-gray-600 leading-relaxed">{{ faq.answer }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-12 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Still have questions?</h2>
                <p class="text-gray-600 mb-6">Can't find the answer you're looking for? Our support team is here to help.</p>
                <router-link to="/contact" class="inline-block px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
                    Contact Support
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const openIndex = ref(null);
const selectedCategory = ref('All');

const categories = ['All', 'Getting Started', 'Payments', 'Courses', 'Certificates', 'Instructors'];

const faqs = ref([
    {
        category: 'Getting Started',
        question: 'How do I create an account?',
        answer: 'Click the "Sign Up" button in the top right corner of the page. Fill in your details (name, email, password) or sign up with Google or Facebook. Verify your email address and you\'re ready to start learning!'
    },
    {
        category: 'Getting Started',
        question: 'How do I enroll in a course?',
        answer: 'Browse our course catalog, select the course you want to enroll in, and click the "Enroll" button. You can pay using any available payment method or enroll for free if the course is free. After enrollment, the course will be available in your dashboard.'
    },
    {
        category: 'Getting Started',
        question: 'Can I preview courses before enrolling?',
        answer: 'Yes! Most courses offer free preview lessons. Look for the "Preview" button on the course page to watch sample videos before making a purchase decision.'
    },
    {
        category: 'Payments',
        question: 'What payment methods do you accept?',
        answer: 'We accept all major credit cards (Visa, Mastercard, American Express), PayPal, and in some regions, we offer bank transfer options. All transactions are secure and encrypted.'
    },
    {
        category: 'Payments',
        question: 'Can I get a refund?',
        answer: 'Yes, we offer a 30-day money-back guarantee for most courses. If you\'re not satisfied with your purchase, contact our support team within 30 days for a full refund. Some restrictions may apply for subscription-based content.'
    },
    {
        category: 'Payments',
        question: 'Do you offer any discounts or coupons?',
        answer: 'We regularly run promotions and offer discount codes. Sign up for our newsletter to receive notifications about special offers. You can also check our deals page for current discounts.'
    },
    {
        category: 'Payments',
        question: 'What is the subscription plan?',
        answer: 'Our subscription plan gives you unlimited access to all courses for a monthly or annual fee. It\'s perfect if you want to learn multiple topics without purchasing courses individually.'
    },
    {
        category: 'Courses',
        question: 'How do I access my purchased courses?',
        answer: 'After purchasing a course, go to your Dashboard and click on "My Courses" to access all your enrolled courses. You can start learning immediately from any device.'
    },
    {
        category: 'Courses',
        question: 'Can I download courses for offline viewing?',
        answer: 'Some courses offer downloadable content. Look for the download icon on the course page to see if offline viewing is available. Mobile app users can also download videos for offline access.'
    },
    {
        category: 'Courses',
        question: 'How long do I have access to a course?',
        answer: 'For one-time purchases, you get lifetime access to the course. This includes all future updates to the course content. Subscribers have access as long as their subscription is active.'
    },
    {
        category: 'Courses',
        question: 'Are there any prerequisites for courses?',
        answer: 'Most beginner courses have no prerequisites. Intermediate and advanced courses may list recommended prerequisites in their description. We also offer learning paths to help you build skills progressively.'
    },
    {
        category: 'Certificates',
        question: 'Do I get a certificate after completing a course?',
        answer: 'Yes, upon completing a course, you\'ll receive a certificate of completion that you can download or share on LinkedIn. Some courses also offer verified certificates with ID verification.'
    },
    {
        category: 'Certificates',
        question: 'Are certificates recognized by employers?',
        answer: 'Our certificates are recognized by many employers worldwide. For professional certifications, we partner with industry leaders to offer credentials that hold significant weight in the job market.'
    },
    {
        category: 'Instructors',
        question: 'How do I become an instructor?',
        answer: 'To become an instructor, register for an instructor account and apply through our instructor portal. You\'ll need to provide information about your expertise, teaching experience, and submit a sample course for review.'
    },
    {
        category: 'Instructors',
        question: 'How much can I earn as an instructor?',
        answer: 'Instructors earn revenue based on course sales. We offer competitive revenue share rates, typically starting at 50% and increasing based on performance. Top instructors can earn significant income through our platform.'
    },
    {
        category: 'Instructors',
        question: 'What tools are available for instructors?',
        answer: 'Instructors have access to our comprehensive teaching platform including course creation tools, quiz builders, assignment features, analytics dashboard, student communication tools, and promotional resources.'
    }
]);

const filteredFaqs = computed(() => {
    if (selectedCategory.value === 'All') return faqs.value;
    return faqs.value.filter(faq => faq.category === selectedCategory.value);
});

function toggleFaq(index) {
    openIndex.value = openIndex.value === index ? null : index;
}
</script>