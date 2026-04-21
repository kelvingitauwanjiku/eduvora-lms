<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 500);
            $table->string('slug', 500)->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->string('banner', 500)->nullable();
            $table->boolean('is_paid')->default(true);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->boolean('discount_enabled')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('courses_count')->default(0);
            $table->integer('enrollments_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });

        Schema::create('bundle_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->unique(['bundle_id', 'course_id']);
        });

        Schema::create('team_training_packages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('bundle_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->string('preview_video', 500)->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('price_per_user', 12, 2)->nullable();
            $table->string('course_privacy', 50)->default('private')->comment('private, team, organization');
            $table->integer('allocation')->comment('Number of user allocations');
            $table->integer('min_allocation')->default(1);
            $table->integer('max_allocation')->nullable();
            $table->enum('expiry_type', ['days', 'months', 'yearly', 'lifetime'])->default('yearly');
            $table->integer('expiry_value')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->json('features')->nullable();
            $table->json('included_courses')->nullable();
            $table->json('user_roles')->nullable();
            $table->enum('pricing_type', ['fixed', 'per_user', 'tiered'])->default('per_user');
            $table->json('tiered_pricing')->nullable();
            $table->boolean('allow_upgrade')->default(false);
            $table->decimal('upgrade_price', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('purchases_count')->default(0);
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        Schema::create('team_package_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('package_purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leader_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('member_email', 255)->nullable();
            $table->string('member_name', 255)->nullable();
            $table->enum('status', ['pending', 'active', 'expired', 'removed'])->default('pending');
            $table->enum('role', ['leader', 'member', 'admin'])->default('member');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('package_purchase_id');
            $table->index('member_id');
        });

        Schema::create('team_package_purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('order_number', 100)->unique();
            $table->string('invoice', 100)->nullable();
            $table->string('invoice_url', 500)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('company_address', 500)->nullable();
            $table->string('company_vat', 50)->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('coupon_discount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('admin_revenue', 12, 2)->default(0);
            $table->decimal('instructor_revenue', 12, 2)->default(0);
            $table->string('payment_method', 100)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->text('payment_details')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->timestamp('subscription_start')->nullable();
            $table->timestamp('subscription_end')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->integer('members_count')->default(0);
            $table->integer('max_members')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });

        Schema::create('bundle_purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bundle_id')->constrained()->cascadeOnDelete();
            $table->string('order_number', 100)->unique();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('payment_method', 100)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->timestamp('payment_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'bundle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_purchases');
        Schema::dropIfExists('team_package_purchases');
        Schema::dropIfExists('team_package_members');
        Schema::dropIfExists('team_training_packages');
        Schema::dropIfExists('bundle_courses');
        Schema::dropIfExists('bundles');
    }
};
