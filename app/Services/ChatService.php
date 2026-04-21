<?php

namespace App\Services;

use App\Models\ChatChannel;
use App\Models\ChatChannelMember;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Str;

class ChatService
{
    public function getChannels(array $filters = [])
    {
        $query = ChatChannel::active()
            ->public()
            ->withCount(['members', 'messages']);

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('position')->get();
    }

    public function getChannel(int $id): ChatChannel
    {
        return ChatChannel::with(['members.user'])
            ->findOrFail($id);
    }

    public function getMessages(int $channelId, array $filters = [])
    {
        return ChatMessage::where('chat_channel_id', $channelId)
            ->parentMessages()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 50);
    }

    public function sendMessage(int $channelId, int $userId, string $content): ChatMessage
    {
        $channel = ChatChannel::findOrFail($channelId);
        
        $member = ChatChannelMember::where('chat_channel_id', $channelId)
            ->where('user_id', $userId)
            ->first();

        if (!$member || !$member->can_post) {
            throw new \Exception('You cannot post in this channel');
        }

        $message = ChatMessage::create([
            'chat_channel_id' => $channel->id,
            'user_id' => $userId,
            'content' => $content,
            'type' => 'text',
        ]);

        $channel->increment('messages_count');
        $channel->update(['last_message_at' => now()]);

        return $message->load('user');
    }

    public function joinChannel(int $channelId, int $userId): ChatChannelMember
    {
        $channel = ChatChannel::findOrFail($channelId);

        $existingMember = ChatChannelMember::where('chat_channel_id', $channelId)
            ->where('user_id', $userId)
            ->first();

        if ($existingMember) {
            throw new \Exception('Already a member of this channel');
        }

        $member = ChatChannelMember::create([
            'chat_channel_id' => $channel->id,
            'user_id' => $userId,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $channel->increment('members_count');

        // System message
        ChatMessage::create([
            'chat_channel_id' => $channel->id,
            'user_id' => $userId,
            'content' => 'joined the channel',
            'type' => 'system',
        ]);

        return $member;
    }

    public function leaveChannel(int $channelId, int $userId): void
    {
        $channel = ChatChannel::findOrFail($channelId);

        $member = ChatChannelMember::where('chat_channel_id', $channelId)
            ->where('user_id', $userId)
            ->first();

        if (!$member) {
            throw new \Exception('Not a member of this channel');
        }

        $member->delete();
        $channel->decrement('members_count');

        // System message
        ChatMessage::create([
            'chat_channel_id' => $channel->id,
            'user_id' => $userId,
            'content' => 'left the channel',
            'type' => 'system',
        ]);
    }

    public function addReaction(int $messageId, int $userId, string $reaction): ChatMessage
    {
        $message = ChatMessage::findOrFail($messageId);
        
        $reactions = $message->reactions ?? [];
        $userIdStr = (string) $userId;

        if (!isset($reactions[$reaction])) {
            $reactions[$reaction] = [];
        }

        if (!in_array($userIdStr, $reactions[$reaction])) {
            $reactions[$reaction][] = $userIdStr;
            $message->increment('reactions_count');
        }

        $message->update(['reactions' => $reactions]);

        return $message;
    }

    public function removeReaction(int $messageId, int $userId, string $reaction): ChatMessage
    {
        $message = ChatMessage::findOrFail($messageId);
        
        $reactions = $message->reactions ?? [];
        $userIdStr = (string) $userId;

        if (isset($reactions[$reaction])) {
            $reactions[$reaction] = array_filter($reactions[$reaction], fn($id) => $id !== $userIdStr);
            if (empty($reactions[$reaction])) {
                unset($reactions[$reaction]);
            }
            $message->decrement('reactions_count');
        }

        $message->update(['reactions' => $reactions]);

        return $message;
    }

    public function getChannelMembers(int $channelId)
    {
        return ChatChannelMember::where('chat_channel_id', $channelId)
            ->with(['user'])
            ->get();
    }

    public function getUserChannels(int $userId)
    {
        return ChatChannelMember::where('user_id', $userId)
            ->with(['chatChannel'])
            ->get()
            ->pluck('chatChannel');
    }
}