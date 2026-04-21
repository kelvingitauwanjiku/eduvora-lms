<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 255)->nullable();
            $table->string('image', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('tutors_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        Schema::create('tutor_subjects', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('tutors_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
        });

        Schema::create('tutors', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255)->nullable();
            $table->text('tagline')->nullable();
            $table->longText('description')->nullable();
            $table->string('profile_image', 500)->nullable();
            $table->string('video_intro', 500)->nullable();
            $table->json('teaching_experience')->nullable();
            $table->json('teaching_methodology')->nullable();
            $table->json('certifications')->nullable();
            $table->string('hourly_rate', 50)->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->integer('sessions_count')->default(0);
            $table->integer('students_count')->default(0);
            $table->integer('total_hours_taught')->default(0);
            $table->string('languages', 255)->nullable();
            $table->string('timezone', 100)->nullable();
            $table->enum('teaching_mode', ['online', 'offline', 'both'])->default('both');
            $table->json('available_days')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('user_id');
            $table->index('is_active');
            $table->index('is_featured');
        });

        Schema::create('tutor_can_teach', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tutor_id', 'category_id', 'subject_id']);
        });

        Schema::create('tutor_schedules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tutor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 255)->nullable();
            $table->string('day_of_week', 20);
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration')->default(60)->comment('Duration in minutes');
            $table->enum('session_type', ['one_on_one', 'group'])->default('one_on_one');
            $table->integer('max_students')->default(1);
            $table->integer('booked_slots')->default(0);
            $table->decimal('price_per_session', 10, 2)->nullable();
            $table->decimal('group_price', 10, 2)->nullable();
            $table->enum('tution_type', ['online', 'offline', 'both'])->default('online');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_recurring')->default(true);
            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_until')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tutor_id');
            $table->index('day_of_week');
            $table->index('is_active');
        });

        Schema::create('tutor_bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tutor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('invoice', 100)->unique();
            $table->string('title', 255)->nullable();
            $table->timestamp('session_date')->nullable();
            $table->string('start_time', 50)->nullable();
            $table->string('end_time', 50)->nullable();
            $table->integer('duration')->comment('Duration in minutes');
            $table->enum('session_type', ['one_on_one', 'group'])->default('one_on_one');
            $table->enum('teaching_mode', ['online', 'offline', 'video_call'])->default('online');
            $table->string('meeting_link', 500)->nullable();
            $table->string('meeting_id', 255)->nullable();
            $table->string('meeting_password', 100)->nullable();
            $table->longText('joining_data')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('admin_revenue', 10, 2)->default(0);
            $table->decimal('tutor_revenue', 10, 2)->default(0);
            $table->string('payment_method', 100)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->text('payment_details')->nullable();
            $table->text('student_notes')->nullable();
            $table->text('tutor_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'rescheduled', 'no_show'])->default('pending');
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_rated')->default(false);
            $table->integer('rating')->nullable();
            $table->text('review')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('actual_duration')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tutor_id');
            $table->index('student_id');
            $table->index('status');
            $table->index('session_date');
        });

        Schema::create('tutor_reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tutor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('rating')->unsigned();
            $table->text('review')->nullable();
            $table->json('ratings_breakdown')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->text('tutor_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tutor_id', 'student_id']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_reviews');
        Schema::dropIfExists('tutor_bookings');
        Schema::dropIfExists('tutor_schedules');
        Schema::dropIfExists('tutor_can_teach');
        Schema::dropIfExists('tutors');
        Schema::dropIfExists('tutor_subjects');
        Schema::dropIfExists('tutor_categories');
    }
};
