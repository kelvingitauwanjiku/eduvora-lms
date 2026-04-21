<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('lessons_count')->default(0);
            $table->integer('quizzes_count')->default(0);
            $table->integer('duration')->nullable()->comment('Duration in minutes');
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('course_id');
            $table->index(['course_id', 'sort_order']);
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('lessons')->cascadeOnDelete();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->enum('lesson_type', ['video', 'text', 'pdf', 'audio', 'image', 'document', 'quiz', 'assignment', 'live', 'zoom', 'google_meet', 'iframe', 'external'])->default('video');
            $table->string('video_source', 50)->nullable();
            $table->string('video_url', 500)->nullable();
            $table->string('video_provider', 50)->nullable();
            $table->string('video_id', 255)->nullable();
            $table->integer('video_duration')->nullable()->comment('Duration in seconds');
            $table->string('thumbnail', 500)->nullable();
            $table->string('preview_video', 500)->nullable();
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_draft')->default(false);
            $table->integer('sort_order')->default(0);
            $table->integer('duration')->nullable()->comment('Duration in minutes');
            $table->integer('download_size')->nullable()->comment('Size in bytes');
            $table->string('attachment', 500)->nullable();
            $table->string('attachment_type', 100)->nullable();
            $table->string('attachment_name', 255)->nullable();
            $table->integer('download_count')->default(0);
            $table->json('video_properties')->nullable();
            $table->json('subtitles')->nullable();
            $table->json('chapters')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('course_id');
            $table->index('section_id');
            $table->index('lesson_type');
            $table->index('is_preview');
            $table->index(['course_id', 'sort_order']);
        });

        Schema::create('lesson_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('views_count')->default(0);
            $table->integer('total_watch_time')->default(0)->comment('In seconds');
            $table->timestamp('last_watched_at')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['lesson_id', 'user_id']);
            $table->index('last_watched_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_views');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('sections');
    }
};
