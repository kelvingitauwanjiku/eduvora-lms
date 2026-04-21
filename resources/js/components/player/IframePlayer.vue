<template>
    <div class="aspect-video bg-black rounded-lg overflow-hidden flex items-center justify-center">
        <iframe v-if="url" :src="embedUrl" class="w-full h-full" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    url: String,
    type: { type: String, default: 'youtube' }
});

const embedUrl = computed(() => {
    if (!props.url) return '';
    if (props.type === 'youtube') {
        const videoId = props.url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&?]+)/)?.[1];
        return videoId ? `https://www.youtube.com/embed/${videoId}` : '';
    }
    if (props.type === 'vimeo') {
        const videoId = props.url.match(/vimeo\.com\/(\d+)/)?.[1];
        return videoId ? `https://player.vimeo.com/video/${videoId}` : '';
    }
    return props.url;
});
</script>