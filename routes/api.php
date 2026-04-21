<?php

use App\Http\Controllers\Api\V1\Admin\BootcampController;
use App\Http\Controllers\Api\V1\Admin\ContactController;
use App\Http\Controllers\Api\V1\Admin\CouponController;
use App\Http\Controllers\Api\V1\Admin\CourseApprovalController;
use App\Http\Controllers\Api\V1\Admin\CourseBundleController;
use App\Http\Controllers\Api\V1\Admin\CustomFieldController;
use App\Http\Controllers\Api\V1\Admin\DataTableController;
use App\Http\Controllers\Api\V1\Admin\EbookController;
use App\Http\Controllers\Api\V1\Admin\ExportController;
use App\Http\Controllers\Api\V1\Admin\FeatureToggleController;
use App\Http\Controllers\Api\V1\Admin\KnowledgeBaseController;
use App\Http\Controllers\Api\V1\Admin\KpiController;
use App\Http\Controllers\Api\V1\Admin\LiveClassController;
use App\Http\Controllers\Api\V1\Admin\NewsletterController;
use App\Http\Controllers\Api\V1\Admin\PayoutController;
use App\Http\Controllers\Api\V1\Admin\PermissionController;
use App\Http\Controllers\Api\V1\Admin\ReportController;
use App\Http\Controllers\Api\V1\Admin\RoleController;
use App\Http\Controllers\Api\V1\Admin\SettingsController;
use App\Http\Controllers\Api\V1\Admin\TeamTrainingController;
use App\Http\Controllers\Api\V1\Admin\TutorController;
use App\Http\Controllers\Api\V1\AI\AIController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Auth\SocialAuthController;
use App\Http\Controllers\Api\V1\Blog\BlogController;
use App\Http\Controllers\Api\V1\Chat\ChatChannelController;
use App\Http\Controllers\Api\V1\Common\CurrencyController;
use App\Http\Controllers\Api\V1\Common\SearchController;
use App\Http\Controllers\Api\V1\Course\CourseController;
use App\Http\Controllers\Api\V1\Course\EnrollmentController;
use App\Http\Controllers\Api\V1\Course\LessonController;
use App\Http\Controllers\Api\V1\Course\ReviewController;
use App\Http\Controllers\Api\V1\Instructor\AssignmentController;
use App\Http\Controllers\Api\V1\Instructor\BootcampController as InstructorBootcampController;
use App\Http\Controllers\Api\V1\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Api\V1\Instructor\LessonController as InstructorLessonController;
use App\Http\Controllers\Api\V1\Instructor\QuizController;
use App\Http\Controllers\Api\V1\Instructor\SectionController;
use App\Http\Controllers\Api\V1\Media\WatermarkController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\Payment\OfflinePaymentController;
use App\Http\Controllers\Api\V1\Payment\PaymentController;
use App\Http\Controllers\Api\V1\Payment\PdfController;
use App\Http\Controllers\Api\V1\Payment\WebhookController;
use App\Http\Controllers\Api\V1\Player\PlayerSettingsController;
use App\Http\Controllers\Api\V1\PwaController;
use App\Http\Controllers\Api\V1\Student\MyBundlesController;
use App\Http\Controllers\Api\V1\Student\MyCoursesController;
use App\Http\Controllers\Api\V1\Student\SupportController;
use App\Http\Controllers\Api\V1\Student\TutorBookingController;
use App\Http\Controllers\Api\V1\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Eduvora v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ========== AUTH ==========
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/password/email', [PasswordResetController::class, 'sendResetLink']);
    Route::post('auth/password/reset', [PasswordResetController::class, 'reset']);

    // Social Auth
    Route::get('auth/google', [SocialAuthController::class, 'googleRedirect']);
    Route::get('auth/google/callback', [SocialAuthController::class, 'googleCallback']);
    Route::get('auth/facebook', [SocialAuthController::class, 'facebookRedirect']);
    Route::get('auth/facebook/callback', [SocialAuthController::class, 'facebookCallback']);
    Route::get('auth/apple', [SocialAuthController::class, 'appleRedirect']);
    Route::get('auth/apple/callback', [SocialAuthController::class, 'appleCallback']);

    // ========== COURSES (Public) ==========
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/featured', [CourseController::class, 'featured']);
    Route::get('courses/popular', [CourseController::class, 'popular']);
    Route::get('courses/{id}', [CourseController::class, 'show']);
    Route::get('courses/{id}/reviews', [ReviewController::class, 'courseReviews']);
    Route::get('categories', [CourseController::class, 'categories']);
    Route::get('categories/{id}/courses', [CourseController::class, 'categoryCourses']);

    // ========== REVIEWS (Public) ==========
    Route::get('reviews/{id}', [ReviewController::class, 'show']);

    // ========== BLOG (Public) ==========
    Route::get('blogs', [BlogController::class, 'index']);
    Route::get('blogs/featured', [BlogController::class, 'featured']);
    Route::get('blogs/{id}', [BlogController::class, 'show']);

    // ========== CHAT CHANNELS (Public) ==========
    Route::get('channels', [ChatChannelController::class, 'index']);
    Route::get('channels/{id}', [ChatChannelController::class, 'show']);
    Route::get('channels/{id}/messages', [ChatChannelController::class, 'messages']);
    Route::get('channels/{id}/members', [ChatChannelController::class, 'members']);

    // ========== USER (Public) ==========
    Route::get('instructors', [UserController::class, 'instructors']);
    Route::get('instructors/{id}', [UserController::class, 'profile']);
    Route::get('instructors/{id}/courses', [UserController::class, 'instructorCourses']);

    // ========== SEARCH & KPIs ==========
    Route::get('search', [KpiController::class, 'searchAnalytics']);

    // ========== PWA ==========
    Route::get('manifest.json', [PwaController::class, 'manifest']);
    Route::get('sw.js', [PwaController::class, 'serviceWorker']);

    // ========== PROTECTED ==========
    Route::middleware('auth:web')->group(function () {

        // Auth
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::put('auth/profile', [AuthController::class, 'updateProfile']);
        Route::put('auth/password', [PasswordResetController::class, 'changePassword']);
        Route::post('auth/email/verify', [EmailVerificationController::class, 'sendVerificationLink']);
        Route::get('auth/email/check', [EmailVerificationController::class, 'checkVerified']);

        // Courses - Enroll
        Route::post('courses/{id}/enroll', [EnrollmentController::class, 'enroll']);
        Route::get('my-enrollments', [EnrollmentController::class, 'index']);
        Route::get('my-enrollments/{id}', [EnrollmentController::class, 'show']);

        // Lessons - Progress
        Route::post('lessons/{id}/progress', [LessonController::class, 'updateProgress']);
        Route::get('lessons/{id}/complete', [LessonController::class, 'markComplete']);

        // Reviews
        Route::post('courses/{id}/reviews', [ReviewController::class, 'store']);
        Route::put('reviews/{id}', [ReviewController::class, 'update']);
        Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);

        // Wishlist
        Route::get('wishlist', [CourseController::class, 'myWishlist']);
        Route::post('courses/{id}/wishlist', [CourseController::class, 'addToWishlist']);
        Route::delete('courses/{id}/wishlist', [CourseController::class, 'removeFromWishlist']);

        // Cart
        Route::get('cart', [PaymentController::class, 'cart']);
        Route::post('cart/add', [PaymentController::class, 'addToCart']);
        Route::delete('cart/{id}/remove', [PaymentController::class, 'removeFromCart']);
        Route::post('cart/checkout', [PaymentController::class, 'checkout']);

        // Payments
        Route::get('my-payments', [PaymentController::class, 'index']);
        Route::get('payments/{id}', [PaymentController::class, 'show']);
        Route::get('payments/{id}/receipt', [PdfController::class, 'generateReceipt']);
        Route::get('payments/{id}/invoice', [PdfController::class, 'generateInvoice']);

        // Chat Channels
        Route::post('channels/{id}/join', [ChatChannelController::class, 'join']);
        Route::post('channels/{id}/leave', [ChatChannelController::class, 'leave']);
        Route::post('channels/{id}/messages', [ChatChannelController::class, 'sendMessage']);
        Route::post('messages/{messageId}/react', [ChatChannelController::class, 'addReaction']);
        Route::delete('messages/{messageId}/react', [ChatChannelController::class, 'removeReaction']);

        // Blog
        Route::post('blogs', [BlogController::class, 'store']);
        Route::put('blogs/{id}', [BlogController::class, 'update']);
        Route::delete('blogs/{id}', [BlogController::class, 'destroy']);
        Route::post('blogs/{id}/like', [BlogController::class, 'like']);
        Route::post('blogs/{id}/comment', [BlogController::class, 'comment']);

        // User Profile
        Route::put('profile', [UserController::class, 'update']);
        Route::post('profile/avatar', [UserController::class, 'uploadAvatar']);

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::put('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::put('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);
        Route::get('notifications/settings', [NotificationController::class, 'settings']);
        Route::put('notifications/settings', [NotificationController::class, 'updateSettings']);
        Route::post('notifications/test', [NotificationController::class, 'sendTest']);
        Route::post('toast', [NotificationController::class, 'sendToast']);

        // AI
        Route::post('ai/generate', [AIController::class, 'generateCourseContent']);
        Route::post('ai/lesson', [AIController::class, 'generateLessonContent']);
        Route::post('ai/quiz', [AIController::class, 'generateQuiz']);
        Route::post('ai/improve', [AIController::class, 'improveContent']);
        Route::post('ai/translate', [AIController::class, 'translateContent']);
        Route::post('ai/seo', [AIController::class, 'generateSeo']);
        Route::post('ai/summary', [AIController::class, 'generateSummary']);
        Route::post('ai/chat', [AIController::class, 'chat']);

        // PWA
        Route::post('pwa/register', [PwaController::class, 'registerDevice']);
        Route::post('pwa/unregister', [PwaController::class, 'unregisterDevice']);
        Route::post('pwa/subscribe', [PwaController::class, 'pushSubscription']);
    });

    // ========== ADMIN ==========
    Route::middleware(['auth:web', 'role:admin'])->group(function () {

        // KPIs & Dashboard
        Route::get('admin/dashboard', [KpiController::class, 'dashboard']);
        Route::get('admin/kpis', [KpiController::class, 'dashboard']);
        Route::get('admin/kpis/reports', [KpiController::class, 'reports']);
        Route::get('admin/search-analytics', [KpiController::class, 'searchAnalytics']);

        // DataTables
        Route::get('admin/dt/users', [DataTableController::class, 'users']);
        Route::get('admin/dt/courses', [DataTableController::class, 'courses']);
        Route::get('admin/dt/enrollments', [DataTableController::class, 'enrollments']);
        Route::get('admin/dt/payments', [DataTableController::class, 'payments']);
        Route::get('admin/dt/reviews', [DataTableController::class, 'reviews']);
        Route::get('admin/dt/tickets', [DataTableController::class, 'tickets']);
        Route::get('admin/dt/categories', [DataTableController::class, 'categories']);
        Route::get('admin/dt/blogs', [DataTableController::class, 'blogs']);
        Route::get('admin/dt/coupons', [DataTableController::class, 'coupons']);

        // Feature Toggles
        Route::get('admin/features', [FeatureToggleController::class, 'index']);
        Route::get('admin/features/{feature}', [FeatureToggleController::class, 'show']);
        Route::post('admin/features', [FeatureToggleController::class, 'store']);
        Route::put('admin/features/{feature}', [FeatureToggleController::class, 'update']);
        Route::delete('admin/features/{feature}', [FeatureToggleController::class, 'destroy']);
        Route::post('admin/features/{feature}/toggle', [FeatureToggleController::class, 'toggle']);
        Route::get('admin/features/{feature}/check', [FeatureToggleController::class, 'check']);

        // Roles & Permissions
        Route::get('admin/roles', [RoleController::class, 'index']);
        Route::post('admin/roles', [RoleController::class, 'store']);
        Route::get('admin/roles/{role}', [RoleController::class, 'show']);
        Route::put('admin/roles/{role}', [RoleController::class, 'update']);
        Route::delete('admin/roles/{role}', [RoleController::class, 'destroy']);
        Route::post('admin/roles/{role}/permissions', [RoleController::class, 'assignPermissions']);

        Route::get('admin/permissions', [PermissionController::class, 'index']);
        Route::post('admin/permissions', [PermissionController::class, 'store']);
        Route::post('admin/permissions/bulk', [PermissionController::class, 'bulkStore']);
        Route::get('admin/permissions/{permission}', [PermissionController::class, 'show']);
        Route::put('admin/permissions/{permission}', [PermissionController::class, 'update']);
        Route::delete('admin/permissions/{permission}', [PermissionController::class, 'destroy']);
        Route::get('admin/permission-groups', [PermissionController::class, 'groups']);

        // Export/Import
        Route::get('admin/export', [ExportController::class, 'export']);
        Route::post('admin/import', [ExportController::class, 'import']);
        Route::get('admin/export/template', [ExportController::class, 'template']);
        Route::post('admin/export/bulk', [ExportController::class, 'bulk']);

        // Settings
        Route::get('admin/settings', [SettingsController::class, 'index']);
        Route::get('admin/settings/{group}', [SettingsController::class, 'show']);
        Route::put('admin/settings', [SettingsController::class, 'update']);
        Route::put('admin/settings/{key}', [SettingsController::class, 'updateKey']);
        Route::delete('admin/settings/{key}', [SettingsController::class, 'destroy']);
        Route::get('admin/settings/export', [SettingsController::class, 'export']);
        Route::post('admin/settings/import', [SettingsController::class, 'import']);
        Route::post('admin/settings/reset', [SettingsController::class, 'reset']);
        Route::get('admin/settings/frontend', [SettingsController::class, 'frontend']);
        Route::put('admin/settings/frontend', [SettingsController::class, 'updateFrontend']);

        // Course Bundles
        Route::get('admin/bundles', [CourseBundleController::class, 'index']);
        Route::post('admin/bundles', [CourseBundleController::class, 'store']);
        Route::get('admin/bundles/{id}', [CourseBundleController::class, 'show']);
        Route::put('admin/bundles/{id}', [CourseBundleController::class, 'update']);
        Route::delete('admin/bundles/{id}', [CourseBundleController::class, 'destroy']);
        Route::post('admin/bundles/{id}/status', [CourseBundleController::class, 'status']);
        Route::post('admin/bundles/{id}/duplicate', [CourseBundleController::class, 'duplicate']);
        Route::get('admin/bundles/purchase-history', [CourseBundleController::class, 'purchaseHistory']);
        Route::get('admin/bundles/{id}/invoice', [CourseBundleController::class, 'invoice']);
        Route::get('admin/bundles/revenue', [CourseBundleController::class, 'adminRevenue']);

        // Bootcamps
        Route::get('admin/bootcamps', [BootcampController::class, 'index']);
        Route::post('admin/bootcamps', [BootcampController::class, 'store']);
        Route::get('admin/bootcamps/{id}', [BootcampController::class, 'show']);
        Route::put('admin/bootcamps/{id}', [BootcampController::class, 'update']);
        Route::delete('admin/bootcamps/{id}', [BootcampController::class, 'destroy']);
        Route::post('admin/bootcamps/{id}/status', [BootcampController::class, 'status']);
        Route::post('admin/bootcamps/{id}/duplicate', [BootcampController::class, 'duplicate']);
        Route::get('admin/bootcamps/purchase-history', [BootcampController::class, 'purchaseHistory']);
        Route::get('admin/bootcamps/{id}/invoice', [BootcampController::class, 'invoice']);
        Route::post('admin/bootcamps/modules', [BootcampController::class, 'modules']);
        Route::put('admin/bootcamps/modules/{id}', [BootcampController::class, 'updateModule']);
        Route::delete('admin/bootcamps/modules/{id}', [BootcampController::class, 'deleteModule']);
        Route::post('admin/bootcamps/modules/sort', [BootcampController::class, 'sortModules']);
        Route::post('admin/bootcamps/resources', [BootcampController::class, 'resources']);
        Route::delete('admin/bootcamps/resources/{id}', [BootcampController::class, 'deleteResource']);
        Route::post('admin/bootcamps/live-classes', [BootcampController::class, 'liveClasses']);
        Route::put('admin/bootcamps/live-classes/{id}', [BootcampController::class, 'updateLiveClass']);
        Route::delete('admin/bootcamps/live-classes/{id}', [BootcampController::class, 'deleteLiveClass']);
        Route::get('admin/bootcamps/live-classes/{id}/join', [BootcampController::class, 'joinLiveClass']);
        Route::get('admin/bootcamps/live-classes/{id}/end', [BootcampController::class, 'endLiveClass']);

        // Team Training
        Route::get('admin/team-packages', [TeamTrainingController::class, 'index']);
        Route::post('admin/team-packages', [TeamTrainingController::class, 'store']);
        Route::get('admin/team-packages/{id}', [TeamTrainingController::class, 'show']);
        Route::put('admin/team-packages/{id}', [TeamTrainingController::class, 'update']);
        Route::delete('admin/team-packages/{id}', [TeamTrainingController::class, 'destroy']);
        Route::post('admin/team-packages/{id}/duplicate', [TeamTrainingController::class, 'duplicate']);
        Route::post('admin/team-packages/{id}/toggle-status', [TeamTrainingController::class, 'toggleStatus']);
        Route::get('admin/team-packages/purchase-history', [TeamTrainingController::class, 'purchaseHistory']);
        Route::get('admin/team-packages/{id}/invoice', [TeamTrainingController::class, 'invoice']);
        Route::get('admin/team-packages/courses', [TeamTrainingController::class, 'getCourses']);

        // Tutors
        Route::get('admin/tutors', [TutorController::class, 'index']);
        Route::get('admin/tutors/{id}', [TutorController::class, 'show']);
        Route::put('admin/tutors/{id}', [TutorController::class, 'update']);
        Route::get('admin/tutor-categories', [TutorController::class, 'categories']);
        Route::post('admin/tutor-categories', [TutorController::class, 'storeCategory']);
        Route::put('admin/tutor-categories/{id}', [TutorController::class, 'updateCategory']);
        Route::delete('admin/tutor-categories/{id}', [TutorController::class, 'deleteCategory']);
        Route::post('admin/tutor-categories/{id}/status', [TutorController::class, 'categoryStatus']);
        Route::get('admin/tutor-subjects', [TutorController::class, 'subjects']);
        Route::post('admin/tutor-subjects', [TutorController::class, 'storeSubject']);
        Route::put('admin/tutor-subjects/{id}', [TutorController::class, 'updateSubject']);
        Route::delete('admin/tutor-subjects/{id}', [TutorController::class, 'deleteSubject']);
        Route::post('admin/tutor-subjects/{id}/status', [TutorController::class, 'subjectStatus']);
        Route::get('admin/tutor-schedules/{tutorId}', [TutorController::class, 'schedules']);
        Route::post('admin/tutor-schedules', [TutorController::class, 'storeSchedule']);
        Route::put('admin/tutor-schedules/{id}', [TutorController::class, 'updateSchedule']);
        Route::delete('admin/tutor-schedules/{id}', [TutorController::class, 'deleteSchedule']);
        Route::get('admin/tutor-bookings', [TutorController::class, 'bookings']);
        Route::put('admin/tutor-bookings/{id}/status', [TutorController::class, 'updateBookingStatus']);

        // Knowledge Base
        Route::get('admin/knowledge-base', [KnowledgeBaseController::class, 'index']);
        Route::post('admin/knowledge-base', [KnowledgeBaseController::class, 'store']);
        Route::get('admin/knowledge-base/{id}', [KnowledgeBaseController::class, 'show']);
        Route::put('admin/knowledge-base/{id}', [KnowledgeBaseController::class, 'update']);
        Route::delete('admin/knowledge-base/{id}', [KnowledgeBaseController::class, 'destroy']);
        Route::get('admin/knowledge-base-topics', [KnowledgeBaseController::class, 'topics']);
        Route::post('admin/knowledge-base-topics', [KnowledgeBaseController::class, 'storeTopic']);
        Route::put('admin/knowledge-base-topics/{id}', [KnowledgeBaseController::class, 'updateTopic']);
        Route::delete('admin/knowledge-base-topics/{id}', [KnowledgeBaseController::class, 'destroyTopic']);

        // Ebooks
        Route::get('admin/ebooks', [EbookController::class, 'index']);
        Route::post('admin/ebooks', [EbookController::class, 'store']);
        Route::get('admin/ebooks/{id}', [EbookController::class, 'show']);
        Route::put('admin/ebooks/{id}', [EbookController::class, 'update']);
        Route::delete('admin/ebooks/{id}', [EbookController::class, 'destroy']);
        Route::post('admin/ebooks/{id}/status', [EbookController::class, 'status']);
        Route::get('admin/ebooks/purchase-history', [EbookController::class, 'purchaseHistory']);
        Route::get('admin/ebooks/revenue', [EbookController::class, 'adminRevenue']);
        Route::get('admin/ebooks/instructor-revenue/{instructorId}', [EbookController::class, 'instructorRevenue']);
        Route::get('admin/ebooks/preview/{slug}/{type}', [EbookController::class, 'preview']);
        Route::get('admin/ebook-categories', [EbookController::class, 'categories']);
        Route::post('admin/ebook-categories', [EbookController::class, 'storeCategory']);
        Route::put('admin/ebook-categories/{id}', [EbookController::class, 'updateCategory']);
        Route::delete('admin/ebook-categories/{id}', [EbookController::class, 'deleteCategory']);

        // Coupons
        Route::get('admin/coupons', [CouponController::class, 'index']);
        Route::post('admin/coupons', [CouponController::class, 'store']);
        Route::get('admin/coupons/{id}', [CouponController::class, 'show']);
        Route::put('admin/coupons/{id}', [CouponController::class, 'update']);
        Route::delete('admin/coupons/{id}', [CouponController::class, 'destroy']);
        Route::post('admin/coupons/{id}/status', [CouponController::class, 'status']);
        Route::post('admin/coupons/validate', [CouponController::class, 'validate']);

        // Live Classes
        Route::get('admin/live-classes', [LiveClassController::class, 'index']);
        Route::post('admin/live-classes', [LiveClassController::class, 'store']);
        Route::get('admin/live-classes/{id}', [LiveClassController::class, 'show']);
        Route::put('admin/live-classes/{id}', [LiveClassController::class, 'update']);
        Route::delete('admin/live-classes/{id}', [LiveClassController::class, 'destroy']);
        Route::post('admin/live-classes/{id}/start', [LiveClassController::class, 'start']);
        Route::post('admin/live-classes/{id}/end', [LiveClassController::class, 'end']);
        Route::get('admin/live-classes/{id}/participants', [LiveClassController::class, 'participants']);
        Route::get('admin/live-classes/settings', [LiveClassController::class, 'settings']);
        Route::put('admin/live-classes/settings', [LiveClassController::class, 'updateSettings']);

        // Reports
        Route::get('admin/reports/revenue', [ReportController::class, 'adminRevenue']);
        Route::get('admin/reports/instructor-earnings', [ReportController::class, 'instructorRevenue']);
        Route::get('admin/reports/purchase-history', [ReportController::class, 'purchaseHistory']);
        Route::get('admin/reports/course-enrollments/{courseId}', [ReportController::class, 'courseEnrollments']);
        Route::get('admin/reports/payouts', [ReportController::class, 'instructorPayouts']);
        Route::get('admin/reports/export-revenue', [ReportController::class, 'exportRevenue']);

        // Newsletters
        Route::get('admin/newsletters', [NewsletterController::class, 'index']);
        Route::post('admin/newsletters', [NewsletterController::class, 'store']);
        Route::get('admin/newsletters/{id}', [NewsletterController::class, 'show']);
        Route::put('admin/newsletters/{id}', [NewsletterController::class, 'update']);
        Route::delete('admin/newsletters/{id}', [NewsletterController::class, 'destroy']);
        Route::post('admin/newsletters/{id}/send', [NewsletterController::class, 'send']);
        Route::get('admin/subscribers', [NewsletterController::class, 'subscribers']);
        Route::delete('admin/subscribers/{id}', [NewsletterController::class, 'deleteSubscriber']);
        Route::get('admin/newsletter-statistics', [NewsletterController::class, 'statistics']);

        // Contact Messages
        Route::get('admin/contacts', [ContactController::class, 'index']);
        Route::post('admin/contacts', [ContactController::class, 'store']);
        Route::get('admin/contacts/{id}', [ContactController::class, 'show']);
        Route::post('admin/contacts/{id}/reply', [ContactController::class, 'reply']);
        Route::delete('admin/contacts/{id}', [ContactController::class, 'destroy']);

        // Custom Fields
        Route::get('admin/custom-fields', [CustomFieldController::class, 'index']);
        Route::post('admin/custom-fields', [CustomFieldController::class, 'store']);
        Route::get('admin/custom-fields/{id}', [CustomFieldController::class, 'show']);
        Route::put('admin/custom-fields/{id}', [CustomFieldController::class, 'update']);
        Route::delete('admin/custom-fields/{id}', [CustomFieldController::class, 'destroy']);
        Route::get('admin/custom-fields/{id}/values', [CustomFieldController::class, 'values']);
        Route::post('admin/custom-fields/values', [CustomFieldController::class, 'saveValues']);
    });

    // ========== INSTRUCTOR ==========
    Route::middleware(['auth:web', 'instructor'])->group(function () {
        Route::get('instructor/courses', [InstructorCourseController::class, 'index']);
        Route::post('instructor/courses', [InstructorCourseController::class, 'store']);
        Route::get('instructor/courses/{id}', [InstructorCourseController::class, 'show']);
        Route::put('instructor/courses/{id}', [InstructorCourseController::class, 'update']);
        Route::delete('instructor/courses/{id}', [InstructorCourseController::class, 'destroy']);
        Route::post('instructor/courses/{id}/duplicate', [InstructorCourseController::class, 'duplicate']);
        Route::post('instructor/courses/{id}/draft', [InstructorCourseController::class, 'draft']);
        Route::post('instructor/courses/{id}/publish', [InstructorCourseController::class, 'publish']);
        Route::get('instructor/courses/{id}/enrollments', [InstructorCourseController::class, 'enrollments']);
        Route::get('instructor/courses/{id}/students-progress', [InstructorCourseController::class, 'studentsProgress']);
        Route::get('instructor/courses/{id}/revenue', [InstructorCourseController::class, 'revenue']);

        Route::post('instructor/sections', [SectionController::class, 'store']);
        Route::put('instructor/sections/{id}', [SectionController::class, 'update']);
        Route::delete('instructor/sections/{id}', [SectionController::class, 'destroy']);
        Route::post('instructor/sections/sort', [SectionController::class, 'sort']);

        Route::post('instructor/lessons', [InstructorLessonController::class, 'store']);
        Route::put('instructor/lessons/{id}', [InstructorLessonController::class, 'update']);
        Route::delete('instructor/lessons/{id}', [InstructorLessonController::class, 'destroy']);
        Route::post('instructor/lessons/sort', [InstructorLessonController::class, 'sort']);
        Route::get('instructor/lessons/{id}/views', [InstructorLessonController::class, 'views']);

        Route::post('instructor/quizzes', [QuizController::class, 'store']);
        Route::get('instructor/quizzes/{id}', [QuizController::class, 'show']);
        Route::put('instructor/quizzes/{id}', [QuizController::class, 'update']);
        Route::delete('instructor/quizzes/{id}', [QuizController::class, 'destroy']);
        Route::get('instructor/quizzes/{id}/questions', [QuizController::class, 'questions']);
        Route::post('instructor/quizzes/{id}/questions', [QuizController::class, 'storeQuestion']);
        Route::put('instructor/questions/{id}', [QuizController::class, 'updateQuestion']);
        Route::delete('instructor/questions/{id}', [QuizController::class, 'deleteQuestion']);
        Route::post('instructor/questions/sort', [QuizController::class, 'sortQuestions']);
        Route::get('instructor/quizzes/{id}/results', [QuizController::class, 'results']);
        Route::get('instructor/quiz-results/{submissionId}', [QuizController::class, 'resultPreview']);

        Route::post('instructor/assignments', [AssignmentController::class, 'store']);
        Route::put('instructor/assignments/{id}', [AssignmentController::class, 'update']);
        Route::delete('instructor/assignments/{id}', [AssignmentController::class, 'destroy']);
        Route::post('instructor/assignments/{id}/status', [AssignmentController::class, 'status']);
        Route::get('instructor/assignments/{id}/submissions', [AssignmentController::class, 'submissions']);
        Route::post('instructor/assignments/{id}/review', [AssignmentController::class, 'review']);
        Route::get('instructor/assignments/{id}/download/{submissionId}', [AssignmentController::class, 'downloadSubmission']);

        Route::get('instructor/bootcamps', [InstructorBootcampController::class, 'index']);
        Route::post('instructor/bootcamps', [InstructorBootcampController::class, 'store']);
        Route::get('instructor/bootcamps/{id}', [InstructorBootcampController::class, 'show']);
        Route::put('instructor/bootcamps/{id}', [InstructorBootcampController::class, 'update']);
        Route::delete('instructor/bootcamps/{id}', [InstructorBootcampController::class, 'destroy']);
        Route::post('instructor/bootcamps/{id}/status', [InstructorBootcampController::class, 'status']);
        Route::post('instructor/bootcamps/{id}/duplicate', [InstructorBootcampController::class, 'duplicate']);
        Route::get('instructor/bootcamps/{id}/purchase-history', [InstructorBootcampController::class, 'purchaseHistory']);
        Route::get('instructor/bootcamps/{id}/invoice', [InstructorBootcampController::class, 'invoice']);

        // ========== PAYOUTS ==========
        Route::get('payouts/balance', [PayoutController::class, 'instructorBalance']);
        Route::get('payouts', [PayoutController::class, 'instructorPayouts']);
        Route::post('payouts/request', [PayoutController::class, 'store']);
    });

    // ========== STUDENT ==========
    Route::middleware('auth:web')->group(function () {
        Route::get('my-courses', [MyCoursesController::class, 'index']);
        Route::get('my-courses/{id}', [MyCoursesController::class, 'show']);
        Route::get('my-courses/{id}/progress', [MyCoursesController::class, 'progress']);
        Route::get('my-courses/{id}/certificate', [MyCoursesController::class, 'certificate']);
        Route::get('my-courses/{id}/download-certificate', [MyCoursesController::class, 'downloadCertificate']);

        Route::get('my-bundles', [MyBundlesController::class, 'index']);
        Route::get('my-bundles/{slug}', [MyBundlesController::class, 'show']);
        Route::get('my-bundles/{id}/invoice', [MyBundlesController::class, 'invoice']);
        Route::get('my-bundles/purchase-history', [MyBundlesController::class, 'purchaseHistory']);

        Route::get('support/tickets', [SupportController::class, 'index']);
        Route::post('support/tickets', [SupportController::class, 'store']);
        Route::get('support/tickets/{id}', [SupportController::class, 'show']);
        Route::post('support/tickets/{id}/reply', [SupportController::class, 'reply']);
        Route::get('support/categories', [SupportController::class, 'categories']);
        Route::get('support/priorities', [SupportController::class, 'priorities']);

        Route::get('my-bookings', [TutorBookingController::class, 'myBookings']);
        Route::post('my-bookings', [TutorBookingController::class, 'book']);
        Route::get('my-bookings/{id}', [TutorBookingController::class, 'show']);
        Route::post('my-bookings/{id}/cancel', [TutorBookingController::class, 'cancel']);
        Route::get('my-bookings/{id}/invoice', [TutorBookingController::class, 'invoice']);
        Route::get('my-bookings/{id}/join', [TutorBookingController::class, 'joinClass']);
        Route::post('my-bookings/{id}/review', [TutorBookingController::class, 'review']);
    });

    // ========== SEARCH ==========
    Route::get('search', [SearchController::class, 'index']);

    // ========== PAYMENT GATEWAYS ==========
    Route::get('payment-gateways', [PaymentController::class, 'gateways']);
    Route::get('payment-gateways/{slug}', [PaymentController::class, 'gateway']);
    Route::post('payment-gateways/{slug}/checkout', [PaymentController::class, 'initiatePayment']);

    // ========== OFFLINE PAYMENTS ==========
    Route::get('offline-payment/bank-details', [OfflinePaymentController::class, 'bankDetails']);
    Route::middleware('auth:web')->group(function () {
        Route::post('offline-payment', [OfflinePaymentController::class, 'store']);
        Route::get('offline-payment/history', [OfflinePaymentController::class, 'index']);
    });

    // ========== CURRENCY ==========
    Route::get('currencies', [CurrencyController::class, 'index']);
    Route::get('currencies/rates', [CurrencyController::class, 'rates']);
    Route::get('currencies/convert', [CurrencyController::class, 'convert']);

    // ========== PLAYER SETTINGS ==========
    Route::middleware('auth:web')->group(function () {
        Route::get('player/settings', [PlayerSettingsController::class, 'index']);
        Route::put('player/settings', [PlayerSettingsController::class, 'update']);
        Route::post('player/settings/reset', [PlayerSettingsController::class, 'reset']);
        Route::get('player/settings/export', [PlayerSettingsController::class, 'export']);
        Route::post('player/settings/import', [PlayerSettingsController::class, 'import']);
        Route::get('player/settings/course/{courseId}', [PlayerSettingsController::class, 'forCourse']);
        Route::put('player/settings/course/{courseId}', [PlayerSettingsController::class, 'saveForCourse']);
        Route::get('player/settings/stats', [PlayerSettingsController::class, 'stats']);
        Route::post('player/settings/sync', [PlayerSettingsController::class, 'sync']);
    });

    // ========== WATERMARK ==========
    Route::middleware('auth:web')->group(function () {
        Route::post('watermark/generate', [WatermarkController::class, 'generate']);
        Route::post('watermark/video/{lessonId}', [WatermarkController::class, 'processVideo']);
        Route::post('watermark/batch', [WatermarkController::class, 'batchProcess']);
        Route::get('watermark/settings', [WatermarkController::class, 'settings']);
        Route::put('watermark/settings', [WatermarkController::class, 'settings']);
        Route::delete('watermark/video/{lessonId}', [WatermarkController::class, 'removeWatermark']);
    });
});

