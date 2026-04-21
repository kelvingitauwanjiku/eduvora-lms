<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->longText('features')->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->decimal('monthly_price', 12, 2)->default(0);
            $table->decimal('yearly_price', 12, 2)->default(0);
            $table->decimal('lifetime_price', 12, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->integer('courses_limit')->nullable()->comment('null = unlimited');
            $table->integer('ebooks_limit')->nullable()->comment('null = unlimited');
            $table->integer('bootcamps_limit')->nullable()->comment('null = unlimited');
            $table->integer('storage_limit')->nullable()->comment('In MB, null = unlimited');
            $table->integer('tutors_limit')->nullable()->comment('null = unlimited');
            $table->boolean('allow_download')->default(true);
            $table->boolean('allow_certificate')->default(true);
            $table->boolean('allow_live_classes')->default(true);
            $table->boolean('allow_consultation')->default(true);
            $table->boolean('allow_affiliate')->default(false);
            $table->boolean('allow_custom_domain')->default(false);
            $table->boolean('allow_priority_support')->default(false);
            $table->boolean('allow_api_access')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_free')->default(false);
            $table->boolean('show_on_pricing')->default(true);
            $table->integer('subscribers_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('is_featured');
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('subscription_number', 100)->unique();
            $table->enum('billing_cycle', ['monthly', 'yearly', 'lifetime'])->default('monthly');
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('setup_fee', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('coupon_discount', 12, 2)->default(0);
            $table->string('coupon_code', 100)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->enum('status', ['active', 'cancelled', 'expired', 'pending', 'paused', 'trial'])->default('pending');
            $table->boolean('is_trial')->default(false);
            $table->integer('trial_days')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancels_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->string('payment_method', 100)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->string('gateway_subscription_id', 255)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'plan_id']);
            $table->index('status');
        });

        Schema::create('subscription_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->integer('value')->nullable();
            $table->string('unit', 50)->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('plan_id');
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name', 255)->nullable();
            $table->string('email', 255);
            $table->string('phone', 50)->nullable();
            $table->string('address', 500)->nullable();
            $table->text('message');
            $table->string('resume', 500)->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->string('portfolio_url', 500)->nullable();
            $table->json('attachments')->nullable();
            $table->string('type', 100)->default('instructor')->comment('instructor, partnership, franchise, bulk_license');
            $table->enum('status', ['pending', 'reviewed', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('status');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('subscription_features');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
