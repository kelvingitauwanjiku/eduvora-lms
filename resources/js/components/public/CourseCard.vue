<template>
    <div class="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <router-link :to="`/course/${course.slug}`" class="block">
            <div class="relative overflow-hidden">
                <img 
                    :src="course.thumbnail || '/images/course-placeholder.jpg'" 
                    :alt="course.title"
                    class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500"
                >
                
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <div class="absolute top-3 left-3 flex gap-2">
                    <span v-if="course.is_free" class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">
                        FREE
                    </span>
                    <span v-else-if="course.discount_percentage" class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">
                        {{ course.discount_percentage }}% OFF
                    </span>
                </div>

                <button 
                    v-if="showWishlist"
                    @click.prevent="toggleWishlist"
                    class="absolute top-3 right-3 w-9 h-9 bg-white/90 backdrop-blur rounded-full flex items-center justify-center shadow hover:bg-white transition-colors"
                >
                    <svg 
                        class="w-5 h-5 transition-colors" 
                        :class="isInWishlist ? 'text-red-500 fill-current' : 'text-gray-400'"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>
        </router-link>

        <div class="p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded">
                    {{ course.category?.name || course.category_name || 'General' }}
                </span>
            </div>

            <router-link :to="`/course/${course.slug}`">
                <h3 class="font-semibold text-gray-900 mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors">
                    {{ course.title }}
                </h3>
            </router-link>

            <div class="flex items-center gap-3 mb-4">
                <img 
                    :src="course.instructor?.avatar || course.instructor_photo || '/images/avatar.png'" 
                    :alt="course.instructor?.name || course.instructor_name || 'Instructor'"
                    class="w-8 h-8 rounded-full object-cover"
                >
                <span class="text-sm text-gray-600">
                    {{ course.instructor?.name || course.instructor_name || 'Unknown' }}
                </span>
            </div>

            <div class="flex items-center gap-4 mb-4">
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">
                        {{ course.rating || '0.0' }}
                    </span>
                </div>
                <span class="text-sm text-gray-500">
                    ({{ formatNumber(course.total_reviews || course.total_rating || 0) }} reviews)
                </span>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{ formatNumber(course.total_enrolled || 0) }} students
                </div>

                <button 
                    v-if="showAddToCart && !course.is_free"
                    @click.prevent="handleAddToCart"
                    :disabled="isInCart || adding"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all"
                    :class="isInCart 
                        ? 'bg-green-100 text-green-700 cursor-not-allowed' 
                        : 'bg-blue-600 text-white hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/20'"
                >
                    <span v-if="isInCart">In Cart</span>
                    <span v-else-if="adding">Adding...</span>
                    <span v-else>Add to Cart</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    course: {
        type: Object,
        required: true
    },
    showWishlist: {
        type: Boolean,
        default: true
    },
    showAddToCart: {
        type: Boolean,
        default: true
    },
    wishlistItems: {
        type: Array,
        default: () => []
    },
    cartItems: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['wishlist-toggle', 'add-to-cart']);

const adding = ref(false);

const isInWishlist = computed(() => {
    return props.wishlistItems.some(item => item.id === props.course.id);
});

const isInCart = computed(() => {
    return props.cartItems.some(item => item.id === props.course.id);
});

const formatNumber = (num) => {
    if (!num) return '0';
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
};

const toggleWishlist = () => {
    emit('wishlist-toggle', props.course.id);
};

const handleAddToCart = () => {
    if (isInCart.value || adding.value) return;
    
    adding.value = true;
    emit('add-to-cart', props.course.id);
    
    setTimeout(() => {
        adding.value = false;
    }, 1000);
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
