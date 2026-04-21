<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->unsignedBigInteger('coupon_usage_id')->nullable();
            $table->enum('enrollment_type', ['free', 'paid', 'gift', 'coupon', 'affiliate', 'admin', 'subscription', 'team'])->default('paid');
            $table->decimal('original_price', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('admin_revenue', 12, 2)->default(0);
            $table->decimal('instructor_revenue', 12, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'expired', 'cancelled', 'refunded', 'pending', 'suspended'])->default('active');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->boolean('lifetime_access')->default(false);
            $table->integer('completion_percentage')->default(0);
            $table->integer('lessons_completed')->default(0);
            $table->integer('total_lessons')->default(0);
            $table->integer('quizzes_completed')->default(0);
            $table->integer('assignments_completed')->default(0);
            $table->timestamp('first_lesson_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('total_time_spent')->default(0)->comment('In minutes');
            $table->integer('certificate_id')->nullable();
            $table->string('certificate_url', 500)->nullable();
            $table->string('invoice_id', 100)->nullable();
            $table->string('invoice_url', 500)->nullable();
            $table->json('progress_data')->nullable();
            $table->json('completed_lessons')->nullable();
            $table->json('completed_quizzes')->nullable();
            $table->json('completed_assignments')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'course_id']);
            $table->index('user_id');
            $table->index('course_id');
            $table->index('status');
            $table->index('expiry_date');
            $table->index('completion_percentage');
        });

        Schema::create('course_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('item_type', ['lesson', 'quiz', 'assignment', 'section'])->default('lesson');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'skipped'])->default('not_started');
            $table->integer('progress_percentage')->default(0);
            $table->integer('time_spent')->default(0)->comment('In seconds');
            $table->integer('attempts')->default(0);
            $table->integer('best_score')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id', 'item_type', 'lesson_id'], 'cp_user_course_type_lesson');
            $table->index(['user_id', 'course_id']);
        });

        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->json('completed_lessons')->nullable();
            $table->string('current_lesson_id', 100)->nullable();
            $table->integer('course_progress')->default(0);
            $table->string('completed_date', 50)->nullable();
            $table->boolean('is_completed')->default(false);
            $table->integer('total_watch_time')->default(0)->comment('In seconds');
            $table->timestamp('last_watched_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
            $table->index('last_watched_at');
        });

        Schema::create('watch_durations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->integer('current_duration')->default(0)->comment('In seconds');
            $table->integer('total_duration')->default(0)->comment('In seconds');
            $table->integer('watched_counter')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('last_watched_at')->nullable();
            $table->json('watch_segments')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_durations');
        Schema::dropIfExists('watch_histories');
        Schema::dropIfExists('course_progress');
        Schema::dropIfExists('enrollments');
    }
};
