<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('quiz_submissions', function (Blueprint $table) {
            $table->foreignId('enrollment_id')->nullable()->references('id')->on('enrollments')->nullOnDelete();
        });

        Schema::table('submitted_assignments', function (Blueprint $table) {
            $table->foreignId('enrollment_id')->nullable()->references('id')->on('enrollments')->nullOnDelete();
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->references('id')->on('coupons')->nullOnDelete();
            $table->foreignId('coupon_usage_id')->nullable()->references('id')->on('coupon_usages')->cascadeOnDelete();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->references('id')->on('courses')->cascadeOnDelete();
            $table->foreignId('ebook_id')->nullable()->references('id')->on('ebooks')->cascadeOnDelete();
            $table->foreignId('bootcamp_id')->nullable()->references('id')->on('bootcamps')->cascadeOnDelete();
            $table->foreignId('bundle_id')->nullable()->references('id')->on('course_bundles')->cascadeOnDelete();
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->references('id')->on('courses')->cascadeOnDelete();
            $table->foreignId('ebook_id')->nullable()->references('id')->on('ebooks')->cascadeOnDelete();
            $table->foreignId('bootcamp_id')->nullable()->references('id')->on('bootcamps')->cascadeOnDelete();
        });

        Schema::table('payment_histories', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->references('id')->on('courses')->cascadeOnDelete();
            $table->foreignId('ebook_id')->nullable()->references('id')->on('ebooks')->cascadeOnDelete();
            $table->foreignId('bootcamp_id')->nullable()->references('id')->on('bootcamps')->cascadeOnDelete();
            $table->foreignId('bundle_id')->nullable()->references('id')->on('course_bundles')->cascadeOnDelete();
            $table->foreignId('coupon_id')->nullable()->references('id')->on('coupons')->cascadeOnDelete();
            $table->foreignId('gateway_id')->nullable()->references('id')->on('payment_gateways')->cascadeOnDelete();
        });

        Schema::table('offline_payments', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->references('id')->on('courses')->cascadeOnDelete();
            $table->foreignId('ebook_id')->nullable()->references('id')->on('ebooks')->cascadeOnDelete();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->references('id')->on('payment_histories')->cascadeOnDelete();
        });

        Schema::table('live_class_participants', function (Blueprint $table) {
            $table->foreignId('enrollment_id')->nullable()->references('id')->on('enrollments')->cascadeOnDelete();
        });

        Schema::table('bootcamp_live_classes', function (Blueprint $table) {
            $table->foreignId('module_id')->nullable()->references('id')->on('bootcamp_modules')->cascadeOnDelete();
        });

        Schema::table('bootcamp_resources', function (Blueprint $table) {
            $table->foreignId('module_id')->nullable()->references('id')->on('bootcamp_modules')->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->references('id')->on('lessons')->cascadeOnDelete();
        });

        Schema::table('tutor_can_teach', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->references('id')->on('tutor_subjects')->cascadeOnDelete();
        });
    }

    public function down(): void {}
};
