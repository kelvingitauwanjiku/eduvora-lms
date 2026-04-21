<?php

namespace Database\Factories;

use App\Models\ChatChannel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ChatChannel>
 */
class ChatChannelFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement([
            'General',
            'Introductions',
            'Announcements',
            'Help & Support',
            'Showcase',
            'Resources',
            'Off-Topic',
            'Jobs & Careers',
            'Networking',
        ]);

        return [
            'uuid' => Str::uuid()->toString(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::lower(Str::random(6)),
            'description' => fake()->optional()->sentence(10),
            'type' => 'text',
            'icon' => fake()->randomElement([
                'fa-comment',
                'fa-hashtag',
                'fa-bullhorn',
                'fa-life-ring',
                'fa-briefcase',
                'fa-graduation-cap',
            ]),
            'workspace_id' => null,
            'user_id' => User::factory(),
            'topic' => fake()->optional()->sentence(5),
            'position' => fake()->numberBetween(0, 50),
            'is_private' => false,
            'is_read_only' => false,
            'allow_massages' => true,
            'allow_threads' => true,
            'allow_files' => true,
            'allow_links' => true,
            'allow_images' => true,
            'allow_emoji' => true,
            'allow_mentions' => true,
            'is_archived' => false,
            'members_count' => 0,
            'messages_count' => 0,
            'threads_count' => 0,
            'files_count' => 0,
        ];
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => true,
        ]);
    }

    public function voice(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'voice',
        ]);
    }

    public function readOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read_only' => true,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_archived' => true,
        ]);
    }
}