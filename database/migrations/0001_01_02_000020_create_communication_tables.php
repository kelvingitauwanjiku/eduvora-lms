<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_threads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('thread_number', 50)->unique();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['direct', 'course', 'group', 'system'])->default('direct');
            $table->string('subject', 255)->nullable();
            $table->text('last_message')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->integer('messages_count')->default(0);
            $table->json('participants')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_muted')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sender_id', 'receiver_id']);
            $table->index('last_message_at');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('thread_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('reply_to_id')->nullable()->constrained('messages')->cascadeOnDelete();
            $table->longText('message')->nullable();
            $table->json('attachments')->nullable();
            $table->json('images')->nullable();
            $table->enum('type', ['text', 'file', 'image', 'video', 'audio', 'location', 'contact', 'system'])->default('text');
            $table->boolean('is_forwarded')->default(false);
            $table->boolean('is_read')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_edited')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('read_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('thread_id');
            $table->index('user_id');
            $table->index('is_read');
        });

        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 500)->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->json('attachments')->nullable();
            $table->json('images')->nullable();
            $table->json('likes')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('dislikes_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_solved')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_edited')->default(false);
            $table->enum('status', ['active', 'pending', 'spam', 'deleted'])->default('active');
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('course_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('is_pinned');
            $table->index('is_solved');
        });

        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('forum_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained()->cascadeOnDelete();
            $table->longText('content');
            $table->json('attachments')->nullable();
            $table->json('likes')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('dislikes_count')->default(0);
            $table->boolean('is_accepted')->default(false)->comment('Marked as best answer');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_edited')->default(false);
            $table->enum('status', ['active', 'pending', 'spam', 'deleted'])->default('active');
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('forum_id');
            $table->index('user_id');
            $table->index('is_accepted');
        });

        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('subject', 500);
            $table->string('preheader', 255)->nullable();
            $table->longText('content')->nullable();
            $table->text('plain_content')->nullable();
            $table->json('template')->nullable();
            $table->json('images')->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->enum('type', ['general', 'promotional', 'announcement', 'news', 'update'])->default('general');
            $table->enum('target', ['all', 'students', 'instructors', 'subscribers', 'specific'])->default('all');
            $table->json('target_filters')->nullable();
            $table->json('courses')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_scheduled')->default(false);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('unsubscribed_count')->default(0);
            $table->integer('bounced_count')->default(0);
            $table->decimal('open_rate', 5, 2)->default(0);
            $table->decimal('click_rate', 5, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_scheduled');
            $table->index('sent_at');
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('email')->unique();
            $table->string('name', 255)->nullable();
            $table->string('token', 100)->unique();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_subscribed')->default(true);
            $table->enum('source', ['website', 'import', 'api', 'checkout'])->default('website');
            $table->json('preferences')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('last_email_at')->nullable();
            $table->integer('emails_received')->default(0);
            $table->integer('emails_opened')->default(0);
            $table->timestamps();

            $table->index('is_subscribed');
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('newsletters');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forums');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('message_threads');
    }
};
