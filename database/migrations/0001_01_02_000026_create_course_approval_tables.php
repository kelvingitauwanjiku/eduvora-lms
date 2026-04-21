<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_approval_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->json('checklist')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('read_status', ['unread', 'read'])->default('unread');
            $table->enum('status', ['pending', 'approved', 'rejected', 'revision_requested'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->integer('revision_count')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('course_id');
            $table->index('user_id');
            $table->index('status');
        });

        Schema::create('course_revisions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approval_request_id')->nullable()->constrained()->cascadeOnDelete();
            $table->json('changes')->nullable();
            $table->json('previous_data')->nullable();
            $table->json('new_data')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->integer('version')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index('course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_revisions');
        Schema::dropIfExists('course_approval_requests');
    }
};
