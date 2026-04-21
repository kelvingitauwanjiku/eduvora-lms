<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_histories', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->decimal('coupon_discount', 12, 2)->nullable()->after('coupon_id');
            $table->decimal('admin_commission', 12, 2)->nullable()->after('coupon_discount');
            $table->decimal('instructor_commission', 12, 2)->nullable()->after('admin_commission');
            $table->string('payment_method', 50)->nullable()->after('instructor_commission');
            $table->enum('payout_status', ['unpaid', 'pending', 'paid'])->default('unpaid')->after('payment_method');
            $table->timestamp('completed_at')->nullable()->after('payout_status');
            $table->timestamp('refunded_at')->nullable()->after('completed_at');
            $table->text('failure_reason')->nullable()->after('refunded_at');
            $table->json('metadata')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payment_histories', function (Blueprint $table) {
            $table->dropColumn([
                'coupon_id', 'coupon_discount', 'admin_commission', 'instructor_commission',
                'payment_method', 'payout_status', 'completed_at', 'refunded_at', 
                'failure_reason', 'metadata'
            ]);
        });
    }
};