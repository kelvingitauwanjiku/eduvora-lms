<template>
    <div>
        <div class="bg-white border-b border-gray-200 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900">All Courses</h1>
                <p class="mt-2 text-gray-500">Explore {{ totalCourses }} courses</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Filters Sidebar -->
                <aside class="w-full lg:w-72 shrink-0">
                    <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-24">
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Search</h3>
                            <div class="relative">
                                <input v-model="filters.search" @input="applyFilters" type="text" placeholder="Search courses..." 
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Category</h3>
                            <select v-model="filters.category" @change="applyFilters" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Price</h3>
                            <div class="space-y-2">
                                <label v-for="option in priceOptions" :key="option.value" class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="filters.price" :value="option.value" @change="applyFilters" 
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm text-gray-600">{{ option.label }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Level</h3>
                            <div class="space-y-2">
                                <label v-for="level in levelOptions" :key="level.value" class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="filters.level" :value="level.value" @change="applyFilters" 
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm text-gray-600">{{ level.label }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Language</h3>
                            <select v-model="filters.language" @change="applyFilters" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <option value="">All Languages</option>
                                <option value="english">English</option>
                                <option value="spanish">Spanish</option>
                                <option value="french">French</option>
                                <option value="german">German</option>
                                <option value="chinese">Chinese</option>
                                <option value="japanese">Japanese</option>
                            </select>
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-900 mb-3">Rating</h3>
                            <div class="space-y-2">
                                <label v-for="rating in [4, 3, 2, 1]" :key="rating" class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="filters.rating" :value="rating" @change="applyFilters"
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <div class="flex items-center">
                                        <svg v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= rating ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-xs text-gray-500 ml-1">& Up</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Courses Grid/List -->
                <main class="flex-1">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <p class="text-gray-500">{{ totalCourses }} courses found</p>
                            <button v-if="compareList.length > 0" @click="showCompareModal = true" class="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                                </svg>
                                Compare ({{ compareList.length }})
                            </button>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                                <button 
                                    @click="viewMode = 'grid'" 
                                    :class="viewMode === 'grid' ? 'bg-white shadow-sm' : ''"
                                    class="p-2 rounded-md transition"
                                >
                                    <svg class="w-5 h-5" :class="viewMode === 'grid' ? 'text-blue-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                </button>
                                <button 
                                    @click="viewMode = 'list'" 
                                    :class="viewMode === 'list' ? 'bg-white shadow-sm' : ''"
                                    class="p-2 rounded-md transition"
                                >
                                    <svg class="w-5 h-5" :class="viewMode === 'list' ? 'text-blue-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                </button>
                            </div>
                            <select v-model="filters.sort" @change="applyFilters" class="px-4 py-2.5 border border-gray-200 rounded-xl">
                                <option value="newest">Newest</option>
                                <option value="popular">Most Popular</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="rating">Highest Rated</option>
                            </select>
                        </div>
                    </div>

                    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div v-for="i in 6" :key="i" class="animate-pulse">
                            <div class="bg-gray-200 h-48 rounded-xl"></div>
                            <div class="mt-4 h-4 bg-gray-200 rounded w-3/4"></div>
                            <div class="mt-2 h-4 bg-gray-200 rounded w-1/2"></div>
                        </div>
                    </div>

                    <div v-else-if="courses.length === 0" class="text-center py-16">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">No courses found</h3>
                        <p class="text-gray-500 mt-1">Try adjusting your filters</p>
                    </div>

                    <!-- Grid View -->
                    <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <router-link v-for="course in courses" :key="course.id" :to="`/courses/${course.id}`" class="group">
                            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition duration-300">
                                <div class="aspect-video relative overflow-hidden">
                                    <img :src="course.thumbnail || '/images/course-placeholder.jpg'" :alt="course.title" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    <div class="absolute top-3 left-3 flex gap-1">
                                        <span v-if="course.is_featured" class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-full">Featured</span>
                                        <span v-if="course.is_free" class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded-full">Free</span>
                                    </div>
                                    <div class="absolute top-3 right-3 flex gap-1">
                                        <button @click.prevent="toggleCompare(course.id)" 
                                            :class="isInCompareList(course.id) ? 'bg-purple-600 text-white' : 'bg-white text-gray-600 hover:bg-purple-100'"
                                            class="p-1.5 rounded-full" title="Compare">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                                            </svg>
                                        </button>
                                        <button @click.prevent="toggleWishlist(course.id)" class="p-1.5 bg-white text-gray-600 rounded-full hover:text-red-500 hover:bg-red-50" title="Wishlist">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition line-clamp-2">{{ course.title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ course.instructor?.name }}</p>
                                    <div class="flex items-center gap-2 mt-3">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            <span class="text-sm font-medium">{{ course.rating || '0.0' }}</span>
                                        </div>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-sm text-gray-500">{{ course.total_enrolled || 0 }} students</span>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <span v-if="course.is_free" class="text-xl font-bold text-green-600">Free</span>
                                        <span v-else class="text-xl font-bold text-gray-900">${{ course.price || '0' }}</span>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                    </div>

                    <!-- List View -->
                    <div v-else class="space-y-4">
                        <router-link v-for="course in courses" :key="course.id" :to="`/courses/${course.id}`" class="group">
                            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition duration-300">
                                <div class="flex">
                                    <div class="w-64 shrink-0">
                                        <div class="aspect-video relative overflow-hidden h-full">
                                            <img :src="course.thumbnail || '/images/course-placeholder.jpg'" :alt="course.title" 
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                            <div class="absolute top-3 left-3">
                                                <span v-if="course.is_featured" class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-full">Featured</span>
                                                <span v-if="course.is_free" class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded-full ml-1">Free</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-1 p-5">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition line-clamp-2">{{ course.title }}</h3>
                                                <p class="text-sm text-gray-500 mt-1">{{ course.instructor?.name }}</p>
                                                <div class="flex items-center gap-4 mt-3">
                                                    <div class="flex items-center gap-1">
                                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                        <span class="text-sm font-medium">{{ course.rating || '0.0' }}</span>
                                                    </div>
                                                    <span class="text-sm text-gray-500">{{ course.total_enrolled || 0 }} students</span>
                                                    <span class="text-sm text-gray-500">{{ course.total_lessons || 0 }} lessons</span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span v-if="course.is_free" class="text-xl font-bold text-green-600">Free</span>
                                                <span v-else class="text-xl font-bold text-gray-900">${{ course.price || '0' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                    </div>

                    <!-- Pagination -->
                    <div v-if="totalPages > 1" class="mt-12 flex justify-center gap-2">
                        <button v-for="page in totalPages" :key="page" @click="changePage(page)"
                            :class="page === currentPage ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition">
                            {{ page }}
                        </button>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Compare Modal -->
    <div v-if="showCompareModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showCompareModal = false">
        <div class="bg-white rounded-xl max-w-6xl w-full max-h-[90vh] overflow-auto">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
                <h2 class="text-xl font-bold text-gray-900">Compare Courses</h2>
                <button @click="showCompareModal = false" class="p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr>
                            <th class="text-left py-3 px-4 bg-gray-50 font-semibold text-gray-700 w-48"></th>
                            <th v-for="course in getCompareCourses()" :key="course.id" class="py-3 px-4 bg-gray-50 font-semibold text-gray-700">
                                <button @click="toggleCompare(course.id)" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-3 px-4 font-medium">Image</td>
                            <td v-for="course in getCompareCourses()" :key="course.id" class="py-3 px-4">
                                <img :src="course.thumbnail || '/images/course-placeholder.jpg'" class="w-32 h-20 object-cover rounded-lg">
                            </td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium">Title</td>
                            <td v-for="course in getCompareCourses()" :key="course.id" class="py-3 px-4">{{ course.title }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium">Instructor</td>
                            <td v-for="course in getCompareCourses()" :key="course.id" class="py-3 px-4">{{ course.instructor?.name }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium">Price</td>
                            <td v-for="course in getCompareCourses()" :key="course.id" class="py-3 px-4">
                                <span v-if="course.is_free" class="text-green-600 font-bold">Free</span>
                                <span v-else class="font-bold">${{ course.price }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium">Rating</td>
                            <td v-for="course in getCompareCourses()" :key="course.id" class="py-3 px-4 flex items-center gap-1">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ course.rating || '0.0' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium">Students</td>
                            <td v-for="course in getCompareCourses()" :key="course.id" class="py-3 px-4">{{ course.total_enrolled || 0 }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium">Duration</td>
                            <td v-for="course in getCompareCourses()" :key="course.id" class="py-3 px-4">{{ course.total_duration || '0h' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium">Lessons</td>
                            <td v-for="course in getCompareCourses()" :key="course.id" class="py-3 px-4">{{ course.total_lessons || 0 }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium">Level</td>
                            <td v-for="course in getCompareCourses()" :key="course.id" class="py-3 px-4">{{ course.level || 'All Levels' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium">Certificate</td>
                            <td v-for="course in getCompareCourses()" :key="course.id" class="py-3 px-4">{{ course.certificate ? 'Yes' : 'No' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-medium">Action</td>
                            <td v-for="course in getCompareCourses()" :key="course.id" class="py-3 px-4">
                                <router-link :to="`/courses/${course.id}`" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">View</router-link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { courseApi, categoryApi } from '@/services/api';

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const courses = ref([]);
const categories = ref([]);
const totalCourses = ref(0);
const currentPage = ref(1);
const totalPages = ref(1);
const viewMode = ref('grid');
const showCompareModal = ref(false);
const compareList = ref([]);
const wishlistItems = ref([]);
const cartItems = ref([]);

const filters = ref({
    search: route.query.search || '',
    category: route.query.category || '',
    price: '',
    level: '',
    language: '',
    rating: '',
    sort: 'newest',
    page: 1,
});

const priceOptions = [
    { value: '', label: 'All Prices' },
    { value: 'free', label: 'Free' },
    { value: 'paid', label: 'Paid' },
];

const levelOptions = [
    { value: '', label: 'All Levels' },
    { value: 'beginner', label: 'Beginner' },
    { value: 'intermediate', label: 'Intermediate' },
    { value: 'advanced', label: 'Advanced' },
];

async function fetchCourses() {
    loading.value = true;
    try {
        const params = { page: filters.value.page, per_page: 12 };
        if (filters.value.search) params.search = filters.value.search;
        if (filters.value.category) params.category_id = filters.value.category;
        if (filters.value.price === 'free') params.is_free = 1;
        if (filters.value.price === 'paid') params.is_free = 0;
        if (filters.value.level) params.level = filters.value.level;
        if (filters.value.language) params.language = filters.value.language;
        if (filters.value.rating) params.min_rating = filters.value.rating;
        if (filters.value.sort) params.sort = filters.value.sort;

        const { data } = await courseApi.getAll(params);
        courses.value = data.data || [];
        totalCourses.value = data.total || 0;
        currentPage.value = data.current_page || 1;
        totalPages.value = data.last_page || 1;
    } catch (error) {
        console.error('Error fetching courses:', error);
    } finally {
        loading.value = false;
    }
}

async function fetchCategories() {
    try {
        const { data } = await categoryApi.getAll();
        categories.value = data.data || [];
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
}

function applyFilters() {
    filters.value.page = 1;
    router.replace({ query: { ...filters.value } });
    fetchCourses();
}

function toggleCompare(courseId) {
    const index = compareList.value.indexOf(courseId);
    if (index > -1) {
        compareList.value.splice(index, 1);
    } else if (compareList.value.length < 4) {
        compareList.value.push(courseId);
    }
}

function isInCompareList(courseId) {
    return compareList.value.includes(courseId);
}

function toggleWishlist(courseId) {
    const index = wishlistItems.value.indexOf(courseId);
    if (index > -1) {
        wishlistItems.value.splice(index, 1);
    } else {
        wishlistItems.value.push(courseId);
    }
}

function toggleCart(courseId) {
    const index = cartItems.value.indexOf(courseId);
    if (index > -1) {
        cartItems.value.splice(index, 1);
    } else {
        cartItems.value.push(courseId);
    }
}

function getCompareCourses() {
    return courses.value.filter(c => compareList.value.includes(c.id));
}

function changePage(page) {
    filters.value.page = page;
    fetchCourses();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

onMounted(() => {
    fetchCourses();
    fetchCategories();
});
</script>