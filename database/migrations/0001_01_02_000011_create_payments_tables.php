<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('logo', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('model_name', 100)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->json('keys')->nullable();
            $table->json('settings')->nullable();
            $table->decimal('transaction_fee_percentage', 5, 2)->default(0);
            $table->decimal('fixed_fee', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->boolean('test_mode')->default(false);
            $table->boolean('is_addon')->default(false);
            $table->string('webhook_url', 500)->nullable();
            $table->string('redirect_url', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('ebook_id')->nullable();
            $table->unsignedBigInteger('bootcamp_id')->nullable();
            $table->unsignedBigInteger('bundle_id')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->unsignedBigInteger('gateway_id')->nullable();
            $table->string('order_number', 100)->unique();
            $table->enum('payment_type', ['course', 'ebook', 'bootcamp', 'bundle', 'subscription', 'consultation', 'service'])->default('course');
            $table->enum('payment_method', ['stripe', 'paypal', 'razorpay', 'paystack', 'offline', 'wallet', 'bank_transfer', 'other'])->default('stripe');
            $table->string('transaction_id', 255)->nullable();
            $table->string('session_id', 255)->nullable();
            $table->string('gateway_response', 500)->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('coupon_discount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->decimal('exchange_rate', 12, 6)->default(1);
            $table->decimal('admin_revenue', 12, 2)->default(0);
            $table->decimal('instructor_revenue', 12, 2)->default(0);
            $table->decimal('affiliate_revenue', 12, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded', 'disputed', 'partially_refunded'])->default('pending');
            $table->timestamp('payment_date')->nullable();
            $table->string('invoice_number', 100)->nullable();
            $table->string('invoice_url', 500)->nullable();
            $table->string('receipt_url', 500)->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('order_number');
            $table->index('transaction_id');
            $table->index('status');
            $table->index('payment_date');
        });

        Schema::create('offline_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('ebook_id')->nullable();
            $table->enum('item_type', ['course', 'ebook', 'bootcamp', 'bundle'])->default('course');
            $table->string('items', 500)->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('coupon_code', 100)->nullable();
            $table->decimal('coupon_discount', 12, 2)->default(0);
            $table->string('payment_method', 100)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('transaction_number', 255)->nullable();
            $table->string('phone_number', 50)->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->string('document', 500)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'verified', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('user_id');
            $table->index('status');
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('payment_id')->constrained('payment_histories')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('refund_number', 100)->unique();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('admin_fee', 12, 2)->default(0);
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->enum('reason', ['course_not_as_described', 'technical_issues', 'change_mind', 'duplicate_purchase', 'fraud', 'other'])->nullable();
            $table->text('user_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing', 'completed', 'failed'])->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('payment_id');
            $table->index('status');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->string('invoice_number', 100)->unique();
            $table->string('invoice_prefix', 20)->default('INV');
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->string('billing_name', 255)->nullable();
            $table->string('billing_email', 255)->nullable();
            $table->string('billing_phone', 50)->nullable();
            $table->string('billing_address', 500)->nullable();
            $table->string('billing_city', 100)->nullable();
            $table->string('billing_state', 100)->nullable();
            $table->string('billing_country', 100)->nullable();
            $table->string('billing_postal_code', 20)->nullable();
            $table->string('billing_company', 255)->nullable();
            $table->string('billing_vat_number', 50)->nullable();
            $table->json('line_items')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->enum('status', ['draft', 'sent', 'paid', 'partial', 'cancelled', 'refunded'])->default('draft');
            $table->string('notes', 1000)->nullable();
            $table->string('pdf_url', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('offline_payments');
        Schema::dropIfExists('payment_histories');
        Schema::dropIfExists('payment_gateways');
    }
};
