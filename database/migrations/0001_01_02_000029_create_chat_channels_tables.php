<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_channels', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('type', 50)->default('text')->comment('text, voice, announcements');
            $table->string('icon', 500)->nullable();
            $table->foreignId('workspace_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('topic', 255)->nullable();
            $table->integer('position')->default(0);
            $table->boolean('is_private')->default(false);
            $table->boolean('is_read_only')->default(false);
            $table->boolean('allow_massages')->default(true);
            $table->boolean('allow_threads')->default(true);
            $table->boolean('allow_files')->default(true);
            $table->boolean('allow_links')->default(true);
            $table->boolean('allow_images')->default(true);
            $table->boolean('allow_emoji')->default(true);
            $table->boolean('allow_mentions')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->integer('members_count')->default(0);
            $table->integer('messages_count')->default(0);
            $table->integer('threads_count')->default(0);
            $table->integer('files_count')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('workspace_id');
            $table->index('type');
            $table->index('is_archived');
        });

        Schema::create('chat_channel_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('chat_channel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('role', 50)->default('member')->comment('owner, admin, moderator, member, guest');
            $table->boolean('can_post')->default(true);
            $table->boolean('can_reply')->default(true);
            $table->boolean('can_share')->default(true);
            $table->boolean('can_manage')->default(false);
            $table->boolean('is_muted')->default(false);
            $table->timestamp('muted_until')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->integer('unread_count')->default(0);
            $table->timestamps();

            $table->unique(['chat_channel_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('chat_channel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('thread_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('reply_to_id')->nullable()->constrained()->cascadeOnDelete();
            $table->longText('content')->nullable();
            $table->string('type', 50)->default('text')->comment('text, file, image, video, audio, link, system');
            $table->json('attachments')->nullable();
            $table->json('embeds')->nullable();
            $table->json('mentions')->nullable();
            $table->json('reactions')->nullable();
            $table->integer('reactions_count')->default(0);
            $table->integer('threads_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_slowmode')->default(false);
            $table->boolean('is_edited')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('chat_channel_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('thread_id');
            $table->index('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_channel_members');
        Schema::dropIfExists('chat_channels');
    }
};