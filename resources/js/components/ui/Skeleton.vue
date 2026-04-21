<template>
    <div :class="['animate-pulse', containerClass]">
        <slot />
    </div>
</template>

<script setup>
defineProps({
    variant: {
        type: String,
        default: 'rect',
    },
    width: {
        type: String,
        default: 'w-full',
    },
    height: {
        type: String,
        default: 'h-4',
    },
    rounded: {
        type: String,
        default: 'rounded',
    },
    containerClass: {
        type: String,
        default: '',
    },
});
</script>