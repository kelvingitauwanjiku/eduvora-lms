import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useThemeStore = defineStore('theme', () => {
    const isDark = ref(false);

    function initTheme() {
        // Check localStorage
        const saved = localStorage.getItem('theme');
        if (saved) {
            isDark.value = saved === 'dark';
        } else {
            // Check system preference
            isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        applyTheme();
    }

    function toggleDark() {
        isDark.value = !isDark.value;
        localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
        applyTheme();
    }

    function applyTheme() {
        if (isDark.value) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    // Watch for system preference changes
    if (typeof window !== 'undefined') {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                isDark.value = e.matches;
                applyTheme();
            }
        });
    }

    return {
        isDark,
        initTheme,
        toggleDark
    };
});
