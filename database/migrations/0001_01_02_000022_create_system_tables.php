<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('folder', 255)->nullable();
            $table->string('file_name', 255);
            $table->string('original_name', 255);
            $table->string('mime_type', 100)->nullable();
            $table->string('extension', 20)->nullable();
            $table->string('size', 50)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->string('path', 500)->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('duration')->nullable()->comment('For video/audio in seconds');
            $table->json('variations')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('folder');
            $table->index('mime_type');
            $table->index('user_id');
        });

        Schema::create('seo_fields', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('blog_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('bootcamp_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('ebook_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('page_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('route', 255)->nullable();
            $table->string('name_route', 255)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_robot')->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->string('custom_url', 500)->nullable();
            $table->longText('json_ld')->nullable();
            $table->string('og_title', 255)->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image', 500)->nullable();
            $table->string('og_type', 50)->nullable();
            $table->string('og_url', 500)->nullable();
            $table->string('twitter_card', 50)->nullable();
            $table->string('twitter_title', 255)->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image', 500)->nullable();
            $table->boolean('is_index')->default(true);
            $table->boolean('is_follow')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('course_id');
            $table->index('blog_id');
            $table->index('route');
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('certificate_number', 100)->unique();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->json('template')->nullable();
            $table->string('qr_code', 500)->nullable();
            $table->string('pdf_url', 500)->nullable();
            $table->string('verify_url', 500)->nullable();
            $table->string('background_image', 500)->nullable();
            $table->string('logo', 500)->nullable();
            $table->string('signature', 500)->nullable();
            $table->decimal('grade', 5, 2)->nullable();
            $table->string('grade_text', 50)->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->boolean('is_shared')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('downloads_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'course_id']);
            $table->index('certificate_number');
        });

        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('image', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('require_authentication')->default(false);
            $table->integer('topics_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        Schema::create('knowledge_base_topics', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('knowledge_base_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->json('attachments')->nullable();
            $table->string('icon', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(true);
            $table->boolean('allow_comments')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('knowledge_base_id');
            $table->index('parent_id');
        });

        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->enum('type', ['text', 'textarea', 'number', 'select', 'multi_select', 'checkbox', 'radio', 'date', 'file', 'url', 'email', 'phone'])->default('text');
            $table->enum('model_type', ['course', 'user', 'blog', 'ebook', 'bootcamp'])->default('course');
            $table->text('description')->nullable();
            $table->text('placeholder')->nullable();
            $table->json('options')->nullable();
            $table->string('default_value')->nullable();
            $table->json('validation_rules')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_unique')->default(false);
            $table->boolean('is_searchable')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('show_on_table')->default(false);
            $table->boolean('show_on_form')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('model_type');
            $table->index('is_active');
        });

        Schema::create('custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_field_id')->constrained()->cascadeOnDelete();
            $table->foreignId('model_id')->constrained()->cascadeOnDelete();
            $table->string('model_type', 100);
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['custom_field_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_field_values');
        Schema::dropIfExists('custom_fields');
        Schema::dropIfExists('knowledge_base_topics');
        Schema::dropIfExists('knowledge_bases');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('seo_fields');
        Schema::dropIfExists('media');
    }
};
