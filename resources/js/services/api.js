import axios from 'axios';

const api = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    withCredentials: false,
});

api.interceptors.request.use((config) => {
    if (!import.meta?.env?.PROD) {
        console.log('🌐 API Request:', config.method?.toUpperCase(), config.url);
        console.log('📦 Data:', config.data);
        console.log('📋 Headers:', config.headers);
    }
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

api.interceptors.response.use(
    (response) => {
        if (!import.meta?.env?.PROD) {
            console.log('✅ API Response:', response.status, response.config.url);
        }
        return response;
    },
    (error) => {
        if (!import.meta?.env?.PROD) {
            console.log('❌ API Error:', error.response?.status, error.config?.url, error.response?.data);
        }
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export const authApi = {
    login: (data) => api.post('/auth/login', data),
    register: (data) => api.post('/auth/register', data),
    logout: () => api.post('/auth/logout'),
    me: () => api.get('/auth/me'),
    updateProfile: (data) => api.put('/auth/profile', data),
    changePassword: (data) => api.put('/auth/password', data),
    sendResetLink: (data) => api.post('/auth/password/email', data),
    resetPassword: (data) => api.post('/auth/password/reset', data),
};

export const courseApi = {
    getAll: (params) => api.get('/courses', { params }),
    getFeatured: () => api.get('/courses/featured'),
    getPopular: () => api.get('/courses/popular'),
    getById: (id) => api.get(`/courses/${id}`),
    create: (data) => api.post('/courses', data),
    update: (id, data) => api.put(`/courses/${id}`, data),
    delete: (id) => api.delete(`/courses/${id}`),
    enroll: (id) => api.post(`/courses/${id}/enroll`),
    getReviews: (id) => api.get(`/courses/${id}/reviews`),
    postReview: (id, data) => api.post(`/courses/${id}/reviews`, data),
    getCurriculum: (id) => api.get(`/courses/${id}/curriculum`),
    updateCurriculum: (id, data) => api.put(`/courses/${id}/curriculum`, data),
};

export const categoryApi = {
    getAll: (params) => api.get('/categories', { params }),
    getById: (id) => api.get(`/categories/${id}`),
    getCourses: (id, params) => api.get(`/categories/${id}/courses`, { params }),
    create: (data) => api.post('/categories', data),
    update: (id, data) => api.put(`/categories/${id}`, data),
    delete: (id) => api.delete(`/categories/${id}`),
};

export const instructorApi = {
    getAll: (params) => api.get('/instructors', { params }),
    getById: (id) => api.get(`/instructors/${id}`),
    getCourses: (id, params) => api.get(`/instructors/${id}/courses`, { params }),
    updateProfile: (data) => api.put('/instructor/profile', data),
    getSocialLinks: () => api.get('/instructor/social-links'),
    updateSocialLinks: (data) => api.put('/instructor/social-links', data),
    getStats: () => api.get('/instructor/stats'),
    enrollStudent: (courseId, data) => api.post(`/instructor/courses/${courseId}/enroll`, data),
    getCurriculum: (courseId) => api.get(`/instructor/courses/${courseId}/curriculum`),
    updateCurriculum: (courseId, data) => api.put(`/instructor/courses/${courseId}/curriculum`, data),
    getDashboard: () => api.get('/instructor/dashboard'),
    getMyCourses: (params) => api.get('/instructor/courses', { params }),
    getRevenue: (params) => api.get('/analytics/revenue', { params }),
};

export const enrollmentApi = {
    getAll: (params) => api.get('/my-enrollments', { params }),
    getById: (id) => api.get(`/my-enrollments/${id}`),
    getProgress: (id) => api.get(`/my-enrollments/${id}/progress`),
    getAllAdmin: (params) => api.get('/admin/enrollments', { params }),
    getByIdAdmin: (id) => api.get(`/admin/enrollments/${id}`),
    getMyEnrollments: () => api.get('/my-enrollments'),
};

export const cartApi = {
    get: () => api.get('/cart'),
    add: (data) => api.post('/cart/add', data),
    remove: (id) => api.delete(`/cart/${id}/remove`),
    checkout: (data) => api.post('/cart/checkout', data),
    applyCoupon: (data) => api.post('/cart/apply-coupon', data),
};

export const paymentApi = {
    getAll: () => api.get('/my-payments'),
    getById: (id) => api.get(`/payments/${id}`),
    getReceipt: (id) => api.get(`/payments/${id}/receipt`),
    getGateways: () => api.get('/payment-gateways'),
    initiate: (slug, data) => api.post(`/payment-gateways/${slug}/checkout`, data),
};

export const wishlistApi = {
    get: () => api.get('/wishlist'),
    add: (courseId) => api.post(`/courses/${courseId}/wishlist`),
    remove: (courseId) => api.delete(`/courses/${courseId}/wishlist`),
};

export const notificationApi = {
    getAll: () => api.get('/notifications'),
    getUnreadCount: () => api.get('/notifications/unread-count'),
    markAsRead: (id) => api.put(`/notifications/${id}/read`),
    markAllAsRead: () => api.put('/notifications/mark-all-read'),
    getSettings: () => api.get('/notifications/settings'),
    updateSettings: (data) => api.put('/notifications/settings', data),
};

export const blogApi = {
    getAll: (params) => api.get('/blogs', { params }),
    getFeatured: () => api.get('/blogs/featured'),
    getById: (id) => api.get(`/blogs/${id}`),
    getComments: (id) => api.get(`/blogs/${id}/comments`),
    post: (data) => api.post('/blogs', data),
    update: (id, data) => api.put(`/blogs/${id}`, data),
    delete: (id) => api.delete(`/blogs/${id}`),
    like: (id) => api.post(`/blogs/${id}/like`),
    comment: (id, data) => api.post(`/blogs/${id}/comment`, data),
    getAllAdmin: (params) => api.get('/admin/blogs', { params }),
    createAdmin: (data) => api.post('/admin/blogs', data),
    updateAdmin: (id, data) => api.put(`/admin/blogs/${id}`, data),
    deleteAdmin: (id) => api.delete(`/admin/blogs/${id}`),
};

export const searchApi = {
    global: (params) => api.get('/search', { params }),
};

export const couponApi = {
    getAll: (params) => api.get('/coupons', { params }),
    create: (data) => api.post('/coupons', data),
    update: (id, data) => api.put(`/coupons/${id}`, data),
    delete: (id) => api.delete(`/coupons/${id}`),
};

export const bootcampApi = {
    getAll: (params) => api.get('/bootcamps', { params }),
    getById: (id) => api.get(`/bootcamps/${id}`),
    getCourses: (id) => api.get(`/bootcamps/${id}/courses`),
    enroll: (id) => api.post(`/bootcamps/${id}/enroll`),
    getMyEnrollments: () => api.get('/my-bootcamps'),
    getAllAdmin: (params) => api.get('/admin/bootcamps', { params }),
    create: (data) => api.post('/admin/bootcamps', data),
    update: (id, data) => api.put(`/admin/bootcamps/${id}`, data),
    delete: (id) => api.delete(`/admin/bootcamps/${id}`),
};

export const bundleApi = {
    getAll: (params) => api.get('/course-bundles', { params }),
    getById: (id) => api.get(`/course-bundles/${id}`),
    getCourses: (id) => api.get(`/course-bundles/${id}/courses`),
    enroll: (id) => api.post(`/course-bundles/${id}/enroll`),
    getAllAdmin: (params) => api.get('/admin/bundles', { params }),
    create: (data) => api.post('/admin/bundles', data),
    update: (id, data) => api.put(`/admin/bundles/${id}`, data),
    delete: (id) => api.delete(`/admin/bundles/${id}`),
};

export const quizApi = {
    getAll: (params) => api.get('/quizzes', { params }),
    getById: (id) => api.get(`/quizzes/${id}`),
    create: (data) => api.post('/quizzes', data),
    update: (id, data) => api.put(`/quizzes/${id}`, data),
    delete: (id) => api.delete(`/quizzes/${id}`),
    submit: (id, data) => api.post(`/quizzes/${id}/submit`, data),
    getResults: (id) => api.get(`/quizzes/${id}/results`),
};

export const certificateApi = {
    getAll: (params) => api.get('/certificates', { params }),
    getById: (id) => api.get(`/certificates/${id}`),
    generate: (data) => api.post('/certificates/generate', data),
    download: (id) => api.get(`/certificates/${id}/download`),
};

export const supportApi = {
    getTickets: (params) => api.get('/support/tickets', { params }),
    createTicket: (data) => api.post('/support/tickets', data),
    replyTicket: (id, data) => api.post(`/support/tickets/${id}/reply`, data),
};

export const payoutApi = {
    getAll: (params) => api.get('/payouts', { params }),
    request: (data) => api.post('/payouts/request', data),
    getAllAdmin: (params) => api.get('/admin/payouts', { params }),
    getById: (id) => api.get(`/admin/payouts/${id}`),
    approve: (id) => api.post(`/admin/payouts/${id}/approve`),
    reject: (id) => api.post(`/admin/payouts/${id}/reject`),
    process: (id) => api.post(`/admin/payouts/${id}/process`),
    getStats: () => api.get('/admin/payouts/stats'),
};

export const analyticsApi = {
    getDashboard: (params) => api.get('/admin/dashboard', { params }),
    getRevenue: (params) => api.get('/admin/kpis', { params }),
    getEnrollments: (params) => api.get('/analytics/enrollments', { params }),
    getCourses: (params) => api.get('/analytics/courses', { params }),
    getStats: () => api.get('/admin/kpis'),
    getReports: () => api.get('/admin/kpis/reports'),
};

export const userApi = {
    getAll: (params) => api.get('/users', { params }),
    getRecent: () => api.get('/users/recent'),
    getById: (id) => api.get(`/users/${id}`),
    create: (data) => api.post('/users', data),
    update: (id, data) => api.put(`/users/${id}`, data),
    delete: (id) => api.delete(`/users/${id}`),
    updateRole: (id, data) => api.put(`/users/${id}/role`, data),
};

export const courseAdminApi = {
    getAll: (params) => api.get('/admin/courses', { params }),
    getRecent: () => api.get('/courses/recent'),
    getById: (id) => api.get(`/admin/courses/${id}`),
    create: (data) => api.post('/admin/courses', data),
    update: (id, data) => api.put(`/admin/courses/${id}`, data),
    delete: (id) => api.delete(`/admin/courses/${id}`),
};

export const assignmentApi = {
    getAll: (params) => api.get('/assignments', { params }),
    getById: (id) => api.get(`/assignments/${id}`),
    create: (data) => api.post('/assignments', data),
    update: (id, data) => api.put(`/assignments/${id}`, data),
    delete: (id) => api.delete(`/assignments/${id}`),
    getSubmissions: (id, params) => api.get(`/assignments/${id}/submissions`, { params }),
    gradeSubmission: (id, data) => api.put(`/assignments/${id}/grade`, data),
};

export const liveClassApi = {
    getAll: (params) => api.get('/live-classes', { params }),
    getById: (id) => api.get(`/live-classes/${id}`),
    create: (data) => api.post('/live-classes', data),
    update: (id, data) => api.put(`/live-classes/${id}`, data),
    delete: (id) => api.delete(`/live-classes/${id}`),
    start: (id) => api.post(`/live-classes/${id}/start`),
    end: (id) => api.post(`/live-classes/${id}/end`),
    enroll: (id) => api.post(`/live-classes/${id}/enroll`),
    getMyClasses: () => api.get('/live-classes/my-classes'),
};

export const messagesApi = {
    getConversations: () => api.get('/messages/conversations'),
    getMessages: (conversationId) => api.get(`/messages/conversation/${conversationId}`),
    send: (data) => api.post('/messages/send', data),
    markRead: (messageId) => api.put(`/messages/${messageId}/read`),
};

export const noticeApi = {
    getAll: (params) => api.get('/notices', { params }),
    getById: (id) => api.get(`/notices/${id}`),
    create: (data) => api.post('/notices', data),
    update: (id, data) => api.put(`/notices/${id}`, data),
    delete: (id) => api.delete(`/notices/${id}`),
};

export const contactApi = {
    send: (data) => api.post('/contact', data),
};

export const settingsApi = {
    get: () => api.get('/admin/settings'),
    getGroup: (group) => api.get(`/admin/settings/${group}`),
    update: (data) => api.put('/admin/settings', data),
    updateKey: (key, data) => api.put(`/admin/settings/${key}`, data),
};

export const teamTrainingApi = {
    getAll: (params) => api.get('/admin/team-packages', { params }),
    getById: (id) => api.get(`/admin/team-packages/${id}`),
    create: (data) => api.post('/admin/team-packages', data),
    update: (id, data) => api.put(`/admin/team-packages/${id}`, data),
    delete: (id) => api.delete(`/admin/team-packages/${id}`),
    duplicate: (id) => api.post(`/admin/team-packages/${id}/duplicate`),
    toggleStatus: (id) => api.post(`/admin/team-packages/${id}/toggle-status`),
    getCourses: () => api.get('/admin/team-packages/courses'),
};

export const ebookApi = {
    getAll: (params) => api.get('/admin/ebooks', { params }),
    getById: (id) => api.get(`/admin/ebooks/${id}`),
    create: (data) => api.post('/admin/ebooks', data),
    update: (id, data) => api.put(`/admin/ebooks/${id}`, data),
    delete: (id) => api.delete(`/admin/ebooks/${id}`),
    updateStatus: (id, data) => api.post(`/admin/ebooks/${id}/status`, data),
};

export default api;