<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('ebook_id')->nullable();
            $table->unsignedBigInteger('bootcamp_id')->nullable();
            $table->unsignedBigInteger('bundle_id')->nullable();
            $table->enum('item_type', ['course', 'ebook', 'bootcamp', 'bundle', 'consultation', 'service'])->default('course');
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->string('coupon_code', 100)->nullable();
            $table->decimal('coupon_discount', 12, 2)->default(0);
            $table->boolean('is_applied_coupon')->default(false);
            $table->integer('quantity')->default(1);
            $table->boolean('is_valid')->default(true);
            $table->string('invalid_reason', 255)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('user_id');
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('ebook_id')->nullable();
            $table->unsignedBigInteger('bootcamp_id')->nullable();
            $table->enum('item_type', ['course', 'ebook', 'bootcamp', 'bundle'])->default('course');
            $table->string('notes', 500)->nullable();
            $table->integer('priority')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('user_id');
            $table->index('course_id');
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code', 100)->unique();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed', 'buy_x_get_y'])->default('percentage');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('max_discount_amount', 12, 2)->nullable();
            $table->decimal('min_purchase_amount', 12, 2)->nullable();
            $table->decimal('min_course_price', 12, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->integer('per_user_limit')->default(1);
            $table->boolean('is_global')->default(false);
            $table->json('course_ids')->nullable();
            $table->json('category_ids')->nullable();
            $table->json('user_ids')->nullable();
            $table->boolean('first_purchase_only')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['active', 'inactive', 'expired', 'scheduled'])->default('active');
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('status');
            $table->index('valid_until');
        });

        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('order_id', 100)->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('order_amount', 12, 2)->default(0);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->unique(['coupon_id', 'user_id', 'course_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('cart_items');
    }
};
