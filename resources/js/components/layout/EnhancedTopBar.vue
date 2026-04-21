<template>
    <!-- Sub-header style (Academy-LMS parity) + glass, i18n dropdown, mobile sheet, scroll-compact -->
    <div
        class="enhanced-topbar border-b border-gray-200/60 dark:border-gray-700/60 bg-white/85 dark:bg-gray-900/85 backdrop-blur-md sticky top-0 z-50 transition-[padding,box-shadow] duration-300"
        :class="isCompact ? 'py-1 shadow-sm' : 'py-2'"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-y-2 gap-x-3 lg:gap-6">
                <!-- Left: phone + address (+ email when space) — mirrors LMS `sub-header-left` -->
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 lg:gap-6 min-w-0 flex-1">
                    <a
                        v-if="config.phone"
                        :href="`tel:${config.phone}`"
                        class="group flex items-center gap-2 text-xs lg:text-sm text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors min-w-0"
                        :title="`Call us: ${config.phone}`"
                    >
                        <div class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/30 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 shrink-0">
                            <svg class="w-3.5 h-3.5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <span class="font-medium truncate">{{ config.phone }}</span>
                    </a>

                    <component
                        :is="config.mapsUrl ? 'a' : 'span'"
                        v-if="config.address && config.features.showAddress && !(isCompact && config.features.enableScrollCollapse)"
                        :href="config.mapsUrl || undefined"
                        :target="config.mapsUrl ? '_blank' : undefined"
                        :rel="config.mapsUrl ? 'noopener noreferrer' : undefined"
                        class="group flex items-center gap-2 text-xs lg:text-sm text-gray-700 dark:text-gray-200 min-w-0 max-w-md"
                        :class="config.mapsUrl ? 'hover:text-blue-600 dark:hover:text-blue-400 transition-colors' : ''"
                    >
                        <div class="p-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/25 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/40 shrink-0">
                            <svg class="w-3.5 h-3.5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="font-medium truncate" :title="config.address">{{ config.address }}</span>
                    </component>

                    <a
                        v-if="config.email && !(isCompact && config.features.enableScrollCollapse)"
                        :href="`mailto:${config.email}`"
                        class="group hidden md:flex items-center gap-2 text-xs lg:text-sm text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors min-w-0 max-w-xs"
                        :title="`Email us: ${config.email}`"
                    >
                        <div class="p-1.5 rounded-lg bg-purple-50 dark:bg-purple-900/30 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 shrink-0">
                            <svg class="w-3.5 h-3.5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="font-medium truncate">{{ config.email }}</span>
                    </a>
                </div>

                <!-- Center: language (desktop) — nicer than LMS `<select>` + nice-select -->
                <div
                    v-if="config.features.allowAnonymousLanguageSwitch"
                    class="hidden lg:flex items-center shrink-0"
                    :class="{ 'lg:hidden': isCompact && config.features.enableScrollCollapse }"
                >
                    <div ref="langDropdownRoot" class="relative">
                        <button
                            type="button"
                            id="topbar-lang-button"
                            @click="toggleLanguageDropdown"
                            class="group flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-700"
                            aria-haspopup="listbox"
                            :aria-expanded="isLangOpen"
                            aria-controls="topbar-lang-listbox"
                        >
                            <span class="text-lg" :key="currentLanguage">{{ getLanguageFlag(currentLanguage) }}</span>
                            <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ currentLanguage.toUpperCase() }}</span>
                            <svg
                                class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200"
                                :class="isLangOpen ? 'rotate-180' : ''"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <transition
                            enter-active-class="transition duration-150 ease-out"
                            enter-from-class="transform -translate-y-2 opacity-0 scale-95"
                            enter-to-class="transform translate-y-0 opacity-100 scale-100"
                            leave-active-class="transition duration-100 ease-in"
                            leave-from-class="transform translate-y-0 opacity-100 scale-100"
                            leave-to-class="transform -translate-y-2 opacity-0 scale-95"
                        >
                            <div
                                v-if="isLangOpen"
                                id="topbar-lang-listbox"
                                role="listbox"
                                aria-labelledby="topbar-lang-button"
                                class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-64 sm:w-72 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 py-2 z-[60] overflow-hidden"
                                @click.stop
                            >
                                <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700 space-y-2">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Language</p>
                                    <input
                                        v-if="props.languages.length > 7"
                                        v-model="langSearch"
                                        type="search"
                                        autocomplete="off"
                                        placeholder="Search…"
                                        class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                                        @keydown.stop
                                    />
                                </div>
                                <div class="max-h-60 overflow-y-auto">
                                    <button
                                        v-for="lang in displayLanguages"
                                        :key="lang.code"
                                        type="button"
                                        role="option"
                                        :aria-selected="currentLanguage === lang.code"
                                        @click="selectLanguage(lang.code)"
                                        class="w-full flex items-center gap-3 px-3 py-2 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group"
                                        :class="currentLanguage === lang.code ? 'bg-blue-50 dark:bg-blue-900/20' : ''"
                                    >
                                        <span class="text-lg group-hover:scale-110 transition-transform">{{ lang.flag }}</span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ lang.name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ lang.nativeName }}</p>
                                        </div>
                                        <svg
                                            v-if="currentLanguage === lang.code"
                                            class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            aria-hidden="true"
                                        >
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <p v-if="displayLanguages.length === 0" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">No matches.</p>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <!-- Right: social (LMS shows only configured networks; we skip empty URLs) -->
                <div class="flex items-center gap-2 lg:gap-3 shrink-0 ms-auto lg:ms-0">
                    <div class="hidden md:flex items-center gap-1.5">
                        <a
                            v-for="social in visibleSocialLinks"
                            :key="social.name"
                            :href="social.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group p-2 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-blue-900/30 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200"
                            :title="`Follow us on ${social.name}`"
                            :aria-label="`Follow us on ${social.name}`"
                        >
                            <component :is="resolveIcon(social.icon)" class="w-4 h-4" />
                        </a>
                    </div>

                    <button
                        type="button"
                        @click="showMobileMenu = !showMobileMenu"
                        class="lg:hidden relative p-2 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-colors"
                        :aria-expanded="showMobileMenu"
                        aria-controls="topbar-mobile-panel"
                        aria-label="Open contact and language menu"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <span
                            v-if="config.features.showOnlineIndicator"
                            class="absolute top-1 right-1 w-2 h-2 bg-emerald-500 rounded-full"
                            aria-hidden="true"
                        />
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile backdrop + sheet -->
        <transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showMobileMenu"
                class="lg:hidden fixed inset-0 z-[55] bg-black/40"
                aria-hidden="true"
                @click="showMobileMenu = false"
            />
        </transition>

        <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div
                v-if="showMobileMenu"
                id="topbar-mobile-panel"
                class="lg:hidden fixed inset-x-0 top-0 bg-white dark:bg-gray-900 shadow-2xl border-b border-gray-200 dark:border-gray-700 z-[56] max-h-[85vh] overflow-y-auto pt-4 pb-6 px-4"
                role="dialog"
                aria-modal="true"
                aria-label="Site options"
            >
                <div class="max-w-7xl mx-auto space-y-4">
                    <div class="flex justify-end">
                        <button
                            type="button"
                            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800"
                            aria-label="Close menu"
                            @click="showMobileMenu = false"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="flex flex-col gap-3 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <a v-if="config.phone" :href="`tel:${config.phone}`" class="flex items-center gap-3 text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            {{ config.phone }}
                        </a>
                        <component
                            :is="config.mapsUrl ? 'a' : 'span'"
                            v-if="config.address && config.features.showAddress"
                            :href="config.mapsUrl || undefined"
                            :target="config.mapsUrl ? '_blank' : undefined"
                            :rel="config.mapsUrl ? 'noopener noreferrer' : undefined"
                            class="flex items-start gap-3 text-gray-800 dark:text-gray-100"
                            :class="config.mapsUrl ? 'hover:text-blue-600 dark:hover:text-blue-400' : ''"
                        >
                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span>{{ config.address }}</span>
                        </component>
                        <a v-if="config.email" :href="`mailto:${config.email}`" class="flex items-center gap-3 text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 break-all">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            {{ config.email }}
                        </a>
                    </div>

                    <div v-if="config.features.allowAnonymousLanguageSwitch" class="pb-4 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Language</p>
                        <input
                            v-if="props.languages.length > 5"
                            v-model="langSearch"
                            type="search"
                            autocomplete="off"
                            placeholder="Search languages…"
                            class="w-full mb-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 px-3 py-2 text-sm"
                        />
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="lang in displayLanguages"
                                :key="lang.code"
                                type="button"
                                @click="selectLanguage(lang.code)"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg border transition-all"
                                :class="currentLanguage === lang.code
                                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-blue-300'"
                            >
                                <span class="text-lg">{{ lang.flag }}</span>
                                <span class="text-sm font-medium">{{ lang.name }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a
                            v-for="social in visibleSocialLinks"
                            :key="social.name"
                            :href="social.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-blue-900/30 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-all"
                            :title="`Follow us on ${social.name}`"
                            :aria-label="`Follow us on ${social.name}`"
                        >
                            <component :is="resolveIcon(social.icon)" class="w-5 h-5" />
                        </a>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, h } from 'vue';

