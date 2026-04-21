<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'Eduvora', 'group' => 'general'],
            ['key' => 'site_title', 'value' => 'Eduvora - Learning Management System', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Premium Online Learning Platform', 'group' => 'general'],
            ['key' => 'site_keywords', 'value' => 'online courses, learning, education, tutorials', 'group' => 'general'],
            ['key' => 'site_email', 'value' => 'hello@eduvora.com', 'group' => 'general'],
            ['key' => 'site_phone', 'value' => '+1 234 567 890', 'group' => 'general'],
            ['key' => 'site_address', 'value' => '123 Learning Street, Education City', 'group' => 'general'],
            ['key' => 'default_language', 'value' => 'en', 'group' => 'general'],
            ['key' => 'default_currency', 'value' => 'USD', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'UTC', 'group' => 'general'],

            // Branding
            ['key' => 'site_logo', 'value' => '', 'group' => 'branding'],
            ['key' => 'site_favicon', 'value' => '', 'group' => 'branding'],
            ['key' => 'primary_color', 'value' => '#4F46E5', 'group' => 'branding'],
            ['key' => 'secondary_color', 'value' => '#10B981', 'group' => 'branding'],
            ['key' => 'accent_color', 'value' => '#F59E0B', 'group' => 'branding'],

            // Features
            ['key' => 'enable_blog', 'value' => '1', 'group' => 'features'],
            ['key' => 'enable_certificate', 'value' => '1', 'group' => 'features'],
            ['key' => 'enable_zoom', 'value' => '1', 'group' => 'features'],
            ['key' => 'enable_course_bundle', 'value' => '1', 'group' => 'features'],
            ['key' => 'enable_bootcamp', 'value' => '1', 'group' => 'features'],
            ['key' => 'enable_ebook', 'value' => '1', 'group' => 'features'],
            ['key' => 'enable_tutor', 'value' => '1', 'group' => 'features'],
            ['key' => 'enable_subscription', 'value' => '1', 'group' => 'features'],
            ['key' => 'enable_offline_payment', 'value' => '1', 'group' => 'features'],
            ['key' => 'enable_coupon', 'value' => '1', 'group' => 'features'],

            // Course
            ['key' => 'default_course_limit', 'value' => '10', 'group' => 'course'],
            ['key' => 'default_video_size', 'value' => '500', 'group' => 'course'],
            ['key' => 'default_enrollment_limit', 'value' => '30', 'group' => 'course'],
            ['key' => 'enable_course_review', 'value' => '1', 'group' => 'course'],
            ['key' => 'enable_qna', 'value' => '1', 'group' => 'course'],
            ['key' => 'enable_announcement', 'value' => '1', 'group' => 'course'],

            // Payment Settings
            ['key' => 'tax_enabled', 'value' => '0', 'group' => 'payment'],
            ['key' => 'tax_percentage', 'value' => '0', 'group' => 'payment'],

            // Email Settings
            ['key' => 'mail_mailer', 'value' => 'smtp', 'group' => 'email'],
            ['key' => 'mail_host', 'value' => '', 'group' => 'email'],
            ['key' => 'mail_port', 'value' => '587', 'group' => 'email'],
            ['key' => 'mail_username', 'value' => '', 'group' => 'email'],
            ['key' => 'mail_password', 'value' => '', 'group' => 'email'],
            ['key' => 'mail_from_address', 'value' => 'noreply@eduvora.com', 'group' => 'email'],
            ['key' => 'mail_from_name', 'value' => 'Eduvora', 'group' => 'email'],

            // Social
            ['key' => 'social_facebook', 'value' => '', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => '', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => '', 'group' => 'social'],
            ['key' => 'social_youtube', 'value' => '', 'group' => 'social'],
            ['key' => 'social_linkedin', 'value' => '', 'group' => 'social'],

            // PWA
            ['key' => 'pwa_enabled', 'value' => '0', 'group' => 'pwa'],
            ['key' => 'pwa_name', 'value' => 'Eduvora', 'group' => 'pwa'],
            ['key' => 'pwa_short_name', 'value' => 'Eduvora', 'group' => 'pwa'],
            ['key' => 'pwa_theme_color', 'value' => '#4F46E5', 'group' => 'pwa'],
            ['key' => 'pwa_background_color', 'value' => '#ffffff', 'group' => 'pwa'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
