<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Course\Course;
use App\Models\Payment\PaymentHistory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PaymentHistory>
 */
class PaymentHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'session_id' => 'sess_' . Str::random(32),
            'transaction_id' => 'txn_' . Str::random(17),
            'invoice' => 'INV-' . Str::upper(Str::random(8)),
            'invoice_url' => 'https://example.com/invoices/' . Str::random(8),
            'currency' => 'USD',
            'currency_symbol' => '$',
            'subtotal' => fake()->randomFloat(2, 10, 200),
            'tax' => 0,
            'tax_percentage' => 0,
            'coupon_id' => null,
            'coupon_discount' => 0,
            'total_amount' => fake()->randomFloat(2, 10, 200),
            'amount' => fake()->randomFloat(2, 10, 200),
            'payment_method' => fake()->randomElement(['stripe', 'paypal', 'razorpay', 'paymob']),
            'payment_type' => fake()->randomElement(['card', 'wallet', 'bank']),
            'payment_status' => fake()->randomElement(['completed', 'pending', 'failed']),
            'payment_details' => [
                'card_brand' => fake()->randomElement(['visa', 'mastercard', 'amex']),
                'last4' => fake()->numerify('####'),
            ],
            'admin_commission' => fake()->randomFloat(2, 3, 60),
            'instructor_commission' => fake()->randomFloat(2, 7, 140),
            'status' => fake()->randomElement(['completed', 'pending', 'failed', 'refunded']),
            'notes' => null,
            'metadata' => null,
            'failed_at' => null,
            'refunded_at' => null,
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'completed',
            'status' => 'completed',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'failed',
            'status' => 'failed',
            'failed_at' => now(),
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'refunded_at' => now(),
        ]);
    }
}