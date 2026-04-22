<template>
    <footer class="bg-gray-900 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12">
                <!-- Brand & Contact -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                            <span class="text-white font-bold text-lg">E</span>
                        </div>
                        <span class="text-xl font-bold text-white">Eduvora</span>
                    </div>
                    <p class="text-sm text-gray-400 mb-6 leading-relaxed">
                        Empowering learners worldwide with quality education. Start your journey to success today.
                    </p>
                    <div class="space-y-3 mb-6">
                        <a v-if="phone" :href="`tel:${phone}`" class="flex items-center gap-3 text-sm hover:text-white transition">
                            <span class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </span>
                            {{ phone }}
                        </a>
                        <a v-if="email" :href="`mailto:${email}`" class="flex items-center gap-3 text-sm hover:text-white transition">
                            <span class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            {{ email }}
                        </a>
                    </div>
                    <div class="flex gap-3">
                        <a v-for="social in socials" :key="social.name" :href="social.url" target="_blank"
                           class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-lg hover:bg-blue-600 transition">
                            <component :is="social.icon" class="w-5 h-5" />
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-white font-semibold mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><router-link to="/courses" class="text-sm hover:text-white transition">All Courses</router-link></li>
                        <li><router-link to="/instructors" class="text-sm hover:text-white transition">Instructors</router-link></li>
                        <li><router-link to="/blog" class="text-sm hover:text-white transition">Blog</router-link></li>
                        <li><router-link to="/faq" class="text-sm hover:text-white transition">FAQ</router-link></li>
                        <li><router-link to="/contact" class="text-sm hover:text-white transition">Contact Us</router-link></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="text-white font-semibold mb-6">Support</h4>
                    <ul class="space-y-3">
                        <li><router-link to="/about" class="text-sm hover:text-white transition">About Us</router-link></li>
                        <li><router-link to="/help" class="text-sm hover:text-white transition">Help Center</router-link></li>
                        <li><router-link to="/terms" class="text-sm hover:text-white transition">Terms of Service</router-link></li>
                        <li><router-link to="/privacy" class="text-sm hover:text-white transition">Privacy Policy</router-link></li>
                        <li><router-link to="/cookies" class="text-sm hover:text-white transition">Cookie Policy</router-link></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h4 class="text-white font-semibold mb-6">Newsletter</h4>
                    <p class="text-sm text-gray-400 mb-4">Subscribe to get updates on new courses.</p>
                    <form @submit.prevent="handleSubscribe" class="space-y-3">
                        <input 
                            v-model="email"
                            type="email" 
                            placeholder="Enter your email"
                            class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 transition"
                        >
                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg hover:shadow-lg hover:shadow-blue-600/20 transition">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-500">&copy; {{ currentYear }} Eduvora. All rights reserved.</p>
                <div class="flex gap-6">
                    <router-link to="/privacy" class="text-sm text-gray-500 hover:text-white transition">Privacy</router-link>
                    <router-link to="/terms" class="text-sm text-gray-500 hover:text-white transition">Terms</router-link>
                    <router-link to="/cookies" class="text-sm text-gray-500 hover:text-white transition">Cookies</router-link>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { ref, computed, h } from 'vue';

defineProps({
    phone: {
        type: String,
        default: ''
    },
    email: {
        type: String,
        default: ''
    }
});

const email = ref('');
const currentYear = computed(() => new Date().getFullYear());

const socials = [
    { name: 'Facebook', url: '#', icon: { render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { d: 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z' })]) } },
    { name: 'Twitter', url: '#', icon: { render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { d: 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z' })]) } },
    { name: 'Instagram', url: '#', icon: { render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { d: 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z' })]) } },
    { name: 'LinkedIn', url: '#', icon: { render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { d: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z' })]) } },
    { name: 'YouTube', url: '#', icon: { render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { d: 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z' })]) } },
];

function handleSubscribe() {
    if (!import.meta?.env?.PROD) console.log('Subscribe:', email.value);
    email.value = '';
}
</script>