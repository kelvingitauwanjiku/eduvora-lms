<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Course\Course;
use App\Models\PaymentHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function processPayment(array $data): PaymentHistory
    {
        return DB::transaction(function () use ($data) {
            $course = Course::findOrFail($data['course_id']);
            $user = User::findOrFail($data['user_id']);

            $price = $course->discount_price ?? $course->price;
            $couponDiscount = 0;
            $coupon = null;

            if (! empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', $data['coupon_code'])
                    ->where('is_active', true)
                    ->first();

                if ($coupon && $coupon->isValid()) {
                    $couponDiscount = $coupon->discount_type === 'percentage'
                        ? ($price * $coupon->discount_value / 100)
                        : $coupon->discount_value;
                }
            }

            $taxAmount = $price * (config('settings.tax_percentage', 0) / 100);
            $totalAmount = $price - $couponDiscount + $taxAmount;

            $adminCommission = ($price - $couponDiscount) * ($course->admin_commission_rate / 100);
            $instructorCommission = ($price - $couponDiscount) - $adminCommission;

            $payment = PaymentHistory::create([
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'course_id' => $course->id,
                'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                'session_id' => 'sess_'.Str::random(32),
                'transaction_id' => 'txn_'.Str::random(17),
                'invoice_number' => 'INV-'.strtoupper(Str::random(8)),
                'currency' => 'USD',
                'subtotal' => $price,
                'tax_amount' => $taxAmount,
                'coupon_id' => $coupon?->id,
                'coupon_discount' => $couponDiscount,
                'total_amount' => $totalAmount,
                'amount' => $totalAmount,
                'payment_method' => $data['payment_method'] ?? 'stripe',
                'status' => 'completed',
                'payout_status' => 'unpaid',
                'admin_commission' => $adminCommission,
                'instructor_commission' => $instructorCommission,
            ]);

            if ($coupon) {
                $coupon->increment('uses_count');
            }

            $course->increment('students_count');
            $course->user->increment('total_earnings', $instructorCommission);

            return $payment;
        });
    }

    public function processRefund(int $paymentId): PaymentHistory
    {
        return DB::transaction(function () use ($paymentId) {
            $payment = PaymentHistory::findOrFail($paymentId);

            if ($payment->status === 'refunded') {
                throw new \Exception('Payment already refunded');
            }

            $payment->update([
                'status' => 'refunded',
                'refunded_at' => now(),
            ]);

            $payment->course->decrement('students_count');
            $payment->course->user->decrement('total_earnings', $payment->instructor_commission);

            return $payment;
        });
    }

    public function getUserTransactions(int $userId, array $filters = [])
    {
        $query = PaymentHistory::where('user_id', $userId)
            ->with(['course']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function validateCoupon(string $code, int $courseId): ?Coupon
    {
        $coupon = Coupon::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (! $coupon || ! $coupon->isValid()) {
            return null;
        }

        if ($coupon->course_id && $coupon->course_id !== $courseId) {
            return null;
        }

        return $coupon;
    }

    public function calculateOrderTotal(int $courseId, ?string $couponCode = null): array
    {
        $course = Course::findOrFail($courseId);
        $price = $course->discount_price ?? $course->price;
        $couponDiscount = 0;
        $coupon = null;

        if ($couponCode) {
            $coupon = $this->validateCoupon($couponCode, $courseId);
            if ($coupon) {
                $couponDiscount = $coupon->discount_type === 'percentage'
                    ? ($price * $coupon->discount_value / 100)
                    : $coupon->discount_value;
            }
        }

        $taxAmount = $price * (config('settings.tax_percentage', 0) / 100);
        $totalAmount = $price - $couponDiscount + $taxAmount;

        return [
            'subtotal' => $price,
            'coupon_discount' => $couponDiscount,
            'tax' => $taxAmount,
            'total' => $totalAmount,
            'currency' => 'USD',
            'coupon' => $coupon,
        ];
    }
}
