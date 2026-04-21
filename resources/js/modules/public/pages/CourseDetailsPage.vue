<template>
    <div>
        <div v-if="loading" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="animate-pulse">
                <div class="h-96 bg-gray-200 rounded-xl"></div>
                <div class="mt-8 h-8 bg-gray-200 rounded w-2/3"></div>
                <div class="mt-4 h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>

        <div v-else-if="course" class="bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <div class="aspect-video bg-gray-900 rounded-xl overflow-hidden mb-8 relative group">
                            <img v-if="!showVideo" :src="course.thumbnail || '/images/course-placeholder.jpg'" :alt="course.title" class="w-full h-full object-cover">
                            <div v-if="course.preview_url && !showVideo" class="absolute inset-0 flex items-center justify-center">
                                <button @click="showVideo = true" class="w-20 h-20 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition shadow-lg group-hover:scale-110">
                                    <svg class="w-8 h-8 text-blue-600 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </button>
                            </div>
                            <div v-if="showVideo && course.preview_url" class="w-full h-full">
                                <video v-if="isHtml5Video" :src="course.preview_url" controls class="w-full h-full object-contain"></video>
                                <iframe v-else-if="isYoutubeVideo" :src="embedYoutubeUrl" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                                <iframe v-else-if="isVimeoVideo" :src="embedVimeoUrl" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                            </div>
                            <img v-else :src="course.thumbnail || '/images/course-placeholder.jpg'" :alt="course.title" class="w-full h-full object-cover">
                        </div>

                        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">About this course</h2>
                            <div class="prose max-w-none" v-html="course.description"></div>
                        </div>

                        <!-- What you'll learn -->
                        <div v-if="course.outcomes?.length" class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">What you'll learn</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div v-for="(outcome, idx) in course.outcomes" :key="idx" class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-600">{{ outcome }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div v-if="course.requirements?.length" class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Requirements</h2>
                            <ul class="space-y-2">
                                <li v-for="(req, idx) in course.requirements" :key="idx" class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    <span class="text-gray-600">{{ req }}</span>
                                </li>
                            </ul>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-200 p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Course Content</h2>
                            <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
                                <span>{{ course.sections?.length || 0 }} sections</span>
                                <span>{{ totalLessons }} lessons</span>
                                <span>{{ totalDuration }}</span>
                            </div>
                            <div class="space-y-2">
                                <div v-for="(section, sIdx) in course.sections || []" :key="section.id" class="border border-gray-200 rounded-lg">
                                    <button 
                                        @click="toggleSection(sIdx)" 
                                        class="w-full px-4 py-3 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition rounded-t-lg"
                                    >
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="openSections.includes(sIdx) ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                            <h3 class="font-medium text-gray-900 text-left">{{ section.title }}</h3>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ section.lessons?.length || 0 }} lessons</span>
                                    </button>
                                    <div v-if="openSections.includes(sIdx)" class="divide-y divide-gray-100">
                                        <div v-for="lesson in section.lessons || []" :key="lesson.id" class="px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition">
                                            <div class="flex items-center gap-3">
                                                <svg v-if="lesson.type === 'video'" class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                </svg>
                                                <svg v-else-if="lesson.type === 'quiz'" class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="text-sm text-gray-600">{{ lesson.title }}</span>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ lesson.duration || '0:00' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructor Info -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6 mt-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Your Instructor</h2>
                            <div class="flex items-start gap-4">
                                <img :src="course.instructor?.avatar || '/images/avatar.png'" class="w-20 h-20 rounded-full object-cover">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ course.instructor?.name }}</h3>
                                    <p class="text-sm text-gray-500">{{ course.instructor?.title || 'Expert Instructor' }}</p>
                                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            {{ course.instructor?.rating || '4.8' }}
                                        </span>
                                        <span>{{ course.instructor?.total_students || 0 }} students</span>
                                        <span>{{ course.instructor?.total_courses || 0 }} courses</span>
                                    </div>
                                    <p v-if="course.instructor?.bio" class="text-sm text-gray-600 mt-3">{{ course.instructor.bio }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews Section -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6 mt-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Reviews</h2>
                            <div class="flex items-center gap-4 mb-6">
                                <div class="text-4xl font-bold text-gray-900">{{ course.rating || '0.0' }}</div>
                                <div>
                                    <div class="flex items-center gap-1">
                                        <svg v-for="i in 5" :key="i" class="w-5 h-5" :class="i <= Math.round(course.rating || 0) ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500">{{ course.total_reviews || 0 }} reviews</p>
                                </div>
                            </div>
                            
                            <div v-if="reviews.length > 0" class="space-y-6">
                                <div v-for="review in reviews" :key="review.id" class="border-b border-gray-100 pb-6 last:border-0">
                                    <div class="flex items-start gap-3">
                                        <img :src="review.user?.avatar || '/images/avatar.png'" class="w-10 h-10 rounded-full object-cover">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="font-medium text-gray-900">{{ review.user?.name }}</span>
                                                    <div class="flex items-center gap-1 mt-0.5">
                                                        <svg v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <span class="text-sm text-gray-500">{{ formatDate(review.created_at) }}</span>
                                            </div>
                                            <p v-if="review.content" class="text-gray-600 mt-2">{{ review.content }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-gray-500 text-center py-4">No reviews yet</p>
                        </div>

                <!-- Related Courses -->
                <div v-if="relatedCourses.length > 0" class="mt-12 lg:col-span-2">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Courses</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <router-link v-for="relCourse in relatedCourses" :key="relCourse.id" :to="`/courses/${relCourse.id}`" class="group">
                            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition">
                                <div class="aspect-video relative overflow-hidden">
                                    <img :src="relCourse.thumbnail || '/images/course-placeholder.jpg'" class="w-full h-full object-cover group-hover:scale-105 transition">
                                    <span v-if="relCourse.is_free" class="absolute top-3 left-3 px-3 py-1 bg-green-600 text-white text-xs font-medium rounded-full">Free</span>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition line-clamp-2">{{ relCourse.title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ relCourse.instructor?.name }}</p>
                                    <div class="flex items-center gap-1 mt-2">
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span class="text-sm font-medium">{{ relCourse.rating || '0.0' }}</span>
                                    </div>
                                    <span class="text-lg font-bold mt-2 block">{{ relCourse.is_free ? 'Free' : '$' + relCourse.price }}</span>
                                </div>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-24">
                            <div class="aspect-video rounded-lg overflow-hidden mb-4 bg-gray-100">
                                <img :src="course.thumbnail || '/images/course-placeholder.jpg'" :alt="course.title" class="w-full h-full object-cover">
                            </div>
                            
                            <div class="text-center mb-4">
                                <span v-if="course.is_free" class="text-3xl font-bold text-green-600">Free</span>
                                <span v-else class="text-3xl font-bold text-gray-900">${{ course.price }}</span>
                                <span v-if="course.discount_percentage" class="ml-2 px-2 py-1 bg-red-100 text-red-600 text-sm rounded">-{{ course.discount_percentage }}%</span>
                            </div>

                            <button v-if="!isEnrolled" @click="handleEnroll" :disabled="enrolling"
                                class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-600/20 transition disabled:opacity-50">
                                {{ enrolling ? 'Enrolling...' : (course.is_free ? 'Enroll Now' : 'Buy Now') }}
                            </button>

                            <router-link v-else :to="`/student/courses/${course.id}/learn`"
                                class="block w-full py-3.5 bg-green-600 text-white font-semibold rounded-xl text-center hover:bg-green-700 transition">
                                Go to Course
                            </router-link>

                            <button @click="toggleWishlist"
                                class="w-full mt-3 py-3 border border-gray-200 rounded-xl font-medium hover:bg-gray-50 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" :class="isWishlisted ? 'text-red-500 fill-current' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                {{ isWishlisted ? 'Wishlisted' : 'Add to Wishlist' }}
                            </button>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <router-link :to="`/instructors/${course.instructor?.id}`" class="flex items-center gap-4 hover:opacity-80 transition">
                                    <img :src="course.instructor?.avatar || '/images/avatar.png'" class="w-12 h-12 rounded-full object-cover">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ course.instructor?.name }}</p>
                                        <p class="text-sm text-gray-500">Instructor</p>
                                    </div>
                                </router-link>
                            </div>

                            <div class="mt-6 space-y-4 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Duration
                                    </span>
                                    <span class="text-gray-900">{{ totalDuration }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        Lessons
                                    </span>
                                    <span class="text-gray-900">{{ totalLessons }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354l3.12 3.333-3.12.833V4.354l3.12.833V2.688L6 4.354l6 1.833zm0 15.292V14.52l-3.12-.833v3.957L6 16.687l3.12-1.833 3.12.833V17.52l3.12.833v1.833L6 18.521l-3.12 1.833V20.52l3.12-.833v1.833L12 20.146l-3.12-.833v-3.957L3 17.52l6-1.833z" transform="scale(0.5) translate(24,24)" />
                                        </svg>
                                        Students
                                    </span>
                                    <span class="text-gray-900">{{ course.total_enrolled || 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                        </svg>
                                        Language
                                    </span>
                                    <span class="text-gray-900">{{ course.language || 'English' }}</span>
                                </div>
                                <div v-if="course.certificate" class="flex items-center justify-between">
                                    <span class="text-gray-500">Certificate</span>
                                    <span class="text-gray-900">Yes</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Level
                                    </span>
                                    <span class="text-gray-900">{{ course.level || 'All Levels' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Access
                                    </span>
                                    <span class="text-gray-900">Lifetime</span>
                                </div>
                            </div>

                            <!-- Share -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <p class="text-sm text-gray-500 mb-3">Share this course:</p>
                                <div class="flex gap-2">
                                    <button @click="shareOn('facebook')" class="p-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </button>
                                    <button @click="shareOn('twitter')" class="p-2 text-black bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                        </svg>
                                    </button>
                                    <button @click="shareOn('linkedin')" class="p-2 text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                        </svg>
                                    </button>
                                    <button @click="shareOn('whatsapp')" class="p-2 text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.218 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                    </button>
                                    <button @click="copyLink" class="p-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { courseApi, wishlistApi } from '@/services/api';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const course = ref(null);
const enrolling = ref(false);
const isEnrolled = ref(false);
const isWishlisted = ref(false);
const openSections = ref([0]);
const relatedCourses = ref([]);
const reviews = ref([]);
const showVideo = ref(false);

const isHtml5Video = computed(() => {
    return course.value?.preview_url?.includes('.mp4') || course.value?.preview_url?.includes('.webm');
});

const isYoutubeVideo = computed(() => {
    return course.value?.preview_url?.includes('youtube') || course.value?.preview_url?.includes('youtu.be');
});

const isVimeoVideo = computed(() => {
    return course.value?.preview_url?.includes('vimeo');
});

const embedYoutubeUrl = computed(() => {
    if (!course.value?.preview_url) return '';
    let url = course.value.preview_url;
    if (url.includes('youtu.be')) {
        const videoId = url.split('youtu.be/')[1];
        return `https://www.youtube.com/embed/${videoId}`;
    }
    if (url.includes('youtube.com/watch')) {
        const searchParams = new URL(url).searchParams;
        return `https://www.youtube.com/embed/${searchParams.get('v')}`;
    }
    return url;
});

const embedVimeoUrl = computed(() => {
    if (!course.value?.preview_url) return '';
    const videoId = course.value.preview_url.split('vimeo.com/')[1];
    return `https://player.vimeo.com/video/${videoId}`;
});

const totalLessons = computed(() => {
    if (!course.value?.sections) return 0;
    return course.value.sections.reduce((sum, section) => sum + (section.lessons?.length || 0), 0);
});

const totalDuration = computed(() => {
    if (!course.value?.sections) return '0h';
    let total = 0;
    course.value.sections.forEach(section => {
        section.lessons?.forEach(lesson => {
            const duration = lesson.duration?.split(':') || [0];
            total += parseInt(duration[0]) * 60 + parseInt(duration[1] || 0);
        });
    });
    const hours = Math.floor(total / 60);
    const mins = total % 60;
    return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`;
});

function toggleSection(index) {
    const idx = openSections.value.indexOf(index);
    if (idx > -1) {
        openSections.value.splice(idx, 1);
    } else {
        openSections.value.push(index);
    }
}

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

async function toggleWishlist() {
    try {
        if (isWishlisted.value) {
            await wishlistApi.remove(course.value.id);
        } else {
            await wishlistApi.add(course.value.id);
        }
        isWishlisted.value = !isWishlisted.value;
    } catch (error) {
        console.error('Error toggling wishlist:', error);
        isWishlisted.value = !isWishlisted.value;
    }
}

async function fetchCourse() {
    loading.value = true;
    try {
        const { data } = await courseApi.getById(route.params.id);
        course.value = data;
    } catch (error) {
        console.error('Error fetching course:', error);
    } finally {
        loading.value = false;
    }
}

async function handleEnroll() {
    if (!authStore.isAuthenticated) {
        return router.push({ name: 'login', query: { redirect: route.fullPath } });
    }

    enrolling.value = true;
    try {
        await courseApi.enroll(course.value.id);
        isEnrolled.value = true;
    } catch (error) {
        console.error('Error enrolling:', error);
    } finally {
        enrolling.value = false;
    }
}

function shareOn(platform) {
    const url = window.location.href;
    const title = course.value?.title || '';
    
    const urls = {
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
        twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`,
        linkedin: `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}`,
        whatsapp: `https://api.whatsapp.com/send?text=${encodeURIComponent(title + ' ' + url)}`,
    };
    
    if (urls[platform]) {
        window.open(urls[platform], '_blank', 'width=600,height=400');
    }
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href);
}

onMounted(() => {
    fetchCourse();
});
</script>