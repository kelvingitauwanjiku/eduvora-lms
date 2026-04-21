<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 500);
            $table->string('slug', 500)->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->string('banner', 500)->nullable();
            $table->string('video_url', 500)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->json('og_tags')->nullable();
            $table->json('structured_data')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('need_authentication')->default(false);
            $table->enum('status', ['draft', 'pending', 'published', 'scheduled', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('bookmarks_count')->default(0);
            $table->string('reading_time', 50)->nullable();
            $table->json('tags')->nullable();
            $table->json('related_blogs')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('category_id');
            $table->index('slug');
            $table->index('status');
            $table->index(['status', 'published_at']);
            $table->index('is_featured');
            $table->index('view_count');
        });

        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('blog_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('blog_comments')->cascadeOnDelete();
            $table->foreignId('replied_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('comment');
            $table->json('attachments')->nullable();
            $table->json('likes')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('dislikes_count')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_reported')->default(false);
            $table->enum('status', ['pending', 'approved', 'spam', 'deleted'])->default('approved');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('blog_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('is_approved');
        });

        Schema::create('blog_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_liked')->default(true);
            $table->timestamp('created_at')->nullable();

            $table->unique(['blog_id', 'user_id']);
        });

        Schema::create('blog_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('notes', 500)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->unique(['blog_id', 'user_id']);
        });

        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('blog_categories')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 255)->nullable();
            $table->string('image', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('blogs_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('parent_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('blog_bookmarks');
        Schema::dropIfExists('blog_likes');
        Schema::dropIfExists('blog_comments');
        Schema::dropIfExists('blogs');
    }
};
