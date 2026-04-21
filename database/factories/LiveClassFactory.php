<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Course\Course;
use App\Models\LiveClass\LiveClass;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LiveClass>
 */
class LiveClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'section_id' => null,
            'title' => fake()->randomElement([
                'Live Q&A Session',
                'Code Review Workshop',
                'Project Demo',
                'Office Hours',
                'Guest Lecture',
                'Group Coding Session',
            ]) . ' ' . fake()->numberBetween(1, 20),
            'description' => fake()->optional()->paragraph(2),
            'meeting_provider' => fake()->randomElement(['zoom', 'meet', 'jitsi']),
            'meeting_id' => Str::random(20),
            'meeting_password' => Str::random(8),
            'join_url' => 'https://meet.example.com/' . Str::random(12),
            'host_url' => 'https://meet.example.com/host/' . Str::random(12),
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 month'),
            'duration' => fake()->randomElement([30, 45, 60, 90, 120]),
            'buffer_time' => 10,
            'max_participants' => fake()->randomElement([25, 50, 100, 200]),
            'enrolled_count' => 0,
            'attended_count' => 0,
            'price' => fake()->randomFloat(2, 0, 50),
            'is_free' => fake()->boolean(30),
            'is_recurring' => false,
            'recurrence_pattern' => null,
            'is_automatic_recording' => true,
            'allow_chat' => true,
            'allow_screen_share' => true,
            'allow_recording' => true,
            'is_published' => true,
            'is_started' => false,
            'is_ended' => false,
            'is_cancelled' => false,
            'cancellation_reason' => null,
            'recording_url' => null,
            'recording_duration' => null,
            'metadata' => null,
        ];
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_free' => true,
            'price' => 0,
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_at' => now()->addDays(rand(1, 14)),
            'is_published' => true,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_started' => true,
            'is_ended' => true,
            'recording_url' => 'https://example.com/recordings/' . Str::random(12),
            'recording_duration' => fake()->numberBetween(30, 120),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_cancelled' => true,
            'cancellation_reason' => fake()->sentence(5),
        ]);
    }
}