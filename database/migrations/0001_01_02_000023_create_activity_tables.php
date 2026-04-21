<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('causer_type', 100)->nullable();
            $table->bigInteger('causer_id')->nullable();
            $table->string('log_name', 100)->nullable();
            $table->string('description', 500);
            $table->string('subject_type', 100)->nullable();
            $table->bigInteger('subject_id')->nullable();
            $table->json('properties')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('location', 255)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('subject_type');
            $table->index('log_name');
            $table->index('created_at');
        });

        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('payout_number', 100)->unique();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('processing_fee', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->enum('payment_method', ['bank_transfer', 'paypal', 'stripe', 'payoneer', 'wire'])->default('bank_transfer');
            $table->string('bank_name', 255)->nullable();
            $table->string('account_name', 255)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('routing_number', 100)->nullable();
            $table->string('swift_code', 50)->nullable();
            $table->string('iban', 100)->nullable();
            $table->string('paypal_email', 255)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'rejected'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->json('earnings_breakdown')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('status');
        });

        Schema::create('instructor_earnings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('bootcamp_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('ebook_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('order_number', 100)->nullable();
            $table->decimal('gross_amount', 12, 2)->default(0);
            $table->decimal('admin_commission', 12, 2)->default(0);
            $table->decimal('instructor_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('refunded_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->decimal('pending_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->enum('status', ['pending', 'available', 'payout_processing', 'payout_completed', 'refunded'])->default('pending');
            $table->timestamp('available_at')->nullable();
            $table->timestamp('payout_at')->nullable();
            $table->foreignId('payout_id')->nullable()->constrained()->cascadeOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('course_id');
        });

        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('referral_code', 50)->unique();
            $table->decimal('commission_rate', 5, 2)->default(10)->comment('Percentage');
            $table->decimal('minimum_payout', 12, 2)->default(50);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->decimal('pending_earnings', 12, 2)->default(0);
            $table->decimal('paid_earnings', 12, 2)->default(0);
            $table->integer('total_referrals')->default(0);
            $table->integer('active_referrals')->default(0);
            $table->integer('total_conversions')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->decimal('average_order_value', 12, 2)->default(0);
            $table->enum('status', ['pending', 'active', 'suspended', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('user_id');
            $table->index('referral_code');
        });

        Schema::create('affiliate_referrals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('affiliate_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('referral_code', 50)->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('source', 100)->nullable();
            $table->string('medium', 100)->nullable();
            $table->string('campaign', 100)->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->boolean('has_purchased')->default(false);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->integer('purchases_count')->default(0);
            $table->timestamps();

            $table->unique(['affiliate_id', 'referred_user_id']);
        });

        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('affiliate_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('order_number', 100)->nullable();
            $table->decimal('order_amount', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(10);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('pending_amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled', 'refunded'])->default('pending');
            $table->foreignId('payout_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('affiliate_id');
            $table->index('order_id');
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type', 100);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->json('data')->nullable();
            $table->string('file_url', 500)->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('affiliate_commissions');
        Schema::dropIfExists('affiliate_referrals');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('instructor_earnings');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('activity_logs');
    }
};