// ========== WEBHOOKS (No Auth) ==========
Route::prefix('v1/webhooks')->group(function () {
    Route::post('stripe', [WebhookController::class, 'handleStripe']);
    Route::post('razorpay', [WebhookController::class, 'handleRazorpay']);
    Route::post('paystack', [WebhookController::class, 'handlePaystack']);
    Route::post('paytm', [WebhookController::class, 'handlePaytm']);
    Route::post('sslcommerz', [WebhookController::class, 'handleSslcommerz']);
    Route::post('paypal', [WebhookController::class, 'handlePaypal']);
});

// ========== ADMIN API ==========
Route::prefix('v1/admin')->middleware(['auth:web', 'role:admin'])->group(function () {
    // Course Approvals
    Route::get('course-approvals/pending', [CourseApprovalController::class, 'pending']);
    Route::post('course-approvals/{id}/start-review', [CourseApprovalController::class, 'startReview']);
    Route::post('course-approvals/{id}/approve', [CourseApprovalController::class, 'approve']);
    Route::post('course-approvals/{id}/reject', [CourseApprovalController::class, 'reject']);
    Route::post('course-approvals/{id}/request-changes', [CourseApprovalController::class, 'requestChanges']);
    Route::get('course-approvals/{id}/history', [CourseApprovalController::class, 'history']);
    Route::get('course-approvals/stats', [CourseApprovalController::class, 'stats']);
    Route::post('course-approvals/bulk-approve', [CourseApprovalController::class, 'bulkApprove']);
    Route::put('course-approvals/auto-approve', [CourseApprovalController::class, 'setAutoApprove']);
    Route::get('course-approvals/{id}/auto-approve-check', [CourseApprovalController::class, 'checkAutoApprove']);

    // Payouts
    Route::get('payouts', [PayoutController::class, 'index']);
    Route::post('payouts', [PayoutController::class, 'store']);
    Route::get('payouts/{id}', [PayoutController::class, 'show']);
    Route::post('payouts/{id}/approve', [PayoutController::class, 'approve']);
    Route::post('payouts/{id}/reject', [PayoutController::class, 'reject']);
    Route::post('payouts/{id}/process', [PayoutController::class, 'process']);
    Route::get('payouts/stats', [PayoutController::class, 'stats']);
    Route::get('payouts/instructor/{userId}', [PayoutController::class, 'instructorPayouts']);
    Route::get('payouts/instructor/{userId}/balance', [PayoutController::class, 'instructorBalance']);
    Route::post('payouts/batch-approve', [PayoutController::class, 'batchApprove']);

    // Offline Payments
    Route::get('offline-payments', [OfflinePaymentController::class, 'index']);
    Route::get('offline-payments/{id}', [OfflinePaymentController::class, 'show']);
    Route::post('offline-payments/{id}/approve', [OfflinePaymentController::class, 'approve']);
    Route::post('offline-payments/{id}/reject', [OfflinePaymentController::class, 'reject']);
    Route::put('offline-payments/{id}', [OfflinePaymentController::class, 'update']);
    Route::get('offline-payments/stats', [OfflinePaymentController::class, 'stats']);
});

return $router;
