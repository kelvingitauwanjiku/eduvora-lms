<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ChatMessage $message,
        public int $channelId
    ) {
        $this->message->load(['user:id,name,avatar']);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.channel.' . $this->channelId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'user' => [
                'id' => $this->message->user->id,
                'name' => $this->message->user->name,
                'avatar' => $this->message->user->avatar,
            ],
            'channel_id' => $this->channelId,
            'timestamp' => $this->message->created_at->toIso8601String(),
            'type' => $this->message->type,
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}