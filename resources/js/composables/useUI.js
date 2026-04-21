import { ref, computed } from 'vue';

export function useDropdown() {
    const isOpen = ref(false);

    function toggle() {
        isOpen.value = !isOpen.value;
    }

    function open() {
        isOpen.value = true;
    }

    function close() {
        isOpen.value = false;
    }

    function toggleWith(show) {
        isOpen.value = show ?? !isOpen.value;
    }

    return {
        isOpen,
        toggle,
        open,
        close,
        toggleWith
    };
}

export function usePagination(items, itemsPerPage = 10) {
    const currentPage = ref(1);

    const totalPages = computed(() => {
        return Math.ceil(items.value.length / itemsPerPage);
    });

    const paginatedItems = computed(() => {
        const start = (currentPage.value - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        return items.value.slice(start, end);
    });

    function goToPage(page) {
        if (page >= 1 && page <= totalPages.value) {
            currentPage.value = page;
        }
    }

    function nextPage() {
        if (currentPage.value < totalPages.value) {
            currentPage.value++;
        }
    }

    function prevPage() {
        if (currentPage.value > 1) {
            currentPage.value--;
        }
    }

    return {
        currentPage,
        totalPages,
        paginatedItems,
        goToPage,
        nextPage,
        prevPage
    };
}

export function useDebounce(fn, delay = 300) {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
}

export function useClickOutside(targetElement, callback) {
    const listener = (event) => {
        if (!targetElement.value || targetElement.value.contains(event.target)) {
            return;
        }
        callback(event);
    };

    document.addEventListener('click', listener);

    return () => {
        document.removeEventListener('click', listener);
    };
}

export function useMediaQuery(query) {
    const matches = ref(false);

    if (typeof window !== 'undefined') {
        matches.value = window.matchMedia(query).matches;

        window.matchMedia(query).addEventListener('change', (event) => {
            matches.value = event.matches;
        });
    }

    return matches;
}
