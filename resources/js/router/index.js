import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    // Guest routes (public)
    {
        path: '/',
        name: 'home',
        component: () => import('../modules/public/pages/HomePage.vue'),
    },
    {
        path: '/topbar-demo',
        name: 'topbar-demo',
        component: () => import('../modules/public/pages/TopBarDemoPage.vue'),
        meta: { layout: 'default' },
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../modules/auth/pages/LoginPage.vue'),
        meta: { layout: 'guest', guest: true },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../modules/auth/pages/RegisterPage.vue'),
        meta: { layout: 'guest', guest: true },
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('../modules/auth/pages/ForgotPasswordPage.vue'),
        meta: { layout: 'guest', guest: true },
    },
    {
        path: '/courses',
        name: 'courses',
        component: () => import('../modules/public/pages/CoursesPage.vue'),
    },
    {
        path: '/courses/:id',
        name: 'course-details',
        component: () => import('../modules/public/pages/CourseDetailsPage.vue'),
    },
    {
        path: '/instructors',
        name: 'instructors',
        component: () => import('../modules/public/pages/InstructorsPage.vue'),
    },
    {
        path: '/instructors/:id',
        name: 'instructor-profile',
        component: () => import('../modules/public/pages/InstructorProfilePage.vue'),
    },
    {
        path: '/blog',
        name: 'blog',
        component: () => import('../modules/public/pages/BlogPage.vue'),
    },
    {
        path: '/blog/:id',
        name: 'blog-details',
        component: () => import('../modules/public/pages/BlogDetailsPage.vue'),
    },
    {
        path: '/about',
        name: 'about',
        component: () => import('../modules/public/pages/AboutPage.vue'),
    },
    {
        path: '/contact',
        name: 'contact',
        component: () => import('../modules/public/pages/ContactPage.vue'),
    },
    {
        path: '/faq',
        name: 'faq',
        component: () => import('../modules/public/pages/FaqPage.vue'),
    },
    {
        path: '/terms',
        name: 'terms',
        component: () => import('../modules/public/pages/TermsPage.vue'),
    },
    {
        path: '/privacy',
        name: 'privacy',
        component: () => import('../modules/public/pages/PrivacyPage.vue'),
    },
    {
        path: '/cookies',
        name: 'cookies',
        component: () => import('../modules/public/pages/CookiePage.vue'),
    },
    {
        path: '/help',
        name: 'help',
        component: () => import('../modules/public/pages/HelpCenterPage.vue'),
    },

    // Student routes
    {
        path: '/student',
        name: 'student-dashboard',
        component: () => import('../modules/student/pages/DashboardPage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },
    {
        path: '/student/courses',
        name: 'my-courses',
        component: () => import('../modules/student/pages/MyCoursesPage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },
    {
        path: '/student/courses/:id/learn',
        name: 'course-learn',
        component: () => import('../modules/player/pages/CoursePlayerPage.vue'),
        meta: { layout: 'player', requiresAuth: true },
    },
    {
        path: '/student/cart',
        name: 'cart',
        component: () => import('../modules/student/pages/CartPage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },
    {
        path: '/student/checkout',
        name: 'checkout',
        component: () => import('../modules/student/pages/CheckoutPage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },
    {
        path: '/student/wishlist',
        name: 'wishlist',
        component: () => import('../modules/student/pages/WishlistPage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },
    {
        path: '/student/purchases',
        name: 'purchases',
        component: () => import('../modules/student/pages/PurchaseHistoryPage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },
    {
        path: '/student/messages',
        name: 'messages',
        component: () => import('../modules/student/pages/MessagesPage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },
    {
        path: '/student/support',
        name: 'support',
        component: () => import('../modules/student/pages/SupportPage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },
    {
        path: '/student/certificates',
        name: 'certificates',
        component: () => import('../modules/student/pages/CertificatesPage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },
    {
        path: '/student/profile',
        name: 'student-profile',
        component: () => import('../modules/student/pages/ProfilePage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },

    // Instructor routes
    {
        path: '/instructor',
        name: 'instructor-dashboard',
        component: () => import('../modules/instructor/pages/DashboardPage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/courses',
        name: 'instructor-courses',
        component: () => import('../modules/instructor/pages/CoursesPage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/courses/create',
        name: 'course-create',
        component: () => import('../modules/instructor/pages/CreateCoursePage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/courses/:id/edit',
        name: 'course-edit',
        component: () => import('../modules/instructor/pages/EditCoursePage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/curriculum/:courseId',
        name: 'course-curriculum',
        component: () => import('../modules/instructor/pages/CurriculumPage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/quizzes',
        name: 'instructor-quizzes',
        component: () => import('../modules/instructor/pages/QuizzesPage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/assignments',
        name: 'instructor-assignments',
        component: () => import('../modules/instructor/pages/AssignmentsPage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/students',
        name: 'instructor-students',
        component: () => import('../modules/instructor/pages/StudentsPage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/payouts',
        name: 'instructor-payouts',
        component: () => import('../modules/instructor/pages/PayoutsPage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/revenue',
        name: 'instructor-revenue',
        component: () => import('../modules/instructor/pages/RevenuePage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/profile',
        name: 'instructor-profile',
        component: () => import('../modules/instructor/pages/ProfilePage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/notice-board',
        name: 'instructor-notice-board',
        component: () => import('../modules/instructor/pages/NoticeBoardPage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },

    // Admin routes
    {
        path: '/admin',
        name: 'admin-dashboard',
        component: () => import('../modules/admin/pages/DashboardPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/users',
        name: 'admin-users',
        component: () => import('../modules/admin/pages/UsersPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/courses',
        name: 'admin-courses',
        component: () => import('../modules/admin/pages/CoursesPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/categories',
        name: 'admin-categories',
        component: () => import('../modules/admin/pages/CategoriesPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/blogs',
        name: 'admin-blogs',
        component: () => import('../modules/admin/pages/BlogsPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/instructors',
        name: 'admin-instructors',
        component: () => import('../modules/admin/pages/InstructorsPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/coupons',
        name: 'admin-coupons',
        component: () => import('../modules/admin/pages/CouponsPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/enrollments',
        name: 'admin-enrollments',
        component: () => import('../modules/admin/pages/EnrollmentsPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/reports',
        name: 'admin-reports',
        component: () => import('../modules/admin/pages/ReportsPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/payouts',
        name: 'admin-payouts',
        component: () => import('../modules/admin/pages/PayoutsPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/certificates',
        name: 'admin-certificates',
        component: () => import('../modules/admin/pages/CertificatesPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/support',
        name: 'admin-support',
        component: () => import('../modules/admin/pages/SupportPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/course-bundles',
        name: 'admin-course-bundles',
        component: () => import('../modules/admin/pages/CourseBundlesPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/settings',
        name: 'admin-settings',
        component: () => import('../modules/admin/pages/SettingsPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/settings/payment',
        name: 'admin-payment-settings',
        component: () => import('../modules/admin/pages/PaymentSettingsPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/bootcamps',
        name: 'admin-bootcamps',
        component: () => import('../modules/admin/pages/BootcampsPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/ebooks',
        name: 'admin-ebooks',
        component: () => import('../modules/admin/pages/EbooksPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/team-training',
        name: 'admin-team-training',
        component: () => import('../modules/admin/pages/TeamTrainingPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/email-templates',
        name: 'admin-email-templates',
        component: () => import('../modules/admin/pages/EmailTemplatesPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/knowledge-base',
        name: 'admin-knowledge-base',
        component: () => import('../modules/admin/pages/KnowledgeBasePage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },

    // Student - Bundles
    {
        path: '/bundles',
        name: 'bundles',
        component: () => import('../modules/public/pages/CourseBundlesPage.vue'),
    },
    {
        path: '/bundles/:id',
        name: 'bundle-details',
        component: () => import('../modules/public/pages/CourseBundleDetailsPage.vue'),
    },

    // Instructor Quiz routes
    {
        path: '/instructor/quizzes/create',
        name: 'quiz-create',
        component: () => import('../modules/instructor/pages/QuizBuilderPage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/quizzes/:id/edit',
        name: 'quiz-edit',
        component: () => import('../modules/instructor/pages/QuizBuilderPage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/quizzes/:id/results',
        name: 'quiz-results',
        component: () => import('../modules/instructor/pages/QuizResultsPage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },

    // Student course sub-routes
    {
        path: '/student/courses/:id',
        name: 'student-course-details',
        component: () => import('../modules/public/pages/CourseDetailsPage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },
    {
        path: '/student/courses/:id/learn/lesson/:lessonId',
        name: 'course-lesson',
        component: () => import('../modules/player/pages/LessonPage.vue'),
        meta: { layout: 'player', requiresAuth: true },
    },
    {
        path: '/student/courses/:id/learn/quiz/:quizId',
        name: 'course-quiz',
        component: () => import('../modules/player/pages/QuizPage.vue'),
        meta: { layout: 'player', requiresAuth: true },
    },

    // Search
    {
        path: '/search',
        name: 'search',
        component: () => import('../modules/public/pages/SearchPage.vue'),
    },

    // Live Classes
    {
        path: '/student/live-classes',
        name: 'live-classes',
        component: () => import('../modules/student/pages/LiveClassesPage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },
    {
        path: '/instructor/live-classes',
        name: 'instructor-live-classes',
        component: () => import('../modules/instructor/pages/LiveClassesPage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },
    {
        path: '/instructor/live-classes/create',
        name: 'live-class-create',
        component: () => import('../modules/instructor/pages/LiveClassCreatePage.vue'),
        meta: { layout: 'instructor', requiresAuth: true, role: 'instructor' },
    },

    // Notifications
    {
        path: '/student/notifications',
        name: 'notifications',
        component: () => import('../modules/student/pages/NotificationsPage.vue'),
        meta: { layout: 'student', requiresAuth: true },
    },

    // Admin Live Classes
    {
        path: '/admin/live-classes',
        name: 'admin-live-classes',
        component: () => import('../modules/admin/pages/LiveClassesPage.vue'),
        meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },

    // Fallback
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('../modules/public/pages/NotFoundPage.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) return savedPosition;
        return { top: 0 };
    },
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return next({ name: 'login', query: { redirect: to.fullPath } });
    }

    if (to.meta.guest && authStore.isAuthenticated) {
        if (authStore.isAdmin) return next({ name: 'admin-dashboard' });
        if (authStore.isInstructor) return next({ name: 'instructor-dashboard' });
        return next({ name: 'student-dashboard' });
    }

    if (to.meta.role === 'admin' && !authStore.isAdmin) {
        return next({ name: 'home' });
    }

    if (to.meta.role === 'instructor' && !authStore.isInstructor) {
        return next({ name: 'student-dashboard' });
    }

    next();
});

export default router;