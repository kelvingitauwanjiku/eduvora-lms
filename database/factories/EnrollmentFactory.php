<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
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
            'price' => fake()->randomFloat(2, 0, 200),
            'tax' => 0,
            'coupon_id' => null,
            'coupon_discount' => 0,
            'total_amount' => 0,
            'currency' => 'USD',
            'payment_method' => fake()->randomElement(['stripe', 'paypal', 'razorpay']),
            'payment_type' => fake()->randomElement(['one_time', 'subscription']),
            'payment_status' => 'completed',
            'admin_commission' => 0,
            'instructor_commission' => 0,
            'status' => 'active',
            'progress' => 0,
            'completion_percentage' => 0,
            'completed_lessons' => 0,
            'total_lessons' => 0,
            'started_at' => now(),
            'completed_at' => null,
            'expires_at' => null,
            'last_accessed_at' => now(),
            'enrollment_type' => fake()->randomElement(['free', 'paid', 'subscription', 'coupon']),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'progress' => 100,
            'completion_percentage' => 100,
            'completed_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expires_at' => fake()->dateTimeBetween('-1 month', '-1 day'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);
    }
}