const defaultFeatures = {
    showAddress: true,
    showOnlineIndicator: true,
    enableScrollCollapse: true,
    allowAnonymousLanguageSwitch: true,
};

const props = defineProps({
    phone: { type: String, default: '' },
    email: { type: String, default: '' },
    address: { type: String, default: '' },
    mapsUrl: { type: String, default: '' },
    languages: {
        type: Array,
        default: () => [
            { code: 'en', name: 'English', nativeName: 'English', flag: '🇺🇸' },
            { code: 'es', name: 'Spanish', nativeName: 'Español', flag: '🇪🇸' },
            { code: 'fr', name: 'French', nativeName: 'Français', flag: '🇫🇷' },
            { code: 'de', name: 'German', nativeName: 'Deutsch', flag: '🇩🇪' },
            { code: 'ar', name: 'Arabic', nativeName: 'العربية', flag: '🇸🇦' },
            { code: 'hi', name: 'Hindi', nativeName: 'हिन्दी', flag: '🇮🇳' },
            { code: 'zh', name: 'Chinese', nativeName: '中文', flag: '🇨🇳' },
        ],
    },
    socialLinks: {
        type: Array,
        default: () => [
            { name: 'Twitter', url: 'https://twitter.com', icon: 'twitter' },
            { name: 'LinkedIn', url: 'https://linkedin.com', icon: 'linkedin' },
            { name: 'Facebook', url: 'https://facebook.com', icon: 'facebook' },
            { name: 'Instagram', url: 'https://instagram.com', icon: 'instagram' },
        ],
    },
    initialLanguage: { type: String, default: 'en' },
    features: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['language-change']);

const mergedFeatures = computed(() => ({ ...defaultFeatures, ...props.features }));

const config = computed(() => ({
    phone: props.phone,
    email: props.email,
    address: props.address,
    mapsUrl: props.mapsUrl,
    socialLinks: props.socialLinks,
    features: mergedFeatures.value,
}));

const visibleSocialLinks = computed(() =>
    (config.value.socialLinks || []).filter((s) => s && s.url && String(s.url).trim() !== '' && String(s.url).trim() !== '#'),
);

const isLangOpen = ref(false);
const showMobileMenu = ref(false);
const currentLanguage = ref(props.initialLanguage);
const langSearch = ref('');
const isCompact = ref(false);
const langDropdownRoot = ref(null);

const displayLanguages = computed(() => {
    const q = langSearch.value.trim().toLowerCase();
    if (!q) {
        return props.languages;
    }
    return props.languages.filter(
        (l) =>
            l.code.toLowerCase().includes(q) ||
            l.name.toLowerCase().includes(q) ||
            (l.nativeName && String(l.nativeName).toLowerCase().includes(q)),
    );
});

function resolveIcon(icon) {
    if (typeof icon === 'string') {
        return getSocialIcon(icon);
    }
    return icon;
}

function getSocialIcon(name) {
    const icons = {
        twitter: {
            render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24', 'aria-hidden': 'true' }, [
                h('path', { d: 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z' }),
            ]),
        },
        linkedin: {
            render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24', 'aria-hidden': 'true' }, [
                h('path', { d: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z' }),
            ]),
        },
        facebook: {
            render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24', 'aria-hidden': 'true' }, [
                h('path', { d: 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z' }),
            ]),
        },
        instagram: {
            render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24', 'aria-hidden': 'true' }, [
                h('path', { d: 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z' }),
            ]),
        },
        youtube: {
            render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24', 'aria-hidden': 'true' }, [
                h('path', { d: 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z' }),
            ]),
        },
    };

    return icons[String(name).toLowerCase()] || icons.twitter;
}

function toggleLanguageDropdown() {
    isLangOpen.value = !isLangOpen.value;
    if (isLangOpen.value) {
        langSearch.value = '';
    }
}

function selectLanguage(langCode) {
    currentLanguage.value = langCode;
    isLangOpen.value = false;
    showMobileMenu.value = false;
    langSearch.value = '';

    localStorage.setItem('preferred_language', langCode);
    emit('language-change', langCode);
}

function getLanguageFlag(code) {
    const lang = props.languages.find((l) => l.code === code);
    return lang ? lang.flag : '🌍';
}

function closeDropdownOnClickOutside(e) {
    if (!isLangOpen.value) {
        return;
    }
    const root = langDropdownRoot.value;
    if (root && !root.contains(e.target)) {
        isLangOpen.value = false;
    }
}

function onDocumentKeydown(e) {
    if (e.key === 'Escape') {
        isLangOpen.value = false;
        showMobileMenu.value = false;
    }
}

let lastScrollY = 0;

function onScroll() {
    if (!config.value.features.enableScrollCollapse) {
        isCompact.value = false;
        return;
    }
    const y = window.scrollY || 0;
    if (y > 72 && y > lastScrollY) {
        isCompact.value = true;
    } else if (y < 48) {
        isCompact.value = false;
    }
    lastScrollY = y;
}

watch(showMobileMenu, (open) => {
    document.body.style.overflow = open ? 'hidden' : '';
});

onMounted(() => {
    const savedLang = localStorage.getItem('preferred_language');
    if (savedLang && props.languages.some((l) => l.code === savedLang)) {
        currentLanguage.value = savedLang;
    }

    document.addEventListener('click', closeDropdownOnClickOutside);
    document.addEventListener('keydown', onDocumentKeydown);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdownOnClickOutside);
    document.removeEventListener('keydown', onDocumentKeydown);
    window.removeEventListener('scroll', onScroll);
    document.body.style.overflow = '';
});

defineExpose({
    currentLanguage,
    selectLanguage,
});
</script>

<style scoped>
.enhanced-topbar {
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.max-h-60::-webkit-scrollbar {
    width: 6px;
}

.max-h-60::-webkit-scrollbar-track {
    background: transparent;
}

.max-h-60::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 3px;
}

.max-h-60::-webkit-scrollbar-thumb:hover {
    background-color: rgba(107, 114, 128, 0.7);
}
</style>
