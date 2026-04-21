<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->integer('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title', 500);
            $table->string('slug', 500)->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->string('banner', 500)->nullable();
            $table->string('preview_video', 500)->nullable();
            $table->string('video_preview_type', 50)->nullable();
            $table->enum('course_type', ['video', 'text', 'mixed', 'live', 'bootcamp', 'bundle'])->default('video');
            $table->enum('status', ['draft', 'pending', 'approved', 'published', 'rejected', 'archived', 'private'])->default('draft');
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
            $table->string('language', 50)->default('en');
            $table->boolean('is_paid')->default(true);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->boolean('discount_enabled')->default(false);
            $table->timestamp('discount_start')->nullable();
            $table->timestamp('discount_end')->nullable();
            $table->boolean('is_free')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->boolean('enable_drip_content')->default(false);
            $table->json('drip_content_settings')->nullable();
            $table->boolean('enable_certificate')->default(true);
            $table->string('certificate_title', 255)->nullable();
            $table->integer('duration')->nullable()->comment('Duration in minutes');
            $table->integer('duration_text')->nullable()->comment('Human readable duration');
            $table->integer('sections_count')->default(0);
            $table->integer('lessons_count')->default(0);
            $table->integer('quizzes_count')->default(0);
            $table->integer('assignments_count')->default(0);
            $table->integer('enrollments_count')->default(0);
            $table->integer('completion_rate')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('ratings_count')->default(0);
            $table->integer('reviews_count')->default(0);
            $table->integer('five_star_count')->default(0);
            $table->integer('four_star_count')->default(0);
            $table->integer('three_star_count')->default(0);
            $table->integer('two_star_count')->default(0);
            $table->integer('one_star_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->integer('wishlists_count')->default(0);
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->decimal('instructor_revenue', 14, 2)->default(0);
            $table->decimal('admin_revenue', 14, 2)->default(0);
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->json('requirements')->nullable();
            $table->json('outcomes')->nullable();
            $table->json('faqs')->nullable();
            $table->json('includes')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_promoted')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_publish_at')->nullable();
            $table->integer('expiry_period')->nullable()->comment('Days after enrollment');
            $table->integer('max_access_days')->nullable()->comment('Maximum access days');
            $table->boolean('lifetime_access')->default(false);
            $table->boolean('allow_download')->default(false);
            $table->boolean('allow_guest_access')->default(false);
            $table->boolean('require_sequential_progress')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('uuid');
            $table->index('category_id');
            $table->index('status');
            $table->index('level');
            $table->index('is_paid');
            $table->index('is_featured');
            $table->index(['status', 'published_at']);
            $table->index(['is_paid', 'price']);
            $table->index(['average_rating', 'ratings_count']);
            $table->index('completion_rate');
            $table->index('total_revenue');
        });

        Schema::create('course_instructor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['owner', 'co_instructor', 'assistant', 'moderator'])->default('co_instructor');
            $table->decimal('revenue_share', 5, 2)->nullable();
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('is_accepted')->default(false);
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('course_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['course_id', 'tag_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('course_tag');
        Schema::dropIfExists('course_instructor');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('tags');
    }
};
