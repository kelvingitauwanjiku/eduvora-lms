<?php

return [
    'app' => [
        'name' => env('APP_NAME', 'Eduvora'),
        'env' => env('APP_ENV', 'production'),
        'debug' => (bool) env('APP_DEBUG', false),
        'url' => env('APP_URL', 'http://localhost'),
        'timezone' => 'UTC',
        'locale' => 'en',
        'fallback_locale' => 'en',
        'faker_locale' => 'en_US',
    ],

    'short_name' => 'Eduvora',
    'email' => 'support@eduvora.com',
    'address' => '123 Learning Street, Education City',

    'payment' => [
        'stripe_key' => env('STRIPE_KEY'),
        'stripe_secret' => env('STRIPE_SECRET'),
        'stripe_webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'paypal_client_id' => env('PAYPAL_CLIENT_ID'),
        'paypal_secret' => env('PAYPAL_SECRET'),
        'razorpay_key' => env('RAZORPAY_KEY'),
        'razorpay_secret' => env('RAZORPAY_SECRET'),
    ],

    'oauth' => [
        'google_client_id' => env('GOOGLE_CLIENT_ID'),
        'google_client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'facebook_client_id' => env('FACEBOOK_CLIENT_ID'),
        'facebook_client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'apple_client_id' => env('APPLE_CLIENT_ID'),
        'apple_client_secret' => env('APPLE_CLIENT_SECRET'),
        'apple_team_id' => env('APPLE_TEAM_ID'),
        'apple_key_id' => env('APPLE_KEY_ID'),
    ],

    'mail' => [
        'mailer' => env('MAIL_MAILER', 'smtp'),
        'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
        'port' => env('MAIL_PORT', 587),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@eduvora.com'),
        'from_name' => env('MAIL_FROM_NAME', 'Eduvora'),
    ],

    'storage' => [
        'disk' => env('FILESYSTEM_DISK', 'local'),
        'path' => env('UPLOAD_PATH', 'uploads'),
        'max_size' => env('MAX_UPLOAD_SIZE', 10240), // KB
    ],

    'pwa' => [
        'version' => '1.0.0',
        'theme_color' => '#1a73e8',
        'background_color' => '#ffffff',
    ],

    'ai' => [
        'provider' => env('AI_PROVIDER', 'openai'),
        'openai_key' => env('OPENAI_API_KEY'),
        'openai_model' => env('OPENAI_MODEL', 'gpt-4'),
    ],

    'websocket' => [
        'enabled' => env('WEBSOCKET_ENABLED', true),
        'pusher_key' => env('PUSHER_KEY'),
        'pusher_secret' => env('PUSHER_SECRET'),
        'pusher_app_id' => env('PUSHER_APP_ID'),
        'pusher_cluster' => env('PUSHER_CLUSTER', 'mt1'),
    ],

    'settings' => [
        'tax_percentage' => 0,
        'referral_enabled' => true,
        'referral_reward' => 10,
        'new_user_reward' => 10,
    ],

    'notification_preferences' => [
        'course_enrolled' => ['email' => true, 'push' => true],
        'course_completed' => ['email' => true, 'push' => true],
        'new_review' => ['email' => true, 'push' => false],
        'support_reply' => ['email' => true, 'push' => true],
        'new_course' => ['email' => false, 'push' => true],
        'promotional' => ['email' => false, 'push' => false],
        'payment' => ['email' => true, 'push' => true],
    ],

    'features' => [
        'ai_generation' => ['name' => 'AI Content Generation', 'description' => 'Generate course content using AI', 'enabled' => false, 'rollout' => 100, 'roles' => [2], 'users' => []],
        'live_classes' => ['name' => 'Live Classes', 'description' => 'Host live classes', 'enabled' => false, 'rollout' => 100, 'roles' => [2], 'users' => []],
        'certificates' => ['name' => 'Certificates', 'description' => 'Generate course completion certificates', 'enabled' => true, 'rollout' => 100, 'roles' => [], 'users' => []],
        'chat' => ['name' => 'Chat', 'description' => 'Real-time chat', 'enabled' => true, 'rollout' => 100, 'roles' => [], 'users' => []],
    ],
];