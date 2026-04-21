<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('rating')->unsigned();
            $table->text('review')->nullable();
            $table->string('title', 255)->nullable();
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            $table->boolean('is_recommended')->default(true);
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('helpful_count')->default(0);
            $table->integer('report_count')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'hidden'])->default('approved');
            $table->text('admin_notes')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('instructor_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'course_id']);
            $table->index('course_id');
            $table->index('rating');
            $table->index('is_published');
            $table->index(['course_id', 'rating']);
        });

        Schema::create('review_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_helpful')->default(false);
            $table->boolean('is_not_helpful')->default(false);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['review_id', 'user_id']);
        });

        Schema::create('review_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('reason', ['spam', 'inappropriate', 'fake', 'harassment', 'off_topic', 'other'])->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['review_id', 'user_id']);
        });

        Schema::create('instructor_reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('rating')->unsigned();
            $table->text('review')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('helpful_count')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->text('instructor_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'instructor_id']);
            $table->index('instructor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_reviews');
        Schema::dropIfExists('review_reports');
        Schema::dropIfExists('review_reactions');
        Schema::dropIfExists('reviews');
    }
};
