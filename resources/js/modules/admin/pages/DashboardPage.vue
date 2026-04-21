<template>
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ authStore.user?.name || 'Admin' }}!</h1>
                <p class="mt-1 text-gray-500">Here's what's happening with your platform today.</p>
            </div>
            <div class="flex items-center gap-3">
                <select v-model="period" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="7d">Last 7 days</option>
                    <option value="30d">Last 30 days</option>
                    <option value="90d">Last 90 days</option>
                    <option value="1y">Last year</option>
                </select>
                <button @click="refreshData" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Users -->
            <div class="group bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-lg hover:shadow-blue-500/5 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-1 text-sm" :class="stats.userGrowth >= 0 ? 'text-green-600' : 'text-red-600'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stats.userGrowth >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 10l-7 7m0 0l-7-7m7 7V3'" />
                        </svg>
                        {{ Math.abs(stats.userGrowth) }}%
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900">{{ formatNumber(stats.total_users) }}</p>
                </div>
                <div class="mt-4 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-500" style="width: 65%"></div>
                </div>
            </div>

            <!-- Total Courses -->
            <div class="group bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-lg hover:shadow-green-500/5 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-1 text-sm" :class="stats.courseGrowth >= 0 ? 'text-green-600' : 'text-red-600'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stats.courseGrowth >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 10l-7 7m0 0l-7-7m7 7V3'" />
                        </svg>
                        {{ Math.abs(stats.courseGrowth) }}%
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Courses</p>
                    <p class="text-3xl font-bold text-gray-900">{{ formatNumber(stats.total_courses) }}</p>
                </div>
                <div class="mt-4 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-green-500 to-emerald-600 rounded-full transition-all duration-500" style="width: 45%"></div>
                </div>
            </div>

            <!-- Total Enrollments -->
            <div class="group bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-lg hover:shadow-purple-500/5 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.241-.247 3.47 3.47 0 014.438 0 3.42 3.42 0 001.241.247 3.42 3.42 0 012.814.013 3.47 3.47 0 012.814.013 3.42 3.42 0 001.241.247c.85.215 1.525.85 1.741 1.764" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-1 text-sm" :class="stats.enrollmentGrowth >= 0 ? 'text-green-600' : 'text-red-600'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stats.enrollmentGrowth >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 10l-7 7m0 0l-7-7m7 7V3'" />
                        </svg>
                        {{ Math.abs(stats.enrollmentGrowth || 0) }}%
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Enrollments</p>
                    <p class="text-3xl font-bold text-gray-900">{{ formatNumber(stats.total_enrollments) }}</p>
                </div>
                <div class="mt-4 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-purple-500 to-indigo-600 rounded-full transition-all duration-500" style="width: 78%"></div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="group bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-lg hover:shadow-yellow-500/5 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg shadow-yellow-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 1v1m0-1H9m3 1h.01" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-1 text-sm" :class="stats.revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stats.revenueGrowth >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 10l-7 7m0 0l-7-7m7 7V3'" />
                        </svg>
                        {{ Math.abs(stats.revenueGrowth) }}%
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-900">${{ formatCurrency(stats.total_revenue) }}</p>
                </div>
                <div class="mt-4 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-yellow-500 to-orange-600 rounded-full transition-all duration-500" style="width: 92%"></div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Revenue Chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Revenue Overview</h2>
                        <p class="text-sm text-gray-500">Monthly revenue for the last 12 months</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="flex items-center gap-1.5 text-sm text-gray-500">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            Revenue
                        </span>
                    </div>
                </div>
                <div class="h-72">
                    <LineChart :labels="revenueChart.labels" :data="revenueChart.data" label="Revenue ($)" color="#3b82f6" />
                </div>
            </div>

            <!-- Enrollments by Category -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Enrollments by Category</h2>
                    <p class="text-sm text-gray-500">Distribution of enrollments</p>
                </div>
                <div class="h-56">
                    <DoughnutChart :labels="categoryChart.labels" :data="categoryChart.data" />
                </div>
                <div class="mt-4 space-y-2">
                    <div v-for="(label, index) in categoryChart.labels" :key="label" class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" :style="`background-color: ${chartColors[index % chartColors.length]}`"></span>
                            <span class="text-gray-600">{{ label }}</span>
                        </div>
                        <span class="font-medium text-gray-900">{{ categoryChart.data[index] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Top Performers -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Enrollments -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Recent Enrollments</h2>
                        <p class="text-sm text-gray-500">Latest student enrollments</p>
                    </div>
                    <router-link to="/admin/enrollments" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View all</router-link>
                </div>
                <div class="space-y-4">
                    <div v-for="enrollment in recentEnrollments" :key="enrollment.id" class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition">
                        <img :src="enrollment.student?.avatar || '/images/avatar.png'" class="w-12 h-12 rounded-xl object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ enrollment.student?.name || 'Student' }}</p>
                            <p class="text-sm text-gray-500 truncate">Enrolled in {{ enrollment.course?.title || 'Course' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">${{ enrollment.amount || 0 }}</p>
                            <p class="text-xs text-gray-500">{{ formatTimeAgo(enrollment.created_at) }}</p>
                        </div>
                    </div>
                    <div v-if="recentEnrollments.length === 0" class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-gray-500">No recent enrollments</p>
                    </div>
                </div>
            </div>

            <!-- Top Instructors -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Top Instructors</h2>
                        <p class="text-sm text-gray-500">Best performing instructors</p>
                    </div>
                    <router-link to="/admin/instructors" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View all</router-link>
                </div>
                <div class="space-y-4">
                    <div v-for="(instructor, index) in topInstructors" :key="instructor.id" class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition">
                        <div class="relative">
                            <img :src="instructor.avatar || '/images/avatar.png'" class="w-12 h-12 rounded-xl object-cover">
                            <div v-if="index < 3" class="absolute -top-2 -right-2 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white"
                                 :class="index === 0 ? 'bg-yellow-500' : index === 1 ? 'bg-gray-400' : 'bg-amber-600'">
                                {{ index + 1 }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ instructor.name }}</p>
                            <p class="text-sm text-gray-500">{{ instructor.total_students || 0 }} students</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">${{ formatCurrency(instructor.total_earnings) }}</p>
                            <div class="flex items-center gap-1 text-xs text-yellow-500">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                {{ instructor.rating || '0.0' }}
                            </div>
                        </div>
                    </div>
                    <div v-if="topInstructors.length === 0" class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <p class="text-gray-500">No instructor data</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Pending Items -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white">
                <h2 class="text-lg font-bold mb-4">Quick Actions</h2>
                <div class="space-y-3">
                    <router-link to="/admin/courses/create" class="flex items-center gap-3 p-3 bg-white/10 rounded-xl hover:bg-white/20 transition">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <span class="font-medium">Add New Course</span>
                    </router-link>
                    <router-link to="/admin/users/create" class="flex items-center gap-3 p-3 bg-white/10 rounded-xl hover:bg-white/20 transition">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <span class="font-medium">Add New User</span>
                    </router-link>
                    <router-link to="/admin/coupons/create" class="flex items-center gap-3 p-3 bg-white/10 rounded-xl hover:bg-white/20 transition">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <span class="font-medium">Create Coupon</span>
                    </router-link>
                    <router-link to="/admin/reports" class="flex items-center gap-3 p-3 bg-white/10 rounded-xl hover:bg-white/20 transition">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="font-medium">View Reports</span>
                    </router-link>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Pending Approvals</h2>
                    <span class="px-2.5 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">
                        {{ pendingApprovals.total || 0 }}
                    </span>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Course Approvals</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ pendingApprovals.courses || 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Instructor Applications</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ pendingApprovals.instructors || 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Payout Requests</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ pendingApprovals.payouts || 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Support Tickets</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ pendingApprovals.tickets || 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">System Status</h2>
                    <span class="flex items-center gap-1.5 text-sm text-green-600">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        All Systems Operational
                    </span>
                </div>
                <div class="space-y-4">
                    <div v-for="service in systemStatus" :key="service.name" class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full" :class="service.status === 'online' ? 'bg-green-500' : 'bg-red-500'"></div>
                            <span class="text-sm text-gray-700">{{ service.name }}</span>
                        </div>
                        <span class="text-xs font-medium" :class="service.status === 'online' ? 'text-green-600' : 'text-red-600'">
                            {{ service.status === 'online' ? 'Online' : 'Offline' }}
                        </span>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">API Response Time</span>
                        <span class="font-medium text-gray-900">{{ systemStats.apiResponse }}ms</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-2">
                        <span class="text-gray-500">Server Uptime</span>
                        <span class="font-medium text-gray-900">{{ systemStats.uptime }}%</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-2">
                        <span class="text-gray-500">Storage Used</span>
                        <span class="font-medium text-gray-900">{{ systemStats.storageUsed }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useAuthStore } from '@/stores/auth';
import LineChart from '@/components/ui/LineChart.vue';
import BarChart from '@/components/ui/BarChart.vue';
import DoughnutChart from '@/components/ui/DoughnutChart.vue';

const authStore = useAuthStore();
const period = ref('30d');
const loading = ref(true);

const chartColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

const stats = ref({
    total_users: 1250,
    total_courses: 348,
    total_enrollments: 4820,
    total_revenue: 156780,
    userGrowth: 12.5,
    courseGrowth: 8.3,
    enrollmentGrowth: 15.2,
    revenueGrowth: 23.8,
});

const pendingApprovals = ref({
    total: 24,
    courses: 8,
    instructors: 5,
    payouts: 3,
    tickets: 8,
});

const systemStats = ref({
    apiResponse: 45,
    uptime: 99.9,
    storageUsed: '2.4 GB / 10 GB',
});

const systemStatus = ref([
    { name: 'Database', status: 'online' },
    { name: 'API Server', status: 'online' },
    { name: 'File Storage', status: 'online' },
    { name: 'Email Service', status: 'online' },
    { name: 'Payment Gateway', status: 'online' },
]);

const recentEnrollments = ref([
    { id: 1, student: { name: 'Sarah Johnson', avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100' }, course: { title: 'React Masterclass' }, amount: 99, created_at: '2026-04-20T10:30:00Z' },
    { id: 2, student: { name: 'Michael Chen', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100' }, course: { title: 'Python for Data Science' }, amount: 149, created_at: '2026-04-20T09:15:00Z' },
    { id: 3, student: { name: 'Emily Davis', avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100' }, course: { title: 'UI/UX Design Fundamentals' }, amount: 79, created_at: '2026-04-20T08:45:00Z' },
    { id: 4, student: { name: 'David Wilson', avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100' }, course: { title: 'Machine Learning A-Z' }, amount: 199, created_at: '2026-04-19T16:20:00Z' },
    { id: 5, student: { name: 'Jessica Brown', avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100' }, course: { title: 'Digital Marketing 2026' }, amount: 89, created_at: '2026-04-19T14:30:00Z' },
]);

const topInstructors = ref([
    { id: 1, name: 'John Smith', avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100', total_students: 12450, total_earnings: 124500, rating: 4.9 },
    { id: 2, name: 'Lisa Anderson', avatar: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=100', total_students: 8920, total_earnings: 89200, rating: 4.8 },
    { id: 3, name: 'Robert Taylor', avatar: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100', total_students: 7650, total_earnings: 76500, rating: 4.7 },
    { id: 4, name: 'Amanda White', avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100', total_students: 5430, total_earnings: 54300, rating: 4.9 },
]);

const revenueChart = ref({
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    data: [12500, 15800, 18200, 21400, 19800, 24500, 28200, 25600, 28900, 31200, 28400, 35600],
});

const categoryChart = ref({
    labels: ['Development', 'Business', 'Design', 'Marketing', 'Data Science', 'Other'],
    data: [35, 20, 18, 12, 10, 5],
});

function formatNumber(num) {
    if (!num) return '0';
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
}

function formatCurrency(num) {
    if (!num) return '0';
    if (num >= 1000000) return (num / 1000000).toFixed(2) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toLocaleString();
}

function formatTimeAgo(date) {
    if (!date) return '';
    const now = new Date();
    const past = new Date(date);
    const diff = Math.floor((now - past) / 1000);
    
    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + ' min ago';
    if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
    return Math.floor(diff / 86400) + ' days ago';
}

async function refreshData() {
    loading.value = true;
    await new Promise(resolve => setTimeout(resolve, 1000));
    loading.value = false;
}

onMounted(() => {
    loading.value = false;
});

watch(period, () => {
    refreshData();
});
</script>