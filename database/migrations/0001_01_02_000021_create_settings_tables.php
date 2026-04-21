<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 100)->default('general');
            $table->string('name', 255)->unique();
            $table->string('display_name', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('type', 50)->default('text');
            $table->text('value')->nullable();
            $table->text('default_value')->nullable();
            $table->json('options')->nullable();
            $table->json('validation_rules')->nullable();
            $table->boolean('is_translatable')->default(false);
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('group');
            $table->index('is_active');
        });

        Schema::create('frontend_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 255)->unique();
            $table->text('value')->nullable();
            $table->string('type', 50)->default('text');
            $table->boolean('is_translatable')->default(false);
            $table->timestamps();

            $table->index('key');
        });

        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('type', 100)->unique();
            $table->string('name', 255);
            $table->string('description', 500)->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('addon_identifier', 100)->nullable();
            $table->json('user_types')->nullable();
            $table->boolean('system_notification')->default(true);
            $table->boolean('email_notification')->default(true);
            $table->boolean('push_notification')->default(true);
            $table->boolean('sms_notification')->default(false);
            $table->boolean('in_app_notification')->default(true);
            $table->string('subject', 255)->nullable();
            $table->text('template')->nullable();
            $table->text('shortcodes')->nullable();
            $table->string('setting_title', 255)->nullable();
            $table->string('setting_sub_title', 255)->nullable();
            $table->boolean('is_editable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('type');
            $table->index('is_active');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 255);
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('image', 500)->nullable();
            $table->string('action_url', 500)->nullable();
            $table->string('action_text', 100)->nullable();
            $table->json('data')->nullable();
            $table->enum('channel', ['database', 'mail', 'sms', 'push', 'broadcast'])->default('database');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_clicked')->default(false);
            $table->timestamp('clicked_at')->nullable();
            $table->boolean('is_broadcast')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('user_id');
            $table->index('type');
            $table->index('is_read');
            $table->index(['user_id', 'is_read']);
        });

        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('notification_type', 100);
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(true);
            $table->boolean('push_enabled')->default(true);
            $table->boolean('in_app_enabled')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'notification_type']);
        });

        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('device_id', 255)->nullable();
            $table->string('device_type', 50)->nullable()->comment('ios, android, web');
            $table->string('device_name', 255)->nullable();
            $table->string('device_token', 500)->nullable();
            $table->string('endpoint_arn', 500)->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('browser_version', 50)->nullable();
            $table->string('os', 100)->nullable();
            $table->string('os_version', 50)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'device_token']);
            $table->index('device_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('user_notification_settings');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_settings');
        Schema::dropIfExists('frontend_settings');
        Schema::dropIfExists('settings');
    }
};
