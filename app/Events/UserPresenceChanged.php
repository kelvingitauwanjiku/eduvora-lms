<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserPresenceChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
        public string $status
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('presence'),
        new Channel('users.' . $this->user->id),
        new Channel('chat.members'),
        new Channel('course.' . $this->user->id),
        new Channel('instructors'),
        new Channel('online'),
        new Channel('notifications.' . $this->user->id),
        new Channel('my-learning.' . $this->user->id),
        new Channel('chat.channel.*'),
        new Channel('chat.direct.' . $this->user->id),
        new Channel('notifications.' . $this->user->id),
    ];
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'status' => $this->status,
            'last_seen' => now()->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'presence.changed';
    }
}