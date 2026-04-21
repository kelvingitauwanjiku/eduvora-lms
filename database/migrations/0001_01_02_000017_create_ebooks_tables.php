<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebooks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 500);
            $table->string('slug', 500)->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('author_name', 255)->nullable();
            $table->string('publisher', 255)->nullable();
            $table->string('isbn', 50)->nullable();
            $table->integer('edition')->nullable();
            $table->integer('pages')->nullable();
            $table->string('language', 50)->default('en');
            $table->string('published_date', 50)->nullable();
            $table->boolean('is_paid')->default(true);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->boolean('discount_enabled')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->string('thumbnail', 500)->nullable();
            $table->string('banner', 500)->nullable();
            $table->string('preview_file', 500)->nullable();
            $table->string('preview_pages')->nullable();
            $table->string('full_file', 500)->nullable();
            $table->string('file_type', 50)->nullable();
            $table->integer('file_size')->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'published', 'rejected', 'archived'])->default('draft');
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('ratings_count')->default(0);
            $table->integer('reviews_count')->default(0);
            $table->integer('downloads_count')->default(0);
            $table->integer('reads_count')->default(0);
            $table->integer('wishlists_count')->default(0);
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->json('tags')->nullable();
            $table->json('includes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('status');
            $table->index('is_featured');
            $table->index('is_paid');
        });

        Schema::create('ebook_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('parent_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 255)->nullable();
            $table->string('image', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('ebooks_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('parent_id');
        });

        Schema::create('ebook_purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ebook_id')->constrained()->cascadeOnDelete();
            $table->string('order_number', 100)->unique();
            $table->string('session_id', 255)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->string('invoice', 100)->nullable();
            $table->string('invoice_url', 500)->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('coupon_discount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('payment_type', 100)->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->decimal('admin_revenue', 12, 2)->default(0);
            $table->decimal('instructor_revenue', 12, 2)->default(0);
            $table->timestamp('payment_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'ebook_id']);
            $table->index('status');
        });

        Schema::create('ebook_reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ebook_id')->constrained()->cascadeOnDelete();
            $table->integer('rating')->unsigned();
            $table->text('review')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_verified_purchase')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'ebook_id']);
            $table->index('ebook_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_reviews');
        Schema::dropIfExists('ebook_purchases');
        Schema::dropIfExists('ebook_categories');
        Schema::dropIfExists('ebooks');
    }
};
