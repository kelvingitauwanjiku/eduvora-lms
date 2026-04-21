<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('meeting_provider', ['zoom', 'google_meet', 'microsoft_teams', 'custom', 'builtin'])->default('zoom');
            $table->string('meeting_id', 255)->nullable();
            $table->string('meeting_password', 100)->nullable();
            $table->string('join_url', 500)->nullable();
            $table->string('host_url', 500)->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('duration')->comment('Duration in minutes');
            $table->integer('buffer_time')->default(15)->comment('Buffer time before/after in minutes');
            $table->integer('max_participants')->nullable();
            $table->integer('enrolled_count')->default(0);
            $table->integer('attended_count')->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->json('recurrence_pattern')->nullable();
            $table->boolean('is_automatic_recording')->default(true);
            $table->boolean('allow_chat')->default(true);
            $table->boolean('allow_screen_share')->default(true);
            $table->boolean('allow_recording')->default(true);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_started')->default(false);
            $table->boolean('is_ended')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->text('cancellation_reason')->nullable();
            $table->string('recording_url', 500)->nullable();
            $table->integer('recording_duration')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('course_id');
            $table->index('scheduled_at');
            $table->index('is_published');
        });

        Schema::create('live_class_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->integer('attendance_duration')->comment('Duration in seconds');
            $table->boolean('is_present')->default(false);
            $table->boolean('is_host')->default(false);
            $table->boolean('is_co_host')->default(false);
            $table->enum('status', ['registered', 'joined', 'absent', 'late', 'left_early'])->default('registered');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['live_class_id', 'user_id']);
        });

        Schema::create('bootcamps', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 500);
            $table->string('slug', 500)->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->string('banner', 500)->nullable();
            $table->string('video_preview', 500)->nullable();
            $table->boolean('is_paid')->default(true);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->boolean('discount_enabled')->default(false);
            $table->timestamp('discount_start')->nullable();
            $table->timestamp('discount_end')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all_levels'])->default('all_levels');
            $table->enum('status', ['draft', 'pending', 'approved', 'published', 'rejected', 'archived'])->default('draft');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('max_students')->nullable();
            $table->integer('enrolled_count')->default(0);
            $table->integer('modules_count')->default(0);
            $table->integer('duration_days')->nullable();
            $table->integer('daily_hours')->nullable();
            $table->integer('total_hours')->nullable();
            $table->longText('faqs')->nullable();
            $table->longText('requirements')->nullable();
            $table->longText('outcomes')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->json('includes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('status');
            $table->index('is_featured');
            $table->index(['status', 'published_at']);
        });

        Schema::create('bootcamp_modules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('bootcamp_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('lessons_count')->default(0);
            $table->integer('duration_days')->nullable();
            $table->json('restrictions')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('bootcamp_id');
        });

        Schema::create('bootcamp_live_classes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('bootcamp_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('module_id')->nullable();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('duration')->comment('Duration in minutes');
            $table->string('meeting_provider', 50)->nullable();
            $table->string('meeting_id', 255)->nullable();
            $table->string('meeting_password', 100)->nullable();
            $table->string('join_url', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_recorded')->default(true);
            $table->string('recording_url', 500)->nullable();
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            $table->json('joining_data')->nullable();
            $table->boolean('force_stop')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('bootcamp_id');
            $table->index('module_id');
        });

        Schema::create('bootcamp_resources', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('bootcamp_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('module_id')->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->string('title', 255);
            $table->enum('upload_type', ['video', 'pdf', 'document', 'audio', 'image', 'archive', 'link'])->default('pdf');
            $table->string('file', 500)->nullable();
            $table->string('file_url', 500)->nullable();
            $table->string('file_name', 255)->nullable();
            $table->integer('file_size')->nullable();
            $table->integer('downloads_count')->default(0);
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_downloadable')->default(true);
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('bootcamp_id');
        });

        Schema::create('bootcamp_purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bootcamp_id')->constrained()->cascadeOnDelete();
            $table->string('order_number', 100)->unique();
            $table->string('invoice', 100)->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('coupon_discount', 12, 2)->default(0);
            $table->decimal('admin_revenue', 12, 2)->default(0);
            $table->decimal('instructor_revenue', 12, 2)->default(0);
            $table->string('payment_method', 100)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->text('payment_details')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->timestamp('access_start')->nullable();
            $table->timestamp('access_end')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'bootcamp_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bootcamp_purchases');
        Schema::dropIfExists('bootcamp_resources');
        Schema::dropIfExists('bootcamp_live_classes');
        Schema::dropIfExists('bootcamp_modules');
        Schema::dropIfExists('bootcamps');
        Schema::dropIfExists('live_class_participants');
        Schema::dropIfExists('live_classes');
    }
};
