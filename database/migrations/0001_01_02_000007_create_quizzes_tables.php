<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->longText('summary')->nullable();
            $table->integer('duration')->nullable()->comment('Duration in minutes');
            $table->integer('time_limit')->nullable()->comment('Time limit in minutes per attempt');
            $table->integer('total_marks')->default(100);
            $table->integer('pass_marks')->default(50);
            $table->integer('attempts_allowed')->default(0)->comment('0 = unlimited');
            $table->integer('questions_count')->default(0);
            $table->integer('random_questions')->nullable()->comment('Number of random questions to show');
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('show_score')->default(true);
            $table->boolean('allow_review')->default(true);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_answers')->default(false);
            $table->enum('quiz_type', ['practice', 'graded', 'final'])->default('practice');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->decimal('average_score', 5, 2)->default(0);
            $table->integer('total_attempts')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0);
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('course_id');
            $table->index('section_id');
            $table->index('is_active');
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('quiz_questions')->cascadeOnDelete();
            $table->text('question');
            $table->text('explanation')->nullable();
            $table->enum('type', ['multiple_choice', 'multiple_select', 'true_false', 'short_answer', 'long_answer', 'fill_blank', 'matching', 'ordering', 'draggable'])->default('multiple_choice');
            $table->json('options')->nullable();
            $table->text('correct_answer')->nullable();
            $table->json('correct_answers')->nullable();
            $table->decimal('marks', 5, 2)->default(1);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('quiz_id');
            $table->index(['quiz_id', 'sort_order']);
        });

        Schema::create('quiz_submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->json('answers')->nullable();
            $table->json('correct_answers')->nullable();
            $table->json('wrong_answers')->nullable();
            $table->json('skipped_answers')->nullable();
            $table->integer('correct_count')->default(0);
            $table->integer('wrong_count')->default(0);
            $table->integer('skipped_count')->default(0);
            $table->integer('total_marks')->default(0);
            $table->integer('obtained_marks')->default(0);
            $table->decimal('score_percentage', 5, 2)->default(0);
            $table->decimal('time_taken', 10, 2)->default(0)->comment('Time taken in minutes');
            $table->enum('status', ['in_progress', 'completed', 'timed_out', 'abandoned'])->default('in_progress');
            $table->enum('result', ['passed', 'failed', 'pending'])->nullable();
            $table->boolean('is_graded')->default(false);
            $table->integer('attempt_number')->default(1);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['quiz_id', 'user_id', 'attempt_number']);
            $table->index('user_id');
            $table->index('status');
            $table->index('result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_submissions');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
