<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Course\Course;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'title' => fake()->optional()->sentence(6),
            'review' => fake()->randomElement([
                'Great course! Learned a lot.',
                'Very informative and well-structured.',
                'Would recommend to others.',
                'Excellent content and explanations.',
                'Good practical examples.',
                'Helped me land a job!',
                'Comprehensive and easy to follow.',
                'Best course I have taken.',
            ]),
            'rating' => fake()->numberBetween(3, 5),
            'rating_quality' => fake()->numberBetween(3, 5),
            'rating_instructor' => fake()->numberBetween(3, 5),
            'rating_value' => fake()->numberBetween(3, 5),
            'is_featured' => fake()->boolean(20),
            'is_published' => true,
            'status' => 'approved',
            'helpful_count' => 0,
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function fiveStar(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => 5,
            'rating_quality' => 5,
            'rating_instructor' => 5,
            'rating_value' => 5,
            'review' => fake()->randomElement([
                'Excellent! Best investment.',
                'Highly recommend!',
                'Amazing course content.',
                'Exceeded my expectations!',
            ]),
        ]);
    }

    public function oneStar(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => 1,
            'review' => fake()->randomElement([
                'Not what I expected.',
                'Could be better.',
                'Not recommended.',
            ]),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'status' => 'pending',
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'is_published' => true,
            'status' => 'approved',
        ]);
    }
}