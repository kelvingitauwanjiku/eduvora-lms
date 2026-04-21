import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { cartApi } from '../services/api';

export const useCartStore = defineStore('cart', () => {
    const items = ref([]);
    const loading = ref(false);
    const total = ref(0);

    const count = computed(() => items.value.length);
    const subtotal = computed(() => {
        return items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    });

    async function fetchCart() {
        loading.value = true;
        try {
            const { data } = await cartApi.get();
            items.value = data.data || data;
            calculateTotal();
        } catch (error) {
            console.error('Failed to fetch cart:', error);
        } finally {
            loading.value = false;
        }
    }

    async function addToCart(courseId, quantity = 1) {
        loading.value = true;
        try {
            const { data } = await cartApi.add({ course_id: courseId, quantity });
            items.value.push(data);
            calculateTotal();
        } catch (error) {
            console.error('Failed to add to cart:', error);
            throw error;
        } finally {
            loading.value = false;
        }
    }

    async function removeFromCart(courseId) {
        loading.value = true;
        try {
            await cartApi.remove(courseId);
            items.value = items.value.filter(item => item.course_id !== courseId);
            calculateTotal();
        } catch (error) {
            console.error('Failed to remove from cart:', error);
            throw error;
        } finally {
            loading.value = false;
        }
    }

    async function checkout(data) {
        loading.value = true;
        try {
            const { data: response } = await cartApi.checkout(data);
            items.value = [];
            total.value = 0;
            return response;
        } finally {
            loading.value = false;
        }
    }

    function calculateTotal() {
        total.value = subtotal.value;
    }

    function clear() {
        items.value = [];
        total.value = 0;
    }

    return {
        items,
        loading,
        total,
        count,
        subtotal,
        fetchCart,
        addToCart,
        removeFromCart,
        checkout,
        clear
    };
});
