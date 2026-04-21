<?php

namespace Database\Factories;

use App\Models\ChatChannel;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'chat_channel_id' => ChatChannel::factory(),
            'user_id' => User::factory(),
            'parent_id' => null,
            'thread_id' => null,
            'reply_to_id' => null,
            'content' => fake()->randomElement([
                'Hey everyone! 👋',
                'Welcome to the channel!',
                'Great work on the project!',
                'Has anyone tried the new feature?',
                'Just finished the tutorial. Really helpful!',
                'Thanks for sharing!',
                'I have a question...',
                'Check out this resource:',
                'Interesting approach!',
                'Can someone help me with this?',
            ]),
            'type' => 'text',
            'attachments' => null,
            'embeds' => null,
            'mentions' => null,
            'reactions' => null,
            'reactions_count' => 0,
            'threads_count' => 0,
            'replies_count' => 0,
            'is_pinned' => false,
            'is_slowmode' => false,
            'is_edited' => false,
            'is_deleted' => false,
            'is_flagged' => false,
        ];
    }

    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinned' => true,
        ]);
    }

    public function flagged(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_flagged' => true,
        ]);
    }

    public function withAttachment(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'file',
            'attachments' => [
                [
                    'name' => 'document.pdf',
                    'url' => 'https://example.com/files/document.pdf',
                    'size' => 1024 * 50,
                    'type' => 'application/pdf',
                ]
            ],
        ]);
    }

    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'image',
            'attachments' => [
                [
                    'url' => 'https://picsum.photos/seed/' . fake()->uuid() . '/800/600',
                    'width' => 800,
                    'height' => 600,
                ]
            ],
        ]);
    }
}