<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('ticket_number', 50)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('priority_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('subject', 500);
            $table->text('description')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('type', ['question', 'bug', 'feature_request', 'billing', 'other'])->default('question');
            $table->enum('source', ['website', 'mobile_app', 'email', 'api'])->default('website');
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('responses_count')->default(0);
            $table->enum('status', ['open', 'pending', 'resolved', 'closed', 'spam'])->default('open');
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('last_reply_at')->nullable();
            $table->float('response_time')->nullable()->comment('Response time in hours');
            $table->float('resolution_time')->nullable()->comment('Resolution time in hours');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('assigned_to');
            $table->index('status');
            $table->index('priority_id');
        });

        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('parent_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('tickets_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ticket_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('ticket_status', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->boolean('require_action')->default(false);
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('message');
            $table->json('attachments')->nullable();
            $table->boolean('is_internal')->default(false)->comment('Internal notes not visible to user');
            $table->boolean('is_note')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('ticket_id');
            $table->index('user_id');
        });

        Schema::create('ticket_macros', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->longText('message_template')->nullable();
            $table->json('actions')->nullable();
            $table->json('attachments')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('priority_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        Schema::create('ticket_faqs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('question', 500);
            $table->longText('answer')->nullable();
            $table->json('attachments')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('views_count')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('phone', 50)->nullable();
            $table->string('subject', 255)->nullable();
            $table->text('address')->nullable();
            $table->text('message');
            $table->enum('type', ['general', 'support', 'sales', 'partnership', 'feedback'])->default('general');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_replied')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_important')->default(false);
            $table->enum('status', ['new', 'read', 'replied', 'archived'])->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('is_read');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('ticket_faqs');
        Schema::dropIfExists('ticket_macros');
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('ticket_status');
        Schema::dropIfExists('ticket_priorities');
        Schema::dropIfExists('ticket_categories');
        Schema::dropIfExists('tickets');
    }
};
