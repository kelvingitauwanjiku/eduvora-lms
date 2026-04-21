<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ChatChannel;
use App\Models\ChatMessage;
use Illuminate\Database\Seeder;

class ChatChannelSeeder extends Seeder
{
    public function run(): void
    {
        $channels = [
            ['name' => 'General', 'description' => 'General discussion and chat', 'type' => 'text'],
            ['name' => 'Introductions', 'description' => 'New to the platform? Introduce yourself here!', 'type' => 'text'],
            ['name' => 'Announcements', 'description' => 'Official announcements and updates', 'type' => 'announcements'],
            ['name' => 'Help & Support', 'description' => 'Get help from the community', 'type' => 'text'],
            ['name' => 'Showcase', 'description' => 'Show off your projects', 'type' => 'text'],
            ['name' => 'JavaScript', 'description' => 'Discuss JavaScript and frameworks', 'type' => 'text'],
            ['name' => 'Python', 'description' => 'Python programming discussions', 'type' => 'text'],
            ['name' => 'Jobs & Careers', 'description' => 'Job opportunities and career advice', 'type' => 'text'],
        ];

        $instructors = User::where('is_instructor', true)->get();

        foreach ($channels as $index => $channel) {
            $instructor = $instructors->random();

            $chatChannel = ChatChannel::create([
                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                'name' => $channel['name'],
                'slug' => \Illuminate\Support\Str::slug($channel['name']),
                'description' => $channel['description'],
                'type' => $channel['type'],
                'icon' => 'fa-comment',
                'user_id' => $instructor->id,
                'position' => $index + 1,
                'is_private' => $channel['type'] === 'announcements',
                'members_count' => rand(10, 100),
                'messages_count' => rand(50, 500),
                'is_archived' => false,
            ]);

            for ($i = 0; $i < rand(5, 20); $i++) {
                $user = User::where('is_instructor', true)->orWhere('id', '>', 0)->inRandomOrder()->first();
                ChatMessage::create([
                    'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                    'chat_channel_id' => $chatChannel->id,
                    'user_id' => $user->id,
                    'content' => [
                        'Hey everyone! 👋',
                        'Welcome to the channel!',
                        'Great discussion here.',
                        'Has anyone tried the new feature?',
                        'Thanks for sharing!',
                        'Check out this resource.',
                    ][rand(0, 5)],
                    'type' => 'text',
                ]);
            }
        }
    }
}