<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();
            $table->longText('questions')->nullable();
            $table->json('attachments')->nullable();
            $table->string('question_file', 500)->nullable();
            $table->integer('total_marks')->default(100);
            $table->integer('pass_marks')->default(40);
            $table->integer('time_limit')->nullable()->comment('Time limit in minutes');
            $table->integer('attempts_allowed')->default(1);
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->boolean('allow_late_submission')->default(false);
            $table->integer('late_penalty_percentage')->default(0);
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_required')->default(true);
            $table->boolean('resubmission_allowed')->default(false);
            $table->integer('max_resubmissions')->default(0);
            $table->integer('submissions_count')->default(0);
            $table->decimal('average_score', 5, 2)->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0);
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('course_id');
            $table->index('is_active');
            $table->index('deadline');
        });

        Schema::create('submitted_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->longText('answer')->nullable();
            $table->json('attachments')->nullable();
            $table->string('submission_file', 500)->nullable();
            $table->text('note')->nullable();
            $table->integer('submission_number')->default(1);
            $table->integer('marks')->nullable();
            $table->integer('percentage')->nullable();
            $table->text('feedback')->nullable();
            $table->text('instructor_remarks')->nullable();
            $table->enum('status', ['submitted', 'graded', 'returned', 'late', 'missed'])->default('submitted');
            $table->enum('result', ['passed', 'failed', 'pending', 'excellent', 'needs_improvement'])->nullable();
            $table->boolean('is_late')->default(false);
            $table->integer('late_penalty_applied')->default(0);
            $table->timestamp('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('notified')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['assignment_id', 'user_id', 'submission_number'], 'sub_assign_user_num');
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submitted_assignments');
        Schema::dropIfExists('assignments');
    }
};
