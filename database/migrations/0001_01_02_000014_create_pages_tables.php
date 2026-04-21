<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->longText('content')->nullable();
            $table->string('template', 50)->nullable()->comment('blank, full_width, sidebar');
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->json('og_tags')->nullable();
            $table->string('featured_image', 500)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('show_in_footer')->default(false);
            $table->boolean('show_in_header')->default(false);
            $table->boolean('show_in_sitemap')->default(true);
            $table->boolean('require_authentication')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('views_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('is_published');
            $table->index('show_in_footer');
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title', 255);
            $table->text('content')->nullable();
            $table->string('link', 500)->nullable();
            $table->string('link_text', 100)->nullable();
            $table->string('image', 500)->nullable();
            $table->enum('type', ['info', 'success', 'warning', 'error', 'promo'])->default('info');
            $table->enum('target', ['all', 'students', 'instructors', 'admins', 'specific'])->default('all');
            $table->json('target_users')->nullable();
            $table->json('target_courses')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_dismissible')->default(true);
            $table->boolean('show_on_dashboard')->default(true);
            $table->boolean('show_on_course')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_published');
            $table->index('target');
            $table->index('starts_at');
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->enum('type', ['general', 'courses', 'payments', 'certificates', 'technical', 'account', 'instructors'])->default('general');
            $table->string('question', 500);
            $table->longText('answer')->nullable();
            $table->json('attachments')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('pages');
    }
};
