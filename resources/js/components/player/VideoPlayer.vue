<template>
    <div class="aspect-video bg-black rounded-lg overflow-hidden relative">
        <video v-if="src" ref="videoRef" :src="src" controls class="w-full h-full" @timeupdate="onTimeUpdate" @loadedmetadata="onLoaded"></video>
        <div v-else class="w-full h-full flex items-center justify-center text-white">
            <div class="text-center">
                <svg class="w-20 h-20 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-400">Video player</p>
            </div>
        </div>
        <div v-if="drmEnabled" class="absolute top-4 right-4 px-2 py-1 bg-red-600 text-white text-xs rounded">DRM</div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    src: String,
    drmEnabled: { type: Boolean, default: false },
    autoplay: { type: Boolean, default: false },
    loop: { type: Boolean, default: false },
});

const emit = defineEmits(['play', 'pause', 'ended', 'timeupdate', 'progress']);
const videoRef = ref(null);

function onTimeUpdate(e) {
    emit('timeupdate', e.target.currentTime, e.target.duration);
}

function onLoaded(e) {
    emit('loaded', e.target.duration);
}

function play() { videoRef.value?.play(); }
function pause() { videoRef.value?.pause(); }
function seek(time) { if (videoRef.value) videoRef.value.currentTime = time; }
function setVolume(vol) { if (videoRef.value) videoRef.value.volume = vol; }

defineExpose({ play, pause, seek, setVolume, videoRef });
</script>