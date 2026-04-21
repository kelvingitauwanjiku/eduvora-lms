<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Tutor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tutor>
 */
class TutorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'user_id' => User::factory(),
            'title' => fake()->randomElement([
                'Senior Developer',
                'Tech Lead',
                'Software Architect',
                'Full Stack Engineer',
                'Data Scientist',
                'UX Designer',
            ]),
            'tagline' => fake()->optional()->sentence(8),
            'description' => fake()->optional()->paragraph(3),
            'profile_image' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . fake()->uuid(),
            'video_intro' => 'https://www.youtube.com/watch?v=' . Str::random(11),
            'teaching_experience' => [
                fake()->randomElement(['5+ years teaching', '10+ years industry experience', 'Bootcamp instructor']),
            ],
            'teaching_methodology' => [
                fake()->randomElement(['Project-based', 'Theory + Practice', 'Step-by-step']),
            ],
            'certifications' => [
                fake()->randomElement(['AWS Certified', 'Google Developer', 'Meta Certified']),
            ],
            'hourly_rate' => fake()->randomFloat(2, 20, 150),
            'rating' => fake()->randomFloat(2, 4.0, 5.0),
            'reviews_count' => 0,
            'sessions_count' => 0,
            'students_count' => 0,
            'total_hours_taught' => 0,
            'languages' => ['English'],
            'timezone' => 'UTC',
            'teaching_mode' => fake()->randomElement(['online', 'both']),
            'available_days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            'is_verified' => fake()->boolean(50),
            'is_featured' => fake()->boolean(20),
            'is_available' => true,
            'is_active' => true,
            'status' => 'approved',
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}