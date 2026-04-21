<template>
    <div>
        <div v-if="loading" class="animate-pulse">
            <div class="bg-gray-200 h-96"></div>
        </div>

        <template v-else-if="post">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 py-16">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <router-link to="/blog" class="text-blue-100 hover:text-white flex items-center gap-1 mb-4 transition w-fit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Blog
                    </router-link>
                    
                    <div class="flex items-center gap-3 mb-4">
                        <span v-if="post.category" class="px-3 py-1 bg-white/20 text-white rounded-full text-sm">
                            {{ post.category }}
                        </span>
                        <span class="text-blue-200">{{ formatDate(post.created_at) }}</span>
                        <span class="text-blue-200">{{ post.read_time || '5 min read' }}</span>
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">{{ post.title }}</h1>
                    
                    <div class="flex flex-wrap items-center gap-4 mt-4">
                        <div class="flex items-center gap-3">
                            <img :src="post.user?.avatar || '/images/avatar-placeholder.jpg'" :alt="post.user?.name"
                                class="w-12 h-12 rounded-full object-cover border-2 border-white/30">
                            <div>
                                <p class="font-medium text-white">{{ post.user?.name }}</p>
                                <p class="text-sm text-blue-200">{{ post.user?.title || 'Author' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Featured Image -->
                <img v-if="post.image" :src="post.image" :alt="post.title"
                    class="w-full h-96 object-cover rounded-2xl mb-8">

                <!-- Article Content -->
                <div class="prose prose-lg max-w-none" v-html="post.content"></div>

                <!-- Share & Like Section -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-4">
                            <button @click="likePost" :class="post.is_liked ? 'text-red-500' : 'text-gray-500 hover:text-red-500'"
                                class="flex items-center gap-2 transition">
                                <svg class="w-6 h-6" :fill="post.is_liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span>{{ post.likes_count || 0 }}</span>
                            </button>
                            <span class="flex items-center gap-2 text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span>{{ comments.length }} Comments</span>
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-gray-500 font-medium">Share:</span>
                            <button @click="sharePost('facebook')" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </button>
                            <button @click="sharePost('twitter')" class="p-2 text-gray-500 hover:text-blue-400 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </button>
                            <button @click="sharePost('linkedin')" class="p-2 text-gray-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </button>
                            <button @click="copyLink" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Author Bio -->
                <div class="mt-8 p-6 bg-gray-50 rounded-2xl">
                    <div class="flex items-start gap-4">
                        <img :src="post.user?.avatar || '/images/avatar-placeholder.jpg'" :alt="post.user?.name"
                            class="w-16 h-16 rounded-full object-cover">
                        <div>
                            <h3 class="font-semibold text-gray-900">Written by {{ post.user?.name }}</h3>
                            <p class="text-sm text-gray-500 mb-2">{{ post.user?.title || 'Content Creator' }}</p>
                            <p v-if="post.user?.bio" class="text-gray-600">{{ post.user.bio }}</p>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="mt-12">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Comments ({{ comments.length }})</h3>

                    <!-- Comment Form -->
                    <div class="mb-8">
                        <textarea v-model="newComment" rows="4" placeholder="Share your thoughts..."
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        <button @click="postComment" :disabled="!newComment.trim()"
                            class="mt-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                            Post Comment
                        </button>
                    </div>

                    <!-- Comments List -->
                    <div v-if="commentsLoading" class="space-y-6">
                        <div v-for="i in 3" :key="i" class="animate-pulse flex gap-4">
                            <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                            <div class="flex-1">
                                <div class="h-4 bg-gray-200 rounded w-1/4 mb-2"></div>
                                <div class="h-16 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="comments.length === 0" class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        No comments yet. Be the first to comment!
                    </div>

                    <div v-else class="space-y-6">
                        <div v-for="comment in comments" :key="comment.id" class="flex gap-4">
                            <img :src="comment.user?.avatar || '/images/avatar-placeholder.jpg'" :alt="comment.user?.name"
                                class="w-10 h-10 rounded-full object-cover">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-900">{{ comment.user?.name }}</span>
                                    <span class="text-sm text-gray-500">{{ formatDate(comment.created_at) }}</span>
                                </div>
                                <p class="mt-1 text-gray-600">{{ comment.content }}</p>
                                <div class="flex items-center gap-4 mt-2">
                                    <button @click="likeComment(comment)" class="text-sm text-gray-500 hover:text-red-500 flex items-center gap-1 transition">
                                        <svg class="w-4 h-4" :fill="comment.is_liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        {{ comment.likes_count || 0 }}
                                    </button>
                                    <button class="text-sm text-gray-500 hover:text-blue-500 transition">Reply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Posts -->
                <div v-if="relatedPosts.length > 0" class="mt-12">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Related Articles</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <router-link v-for="related in relatedPosts" :key="related.id" :to="`/blog/${related.id}`" class="group">
                            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition">
                                <div class="aspect-video relative overflow-hidden">
                                    <img :src="related.image || '/images/blog-placeholder.jpg'" class="w-full h-full object-cover group-hover:scale-105 transition">
                                </div>
                                <div class="p-4">
                                    <h4 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ related.title }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">{{ formatDate(related.created_at) }}</p>
                                </div>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
        </template>

        <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h2 class="text-2xl font-bold text-gray-900">Article Not Found</h2>
            <router-link to="/blog" class="text-blue-600 hover:underline mt-2 inline-block">
                Back to Blog
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { blogApi } from '@/services/api';

const route = useRoute();

const loading = ref(true);
const post = ref(null);
const comments = ref([]);
const relatedPosts = ref([]);
const commentsLoading = ref(true);
const newComment = ref('');

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

async function fetchPost() {
    loading.value = true;
    try {
        const { data } = await blogApi.getById(route.params.id);
        post.value = data;
    } catch (error) {
        console.error('Error fetching post:', error);
        post.value = null;
    } finally {
        loading.value = false;
    }
}

async function fetchComments() {
    commentsLoading.value = true;
    try {
        const { data } = await blogApi.getComments(route.params.id);
        comments.value = data || [];
    } catch (error) {
        console.error('Error fetching comments:', error);
    } finally {
        commentsLoading.value = false;
    }
}

async function likePost() {
    try {
        await blogApi.like(route.params.id);
        post.value.is_liked = !post.value.is_liked;
        post.value.likes_count = (post.value.likes_count || 0) + (post.value.is_liked ? 1 : -1);
    } catch (error) {
        console.error('Error liking post:', error);
    }
}

async function postComment() {
    if (!newComment.value.trim()) return;
    try {
        const { data } = await blogApi.comment(route.params.id, { content: newComment.value });
        comments.value.unshift(data);
        newComment.value = '';
    } catch (error) {
        console.error('Error posting comment:', error);
    }
}

async function likeComment(comment) {
    try {
        comment.is_liked = !comment.is_liked;
        comment.likes_count = (comment.likes_count || 0) + (comment.is_liked ? 1 : -1);
    } catch (error) {
        console.error('Error liking comment:', error);
    }
}

function sharePost(platform) {
    const url = window.location.href;
    const title = post.value?.title || '';
    
    const urls = {
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
        twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`,
        linkedin: `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}`,
    };
    
    if (urls[platform]) {
        window.open(urls[platform], '_blank', 'width=600,height=400');
    }
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href);
}

onMounted(() => {
    fetchPost();
    fetchComments();
});
</script>