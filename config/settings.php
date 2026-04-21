<?php

return [
    'tax_percentage' => env('TAX_PERCENTAGE', 0),
    'referral_enabled' => env('REFERRAL_ENABLED', true),
    'referral_reward' => env('REFERRAL_REWARD', 10),
    'new_user_reward' => env('NEW_USER_REWARD', 10),

    'course' => [
        'auto_approve' => env('COURSE_AUTO_APPROVE', false),
        'min_rating_for_autoapprove' => env('COURSE_MIN_RATING_FOR_AUTOAPPROVE', 4.5),
        'min_enrollments_for_autoapprove' => env('COURSE_MIN_ENROLLMENTS_FOR_AUTOAPPROVE', 10),
    ],

    'offline_payment' => [
        'bank_name' => env('OFFLINE_PAYMENT_BANK_NAME', ''),
        'account_number' => env('OFFLINE_PAYMENT_ACCOUNT_NUMBER', ''),
        'account_name' => env('OFFLINE_PAYMENT_ACCOUNT_NAME', ''),
        'routing_number' => env('OFFLINE_PAYMENT_ROUTING_NUMBER', ''),
        'swift_code' => env('OFFLINE_PAYMENT_SWIFT_CODE', ''),
        'iban' => env('OFFLINE_PAYMENT_IBAN', ''),
        'branch' => env('OFFLINE_PAYMENT_BRANCH', ''),
        'instructions' => env('OFFLINE_PAYMENT_INSTRUCTIONS', ''),
    ],
];
