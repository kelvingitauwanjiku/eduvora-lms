<template>
  <Teleport to="body">
    <!-- Mobile Overlay -->
    <transition name="fade">
      <div v-if="mobileOpen" class="sidebar-overlay" @click="$emit('close')"></div>
    </transition>
  </Teleport>

  <aside 
    class="admin-sidebar" 
    :class="{ 
      'collapsed': collapsed, 
      'mobile-open': mobileOpen 
    }"
    @keydown.esc="$emit('close')"
    tabindex="-1"
    ref="sidebarRef"
  >
    <!-- Logo -->
    <div class="sidebar-logo-area">
      <router-link to="/" class="sidebar-logos" @click="$emit('close')">
        <img class="sidebar-logo-lg" height="50" :src="darkLogo" :alt="appName">
        <img class="sidebar-logo-sm" height="40" :src="favicon" :alt="appName">
      </router-link>
      <button class="sidebar-close-btn" @click="$emit('close')" :title="get_phrase('Close')">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Collapse Toggle Button -->
    <button 
      class="sidebar-collapse-btn" 
      @click="$emit('toggle')"
      :title="collapsed ? get_phrase('Expand') : get_phrase('Collapse')"
    >
      <svg 
        class="w-4 h-4 transition-transform duration-300" 
        :class="{ 'rotate-180': collapsed }"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
      </svg>
    </button>

    <!-- Main Content -->
    <div class="sidebar-content">
      <h3 class="sidebar-title">{{ get_phrase('Main Menu') }}</h3>
      
      <div class="sidebar-nav-area" ref="navAreaRef">
        <nav class="sidebar-nav" role="navigation" aria-label="Main navigation">
          <ul>
            <!-- Dashboard -->
            <li class="sidebar-item" :class="{ 'active': isActiveRoute('admin.dashboard') }">
              <router-link :to="{ name: 'admin.dashboard' }" class="sidebar-link" @click="handleLinkClick">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Dashboard') }}</span>
              </router-link>
            </li>

            <!-- Category -->
            <li class="sidebar-item" :class="{ 'active': isActiveRoute('admin.categories') }">
              <router-link :to="{ name: 'admin.categories' }" class="sidebar-link" @click="handleLinkClick">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Category') }}</span>
              </router-link>
            </li>

            <!-- Course (Dropdown) -->
            <li class="sidebar-item sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.courses', 'admin.course.', 'admin.coupons', 'admin.course.bundles', 'admin.course_bundle.']), 'open': openDropdown === 'course' }">
              <a href="javascript:void(0);" class="sidebar-link sidebar-dropdown-toggle" @click.prevent="toggleDropdown('course')">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Course') }}</span>
                <span class="sidebar-arrow">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </a>
              <transition name="slide">
                <ul v-show="openDropdown === 'course'" class="sidebar-submenu">
                  <li class="sidebar-submenu-title">{{ get_phrase('Course') }}</li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.courses') }">
                    <router-link :to="{ name: 'admin.courses' }" @click="handleLinkClick">{{ get_phrase('Manage Courses') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.course.create') }">
                    <router-link :to="{ name: 'admin.course.create' }" @click="handleLinkClick">{{ get_phrase('Add New Course') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.coupons') }">
                    <router-link :to="{ name: 'admin.coupons' }" @click="handleLinkClick">{{ get_phrase('Coupons') }}</router-link>
                  </li>
                  <!-- Course Bundle Submenu -->
                  <li class="sidebar-subitem sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.course.bundles', 'admin.course_bundle.']), 'open': openSubDropdown === 'courseBundle' }">
                    <a href="javascript:void(0);" class="sidebar-dropdown-toggle" @click.prevent="toggleSubDropdown('courseBundle')">
                      {{ get_phrase('Course Bundle') }}
                      <span class="sidebar-arrow">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                      </span>
                    </a>
                    <transition name="slide">
                      <ul v-show="openSubDropdown === 'courseBundle'" class="sidebar-submenu-level-2">
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.course.bundles') }">
                          <router-link :to="{ name: 'admin.course.bundles' }" @click="handleLinkClick">{{ get_phrase('Manage Bundle') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.course.bundle.create') }">
                          <router-link :to="{ name: 'admin.course.bundle.create' }" @click="handleLinkClick">{{ get_phrase('Add New Bundle') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.course_bundle.admin_revenue') }">
                          <router-link :to="{ name: 'admin.course_bundle.admin_revenue' }" @click="handleLinkClick">{{ get_phrase('Admin Revenue') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.course_bundle.instructor_revenue') }">
                          <router-link :to="{ name: 'admin.course_bundle.instructor_revenue' }" @click="handleLinkClick">{{ get_phrase('Instructor Revenue') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.course.bundle.subscription.report') }">
                          <router-link :to="{ name: 'admin.course.bundle.subscription.report' }" @click="handleLinkClick">{{ get_phrase('Subscription Report') }}</router-link>
                        </li>
                      </ul>
                    </transition>
                  </li>
                </ul>
              </transition>
            </li>

            <!-- Bootcamp -->
            <li class="sidebar-item sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.bootcamps', 'admin.bootcamp.']), 'open': openDropdown === 'bootcamp' }">
              <a href="javascript:void(0);" class="sidebar-link sidebar-dropdown-toggle" @click.prevent="toggleDropdown('bootcamp')">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a4 4 0 11-8 0 4 4 0 018 0zM7 8a3 3 0 100-6 3 3 0 000 6z" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Bootcamp') }}</span>
                <span class="sidebar-arrow">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </a>
              <transition name="slide">
                <ul v-show="openDropdown === 'bootcamp'" class="sidebar-submenu">
                  <li class="sidebar-submenu-title">{{ get_phrase('Bootcamp') }}</li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.bootcamps') }">
                    <router-link :to="{ name: 'admin.bootcamps' }" @click="handleLinkClick">{{ get_phrase('Manage Bootcamps') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.bootcamp.create') }">
                    <router-link :to="{ name: 'admin.bootcamp.create' }" @click="handleLinkClick">{{ get_phrase('Add New Bootcamp') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.bootcamp.purchase.history') }">
                    <router-link :to="{ name: 'admin.bootcamp.purchase.history' }" @click="handleLinkClick">{{ get_phrase('Purchase History') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.bootcamp.categories') }">
                    <router-link :to="{ name: 'admin.bootcamp.categories' }" @click="handleLinkClick">{{ get_phrase('Category') }}</router-link>
                  </li>
                </ul>
              </transition>
            </li>

            <!-- Tutor Booking -->
            <li class="sidebar-item sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.tutor_', 'admin.tutor_subjects']), 'open': openDropdown === 'tutor' }">
              <a href="javascript:void(0);" class="sidebar-link sidebar-dropdown-toggle" @click.prevent="toggleDropdown('tutor')">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Tutor Booking') }}</span>
                <span class="sidebar-arrow">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </a>
              <transition name="slide">
                <ul v-show="openDropdown === 'tutor'" class="sidebar-submenu">
                  <li class="sidebar-submenu-title">{{ get_phrase('Tutor Booking') }}</li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.tutor_subjects') }">
                    <router-link :to="{ name: 'admin.tutor_subjects' }" @click="handleLinkClick">{{ get_phrase('Subjects') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.tutor_categories') }">
                    <router-link :to="{ name: 'admin.tutor_categories' }" @click="handleLinkClick">{{ get_phrase('Subject Category') }}</router-link>
                  </li>
                </ul>
              </transition>
            </li>

            <!-- Ebook -->
            <li class="sidebar-item sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.ebooks', 'admin.ebook.']), 'open': openDropdown === 'ebook' }">
              <a href="javascript:void(0);" class="sidebar-link sidebar-dropdown-toggle" @click.prevent="toggleDropdown('ebook')">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Ebook') }}</span>
                <span class="sidebar-arrow">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </a>
              <transition name="slide">
                <ul v-show="openDropdown === 'ebook'" class="sidebar-submenu">
                  <li class="sidebar-submenu-title">{{ get_phrase('Ebook') }}</li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.ebooks') }">
                    <router-link :to="{ name: 'admin.ebooks' }" @click="handleLinkClick">{{ get_phrase('Manage Ebooks') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.ebook.create') }">
                    <router-link :to="{ name: 'admin.ebook.create' }" @click="handleLinkClick">{{ get_phrase('Add New Ebook') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.ebook.admin-revenue') }">
                    <router-link :to="{ name: 'admin.ebook.admin-revenue' }" @click="handleLinkClick">{{ get_phrase('Admin revenue') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.ebook.instructor-revenue') }">
                    <router-link :to="{ name: 'admin.ebook.instructor-revenue' }" @click="handleLinkClick">{{ get_phrase('Instructor revenue') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.ebook.categories') }">
                    <router-link :to="{ name: 'admin.ebook.categories' }" @click="handleLinkClick">{{ get_phrase('Category') }}</router-link>
                  </li>
                </ul>
              </transition>
            </li>

            <!-- Student Enrollment -->
            <li class="sidebar-item sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.student.enroll', 'admin.enroll.history']), 'open': openDropdown === 'enrollment' }">
              <a href="javascript:void(0);" class="sidebar-link sidebar-dropdown-toggle" @click.prevent="toggleDropdown('enrollment')">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a4 4 0 014-4h4a4 4 0 014 4v2H3v-2z" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Student enrollment') }}</span>
                <span class="sidebar-arrow">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </a>
              <transition name="slide">
                <ul v-show="openDropdown === 'enrollment'" class="sidebar-submenu">
                  <li class="sidebar-submenu-title">{{ get_phrase('Course enrollment') }}</li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.enroll.history') }">
                    <router-link :to="{ name: 'admin.enroll.history' }" @click="handleLinkClick">{{ get_phrase('Enrollment History') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.student.enroll') }">
                    <router-link :to="{ name: 'admin.student.enroll' }" @click="handleLinkClick">{{ get_phrase('Enroll student') }}</router-link>
                  </li>
                </ul>
              </transition>
            </li>

            <!-- Payment Report -->
            <li class="sidebar-item sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.offline.payments', 'admin.revenue', 'admin.purchase.history']), 'open': openDropdown === 'payment' }">
              <a href="javascript:void(0);" class="sidebar-link sidebar-dropdown-toggle" @click.prevent="toggleDropdown('payment')">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2 2 1.343 2 3 2m0-8c1.11 0 2.08.402 2.599 1M5 10v3a3 3 0 003 3v0a3 3 0 003-3v-3m0 8a3 3 0 003 3v0a3 3 0 003-3v-3" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Payment Report') }}</span>
                <span class="sidebar-arrow">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </a>
              <transition name="slide">
                <ul v-show="openDropdown === 'payment'" class="sidebar-submenu">
                  <li class="sidebar-submenu-title">{{ get_phrase('Payment Report') }}</li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.offline.payments') }">
                    <router-link :to="{ name: 'admin.offline.payments' }" @click="handleLinkClick">{{ get_phrase('Offline payments') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.revenue') }">
                    <router-link :to="{ name: 'admin.revenue' }" @click="handleLinkClick">{{ get_phrase('Admin Revenue') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.instructor.revenue') }">
                    <router-link :to="{ name: 'admin.instructor.revenue' }" @click="handleLinkClick">{{ get_phrase('Instructor Revenue') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.purchase.history') }">
                    <router-link :to="{ name: 'admin.purchase.history' }" @click="handleLinkClick">{{ get_phrase('Payment History') }}</router-link>
                  </li>
                </ul>
              </transition>
            </li>

            <!-- Users -->
            <li class="sidebar-item sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.instructor.', 'admin.admins.', 'admin.student.']), 'open': openDropdown === 'users' }">
              <a href="javascript:void(0);" class="sidebar-link sidebar-dropdown-toggle" @click.prevent="toggleDropdown('users')">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Users') }}</span>
                <span class="sidebar-arrow">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </a>
              <transition name="slide">
                <ul v-show="openDropdown === 'users'" class="sidebar-submenu">
                  <li class="sidebar-submenu-title">{{ get_phrase('Users') }}</li>
                  <!-- Admin -->
                  <li class="sidebar-subitem sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.admins.']), 'open': openSubDropdown === 'adminUsers' }">
                    <a href="javascript:void(0);" class="sidebar-dropdown-toggle" @click.prevent="toggleSubDropdown('adminUsers')">
                      {{ get_phrase('Admin') }}
                      <span class="sidebar-arrow">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                      </span>
                    </a>
                    <transition name="slide">
                      <ul v-show="openSubDropdown === 'adminUsers'" class="sidebar-submenu-level-2">
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.admins.index') }">
                          <router-link :to="{ name: 'admin.admins.index' }" @click="handleLinkClick">{{ get_phrase('Manage Admin') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.admins.create') }">
                          <router-link :to="{ name: 'admin.admins.create' }" @click="handleLinkClick">{{ get_phrase('Add New Admin') }}</router-link>
                        </li>
                      </ul>
                    </transition>
                  </li>
                  <!-- Instructor -->
                  <li class="sidebar-subitem sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.instructor.']), 'open': openSubDropdown === 'instructors' }">
                    <a href="javascript:void(0);" class="sidebar-dropdown-toggle" @click.prevent="toggleSubDropdown('instructors')">
                      {{ get_phrase('Instructor') }}
                      <span class="sidebar-arrow">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                      </span>
                    </a>
                    <transition name="slide">
                      <ul v-show="openSubDropdown === 'instructors'" class="sidebar-submenu-level-2">
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.instructor.index') }">
                          <router-link :to="{ name: 'admin.instructor.index' }" @click="handleLinkClick">{{ get_phrase('Manage Instructors') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.instructor.create') }">
                          <router-link :to="{ name: 'admin.instructor.create' }" @click="handleLinkClick">{{ get_phrase('Add new Instructor') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.instructor.payout') }">
                          <router-link :to="{ name: 'admin.instructor.payout' }" @click="handleLinkClick">{{ get_phrase('Instructor Payout') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.instructor.setting') }">
                          <router-link :to="{ name: 'admin.instructor.setting' }" @click="handleLinkClick">{{ get_phrase('Instructor Setting') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.instructor.application') }">
                          <router-link :to="{ name: 'admin.instructor.application' }" @click="handleLinkClick">{{ get_phrase('Application') }}</router-link>
                        </li>
                      </ul>
                    </transition>
                  </li>
                  <!-- Student -->
                  <li class="sidebar-subitem sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.student.']), 'open': openSubDropdown === 'students' }">
                    <a href="javascript:void(0);" class="sidebar-dropdown-toggle" @click.prevent="toggleSubDropdown('students')">
                      {{ get_phrase('Student') }}
                      <span class="sidebar-arrow">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                      </span>
                    </a>
                    <transition name="slide">
                      <ul v-show="openSubDropdown === 'students'" class="sidebar-submenu-level-2">
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.student.index') }">
                          <router-link :to="{ name: 'admin.student.index' }" @click="handleLinkClick">{{ get_phrase('Manage Students') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.student.create') }">
                          <router-link :to="{ name: 'admin.student.create' }" @click="handleLinkClick">{{ get_phrase('Add new Student') }}</router-link>
                        </li>
                      </ul>
                    </transition>
                  </li>
                </ul>
              </transition>
            </li>

            <!-- Message -->
            <li class="sidebar-item" :class="{ 'active': isActiveRoute('admin.message') }">
              <router-link :to="{ name: 'admin.message' }" class="sidebar-link" @click="handleLinkClick">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-9s-9-3.582-9-8 9 3.582 9 8 9z" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Message') }}</span>
                <span v-if="unreadMessages > 0" class="sidebar-badge">{{ unreadMessages }}</span>
              </router-link>
            </li>

            <!-- Newsletter -->
            <li class="sidebar-item sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.newsletter', 'admin.subscribed_user']), 'open': openDropdown === 'newsletter' }">
              <a href="javascript:void(0);" class="sidebar-link sidebar-dropdown-toggle" @click.prevent="toggleDropdown('newsletter')">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Newsletter') }}</span>
                <span class="sidebar-arrow">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </a>
              <transition name="slide">
                <ul v-show="openDropdown === 'newsletter'" class="sidebar-submenu">
                  <li class="sidebar-submenu-title">{{ get_phrase('Newsletter') }}</li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.newsletter') }">
                    <router-link :to="{ name: 'admin.newsletter' }" @click="handleLinkClick">{{ get_phrase('Manage Newsletters') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.subscribed_user') }">
                    <router-link :to="{ name: 'admin.subscribed_user' }" @click="handleLinkClick">{{ get_phrase('Subscribed User') }}</router-link>
                  </li>
                </ul>
              </transition>
            </li>

            <!-- Contacts -->
            <li class="sidebar-item" :class="{ 'active': isActiveRoute('admin.contacts') }">
              <router-link :to="{ name: 'admin.contacts' }" class="sidebar-link" @click="handleLinkClick">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.716 3 19a2 2 0 012 2v1a1 1 0 001 1v1a1 1 0 001-1V7.414a1 1 0 01.293-.707l5.414-5.414a1 1 0 01.293-.707H19a2 2 0 012 2v1a1 1 0 001 1v1a1 1 0 01-1 1h-1z" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Contacts') }}</span>
              </router-link>
            </li>

            <!-- Customer Support -->
            <li class="sidebar-item sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.customer.support.']), 'open': openDropdown === 'support' }">
              <a href="javascript:void(0);" class="sidebar-link sidebar-dropdown-toggle" @click.prevent="toggleDropdown('support')">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.657l3.536 3.536M9.879 16.121l-3.536 3.536m0 5.657l3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 100-8 4 4 0 000 8z" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Customer Support') }}</span>
                <span class="sidebar-arrow">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </a>
              <transition name="slide">
                <ul v-show="openDropdown === 'support'" class="sidebar-submenu">
                  <li class="sidebar-submenu-title">{{ get_phrase('Customer Support') }}</li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.customer.support.ticket.index') }">
                    <router-link :to="{ name: 'admin.customer.support.ticket.index' }" @click="handleLinkClick">{{ get_phrase('Tickets') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.customer.support.ticket.create') }">
                    <router-link :to="{ name: 'admin.customer.support.ticket.create' }" @click="handleLinkClick">{{ get_phrase('Add New Ticket') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.customer.support.ticket.faq.index') }">
                    <router-link :to="{ name: 'admin.customer.support.ticket.faq.index' }" @click="handleLinkClick">{{ get_phrase('FAQ') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.customer.support.ticket.report') }">
                    <router-link :to="{ name: 'admin.customer.support.ticket.report' }" @click="handleLinkClick">{{ get_phrase('Reports') }}</router-link>
                  </li>
                  <li class="sidebar-subitem sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.customer.support.ticket.macro.', 'admin.customer.support.ticket.category.', 'admin.customer.support.ticket.priority.', 'admin.customer.support.ticket.status.']), 'open': openSubDropdown === 'supportSettings' }">
                    <a href="javascript:void(0);" class="sidebar-dropdown-toggle" @click.prevent="toggleSubDropdown('supportSettings')">
                      {{ get_phrase('Settings') }}
                      <span class="sidebar-arrow">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                      </span>
                    </a>
                    <transition name="slide">
                      <ul v-show="openSubDropdown === 'supportSettings'" class="sidebar-submenu-level-2">
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.customer.support.ticket.macro.index') }">
                          <router-link :to="{ name: 'admin.customer.support.ticket.macro.index' }" @click="handleLinkClick">{{ get_phrase('Ticket Macros') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.customer.support.ticket.category.index') }">
                          <router-link :to="{ name: 'admin.customer.support.ticket.category.index' }" @click="handleLinkClick">{{ get_phrase('Ticket Categories') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.customer.support.ticket.priority.index') }">
                          <router-link :to="{ name: 'admin.customer.support.ticket.priority.index' }" @click="handleLinkClick">{{ get_phrase('Ticket Priorities') }}</router-link>
                        </li>
                        <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.customer.support.ticket.status.index') }">
                          <router-link :to="{ name: 'admin.customer.support.ticket.status.index' }" @click="handleLinkClick">{{ get_phrase('Ticket Status') }}</router-link>
                        </li>
                      </ul>
                    </transition>
                  </li>
                </ul>
              </transition>
            </li>

            <!-- Blogs -->
            <li class="sidebar-item sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.blogs', 'admin.blog.']), 'open': openDropdown === 'blogs' }">
              <a href="javascript:void(0);" class="sidebar-link sidebar-dropdown-toggle" @click.prevent="toggleDropdown('blogs')">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Blogs') }}</span>
                <span class="sidebar-arrow">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </a>
              <transition name="slide">
                <ul v-show="openDropdown === 'blogs'" class="sidebar-submenu">
                  <li class="sidebar-submenu-title">{{ get_phrase('Blogs') }}</li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.blogs') }">
                    <router-link :to="{ name: 'admin.blogs' }" @click="handleLinkClick">{{ get_phrase('Manage Blogs') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.blog.pending') }">
                    <router-link :to="{ name: 'admin.blog.pending' }" @click="handleLinkClick">{{ get_phrase('Pending Blogs') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.blog.category') }">
                    <router-link :to="{ name: 'admin.blog.category' }" @click="handleLinkClick">{{ get_phrase('Category') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.blog.settings') }">
                    <router-link :to="{ name: 'admin.blog.settings' }" @click="handleLinkClick">{{ get_phrase('Settings') }}</router-link>
                  </li>
                </ul>
              </transition>
            </li>

            <!-- Knowledge Base -->
            <li class="sidebar-item" :class="{ 'active': hasActiveChild(['admin.knowledge.base', 'admin.articles']) }">
              <router-link :to="{ name: 'admin.knowledge.base' }" class="sidebar-link" @click="handleLinkClick">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Knowledge Base') }}</span>
              </router-link>
            </li>
          </ul>
        </nav>

        <!-- Settings Section -->
        <nav class="sidebar-nav">
          <h3 class="sidebar-title">{{ get_phrase('Settings') }}</h3>
          <ul>
            <li class="sidebar-item sidebar-dropdown" :class="{ 'active': hasActiveChild(['admin.system.settings', 'admin.website.settings', 'admin.payment.settings']), 'open': openDropdown === 'settings' }">
              <a href="javascript:void(0);" class="sidebar-link sidebar-dropdown-toggle" @click.prevent="toggleDropdown('settings')">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('System Settings') }}</span>
                <span class="sidebar-arrow">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </a>
              <transition name="slide">
                <ul v-show="openDropdown === 'settings'" class="sidebar-submenu">
                  <li class="sidebar-submenu-title">{{ get_phrase('System Settings') }}</li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.system.settings') }">
                    <router-link :to="{ name: 'admin.system.settings' }" @click="handleLinkClick">{{ get_phrase('System Settings') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.website.settings') }">
                    <router-link :to="{ name: 'admin.website.settings' }" @click="handleLinkClick">{{ get_phrase('Website Settings') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.payment.settings') }">
                    <router-link :to="{ name: 'admin.payment.settings' }" @click="handleLinkClick">{{ get_phrase('Payment Settings') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.live.class.settings') }">
                    <router-link :to="{ name: 'admin.live.class.settings' }" @click="handleLinkClick">{{ get_phrase('Live Class Settings') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.notification.settings') }">
                    <router-link :to="{ name: 'admin.notification.settings' }" @click="handleLinkClick">{{ get_phrase('SMTP Settings') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.amazom_s3.settings') }">
                    <router-link :to="{ name: 'admin.amazom_s3.settings' }" @click="handleLinkClick">{{ get_phrase('Amazon S3 Settings') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.certificate.settings') }">
                    <router-link :to="{ name: 'admin.certificate.settings' }" @click="handleLinkClick">{{ get_phrase('Certificate Settings') }}</router-link>
                  </li>
                  <li class="sidebar-subitem" :class="{ 'active': isActiveRoute('admin.player.settings') }">
                    <router-link :to="{ name: 'admin.player.settings' }" @click="handleLinkClick">{{ get_phrase('Player Settings') }}</router-link>
                  </li>
                </ul>
              </transition>
            </li>

            <!-- Manage Profile -->
            <li class="sidebar-item" :class="{ 'active': isActiveRoute('admin.manage.profile') }">
              <router-link :to="{ name: 'admin.manage.profile' }" class="sidebar-link" @click="handleLinkClick">
                <span class="sidebar-icon">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </span>
                <span class="sidebar-text">{{ get_phrase('Manage Profile') }}</span>
              </router-link>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { useRoute } from 'vue-router';

const props = defineProps({
  collapsed: {
    type: Boolean,
    default: false
  },
  mobileOpen: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['toggle', 'close']);

const route = useRoute();
const navAreaRef = ref(null);
const sidebarRef = ref(null);

const openDropdown = ref(null);
const openSubDropdown = ref(null);
const unreadMessages = ref(0);

const darkLogo = ref('/images/logo-dark.png');
const favicon = ref('/images/favicon.png');
const appName = ref('Eduvora');

onMounted(() => {
  restoreScrollPosition();
  autoExpandOnRoute();
});

watch(() => route.name, () => {
  nextTick(() => {
    autoExpandOnRoute();
    scrollToActive();
  });
});

function autoExpandOnRoute() {
  if (!route.name) return;
  
  const dropdowns = ['course', 'bootcamp', 'tutor', 'ebook', 'enrollment', 'payment', 'users', 'newsletter', 'support', 'blogs', 'settings'];
  const subDropdowns = ['courseBundle', 'adminUsers', 'instructors', 'students', 'supportSettings'];
  
  for (const dropdown of dropdowns) {
    const paths = getDropdownPaths(dropdown);
    if (paths.some(p => route.name === p || (p && route.name?.startsWith(p + '.')))) {
      openDropdown.value = dropdown;
      break;
    }
  }
  
  for (const subDropdown of subDropdowns) {
    const paths = getSubDropdownPaths(subDropdown);
    if (paths.some(p => route.name === p || (p && route.name?.startsWith(p + '.')))) {
      openSubDropdown.value = subDropdown;
      break;
    }
  }
}

function getDropdownPaths(dropdown) {
  const paths = {
    course: ['admin.courses', 'admin.course', 'admin.coupons', 'admin.course.bundles', 'admin.course_bundle'],
    bootcamp: ['admin.bootcamps', 'admin.bootcamp'],
    tutor: ['admin.tutor_subjects', 'admin.tutor_categories'],
    ebook: ['admin.ebooks', 'admin.ebook'],
    enrollment: ['admin.student.enroll', 'admin.enroll.history'],
    payment: ['admin.offline.payments', 'admin.revenue', 'admin.purchase.history'],
    users: ['admin.instructor', 'admin.admins', 'admin.student'],
    newsletter: ['admin.newsletter', 'admin.subscribed_user'],
    support: ['admin.customer.support'],
    blogs: ['admin.blogs', 'admin.blog'],
    settings: ['admin.system.settings', 'admin.website.settings', 'admin.payment.settings']
  };
  return paths[dropdown] || [];
}

function getSubDropdownPaths(subDropdown) {
  const paths = {
    courseBundle: ['admin.course.bundles', 'admin.course_bundle'],
    adminUsers: ['admin.admins'],
    instructors: ['admin.instructor'],
    students: ['admin.student'],
    supportSettings: ['admin.customer.support.ticket.macro', 'admin.customer.support.ticket.category', 'admin.customer.support.ticket.priority', 'admin.customer.support.ticket.status']
  };
  return paths[subDropdown] || [];
}

function scrollToActive() {
  nextTick(() => {
    const activeEl = document.querySelector('.sidebar-item.active, .sidebar-subitem.active');
    if (activeEl) {
      activeEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
}

function handleLinkClick() {
  if (window.innerWidth < 1024) {
    emit('close');
  }
}

function get_phrase(key) {
  const phrases = {
    'Main Menu': 'Main Menu',
    'Dashboard': 'Dashboard',
    'Category': 'Category',
    'Course': 'Course',
    'Manage Courses': 'Manage Courses',
    'Add New Course': 'Add New Course',
    'Coupons': 'Coupons',
    'Course Bundle': 'Course Bundle',
    'Manage Bundle': 'Manage Bundle',
    'Add New Bundle': 'Add New Bundle',
    'Admin Revenue': 'Admin Revenue',
    'Instructor Revenue': 'Instructor Revenue',
    'Subscription Report': 'Subscription Report',
    'Bootcamp': 'Bootcamp',
    'Manage Bootcamps': 'Manage Bootcamps',
    'Add New Bootcamp': 'Add New Bootcamp',
    'Purchase History': 'Purchase History',
    'Tutor Booking': 'Tutor Booking',
    'Subjects': 'Subjects',
    'Subject Category': 'Subject Category',
    'Ebook': 'Ebook',
    'Manage Ebooks': 'Manage Ebooks',
    'Add New Ebook': 'Add New Ebook',
    'Admin revenue': 'Admin revenue',
    'Instructor revenue': 'Instructor revenue',
    'Student enrollment': 'Student enrollment',
    'Course enrollment': 'Course enrollment',
    'Enrollment History': 'Enrollment History',
    'Enroll student': 'Enroll student',
    'Payment Report': 'Payment Report',
    'Offline payments': 'Offline payments',
    'Payment History': 'Payment History',
    'Users': 'Users',
    'Admin': 'Admin',
    'Manage Admin': 'Manage Admin',
    'Add New Admin': 'Add New Admin',
    'Instructor': 'Instructor',
    'Manage Instructors': 'Manage Instructors',
    'Add new Instructor': 'Add new Instructor',
    'Instructor Payout': 'Instructor Payout',
    'Instructor Setting': 'Instructor Setting',
    'Application': 'Application',
    'Student': 'Student',
    'Manage Students': 'Manage Students',
    'Add new Student': 'Add new Student',
    'Message': 'Message',
    'Newsletter': 'Newsletter',
    'Manage Newsletters': 'Manage Newsletters',
    'Subscribed User': 'Subscribed User',
    'Contacts': 'Contacts',
    'Customer Support': 'Customer Support',
    'Tickets': 'Tickets',
    'Add New Ticket': 'Add New Ticket',
    'FAQ': 'FAQ',
    'Reports': 'Reports',
    'Settings': 'Settings',
    'Ticket Macros': 'Ticket Macros',
    'Ticket Categories': 'Ticket Categories',
    'Ticket Priorities': 'Ticket Priorities',
    'Ticket Status': 'Ticket Status',
    'Blogs': 'Blogs',
    'Manage Blogs': 'Manage Blogs',
    'Pending Blogs': 'Pending Blogs',
    'Knowledge Base': 'Knowledge Base',
    'System Settings': 'System Settings',
    'Website Settings': 'Website Settings',
    'Payment Settings': 'Payment Settings',
    'Live Class Settings': 'Live Class Settings',
    'SMTP Settings': 'SMTP Settings',
    'Amazon S3 Settings': 'Amazon S3 Settings',
    'Certificate Settings': 'Certificate Settings',
    'Player Settings': 'Player Settings',
    'Manage Profile': 'Manage Profile',
    'Close': 'Close',
    'Expand': 'Expand',
    'Collapse': 'Collapse'
  };
  return phrases[key] || key;
}

function isActiveRoute(name) {
  if (!route.name) return false;
  return route.name === name || route.name.startsWith(name + '.');
}

function hasActiveChild(paths) {
  if (!route.name) return false;
  return paths.some(path => route.name === path || route.name.startsWith(path + '.'));
}

function toggleDropdown(menu) {
  openDropdown.value = openDropdown.value === menu ? null : menu;
  openSubDropdown.value = null;
}

function toggleSubDropdown(submenu) {
  openSubDropdown.value = openSubDropdown.value === submenu ? null : submenu;
}

function saveScrollPosition() {
  localStorage.setItem('navScrollPos', navAreaRef.value?.scrollTop || 0);
}

function restoreScrollPosition() {
  const scrollPos = localStorage.getItem('navScrollPos');
  if (scrollPos && navAreaRef.value) {
    navAreaRef.value.scrollTop = parseInt(scrollPos);
  }
}
</script>

<style scoped>
/* Base Sidebar */
.admin-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: 280px;
  background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
  color: white;
  z-index: 60;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease, width 0.3s ease;
  box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
}

.admin-sidebar.collapsed {
  width: 80px;
}

.admin-sidebar.collapsed .sidebar-logo-lg,
.admin-sidebar.collapsed .sidebar-title,
.admin-sidebar.collapsed .sidebar-text,
.admin-sidebar.collapsed .sidebar-arrow,
.admin-sidebar.collapsed .sidebar-badge,
.admin-sidebar.collapsed .sidebar-submenu,
.admin-sidebar.collapsed .sidebar-content > .sidebar-nav:not(:first-child) {
  display: none;
}

.admin-sidebar.collapsed .sidebar-link,
.admin-sidebar.collapsed .sidebar-dropdown-toggle {
  justify-content: center;
  padding: 12px;
}

.admin-sidebar.collapsed .sidebar-icon {
  margin-right: 0;
}

/* Mobile Styles */
@media (max-width: 1023px) {
  .admin-sidebar {
    transform: translateX(-100%);
    width: 280px !important;
  }
  
  .admin-sidebar.mobile-open {
    transform: translateX(0);
  }
}

/* Overlay */
.sidebar-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 50;
  backdrop-filter: blur(2px);
}

/* Logo Area */
.sidebar-logo-area {
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(0, 0, 0, 0.2);
}

.sidebar-logos {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
}

.sidebar-logo-lg {
  height: 42px;
  max-width: 160px;
  object-fit: contain;
  transition: opacity 0.3s;
}

.sidebar-logo-sm {
  display: none;
}

.sidebar-close-btn {
  display: none;
  padding: 8px;
  border-radius: 8px;
  color: rgba(255, 255, 255, 0.7);
  background: transparent;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.sidebar-close-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

@media (max-width: 1023px) {
  .sidebar-close-btn {
    display: flex;
  }
}

/* Collapse Button */
.sidebar-collapse-btn {
  position: absolute;
  right: -14px;
  top: 80px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #3b82f6;
  border: 3px solid #1e293b;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  z-index: 10;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
}

.sidebar-collapse-btn:hover {
  background: #2563eb;
  transform: scale(1.1);
}

@media (max-width: 1023px) {
  .sidebar-collapse-btn {
    display: none;
  }
}

/* Content */
.sidebar-content {
  flex: 1;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.sidebar-title {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: rgba(255, 255, 255, 0.4);
  padding: 20px 20px 12px;
  margin: 0;
}

/* Navigation Area */
.sidebar-nav-area {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding-bottom: 20px;
}

.sidebar-nav {
  padding: 0 12px;
}

.sidebar-nav ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

/* Sidebar Item */
.sidebar-item {
  margin-bottom: 4px;
  position: relative;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  border-radius: 10px;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 14px;
  font-weight: 500;
  position: relative;
  overflow: hidden;
}

.sidebar-link::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 3px;
  height: 0;
  background: #3b82f6;
  border-radius: 0 3px 3px 0;
  transition: height 0.2s;
}

.sidebar-link:hover {
  background: rgba(255, 255, 255, 0.08);
  color: white;
  transform: translateX(4px);
}

.sidebar-item.active > .sidebar-link,
.sidebar-item.active > .sidebar-link:hover {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(59, 130, 246, 0.1) 100%);
  color: #60a5fa;
}

.sidebar-item.active > .sidebar-link::before {
  height: 60%;
}

/* Icons */
.sidebar-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  flex-shrink: 0;
}

.sidebar-icon svg {
  width: 20px;
  height: 20px;
}

/* Text */
.sidebar-text {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Arrow */
.sidebar-arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.3s ease;
  color: rgba(255, 255, 255, 0.4);
}

.sidebar-dropdown.open > .sidebar-link > .sidebar-arrow {
  transform: rotate(180deg);
}

/* Badge */
.sidebar-badge {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 12px;
  min-width: 20px;
  text-align: center;
  box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
}

/* Dropdown Submenu */
.sidebar-submenu {
  list-style: none;
  margin: 6px 0 0 0;
  padding: 8px 0 0 44px;
  overflow: hidden;
}

.sidebar-submenu-title {
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: rgba(255, 255, 255, 0.35);
  margin-bottom: 10px;
  padding-left: 12px;
}

.sidebar-subitem {
  margin-bottom: 2px;
}

.sidebar-subitem > a,
.sidebar-dropdown-toggle {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  color: rgba(255, 255, 255, 0.6);
  text-decoration: none;
  border-radius: 8px;
  font-size: 13px;
  transition: all 0.2s;
}

.sidebar-subitem > a:hover,
.sidebar-dropdown-toggle:hover {
  background: rgba(255, 255, 255, 0.06);
  color: white;
}

.sidebar-subitem.active > a,
.sidebar-subitem.active > .sidebar-dropdown-toggle {
  background: rgba(59, 130, 246, 0.15);
  color: #60a5fa;
}

.sidebar-subitem .sidebar-arrow {
  font-size: 10px;
}

/* Level 2 Submenu */
.sidebar-submenu-level-2 {
  list-style: none;
  margin: 4px 0 0 8px;
  padding: 4px 0 0 8px;
  border-left: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-submenu-level-2 .sidebar-subitem > a {
  font-size: 12px;
  padding: 6px 10px;
}

/* Animations */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
  max-height: 500px;
  opacity: 1;
}

.slide-enter-from,
.slide-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(-10px);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Scrollbar */
.sidebar-nav-area::-webkit-scrollbar {
  width: 5px;
}

.sidebar-nav-area::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar-nav-area::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 3px;
}

.sidebar-nav-area::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.25);
}
</style>
