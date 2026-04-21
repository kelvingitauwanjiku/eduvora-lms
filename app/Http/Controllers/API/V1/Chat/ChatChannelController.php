<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Http\Controllers\Controller;
use App\Models\ChatChannel;
use App\Models\ChatChannelMember;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatChannelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $channels = ChatChannel::active()
            ->public()
            ->orderBy('position')
            ->withCount(['members', 'messages'])
            ->get();

        return response()->json($channels);
    }

    public function show(int $id): JsonResponse
    {
        $channel = ChatChannel::with(['members.user'])
            ->findOrFail($id);

        return response()->json($channel);
    }

    public function messages(Request $request, int $id): JsonResponse
    {
        $messages = ChatMessage::where('chat_channel_id', $id)
            ->parentMessages()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 50));

        return response()->json($messages);
    }

    public function sendMessage(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $channel = ChatChannel::findOrFail($id);
        $user = $request->user();

        $message = ChatMessage::create([
            'chat_channel_id' => $channel->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'type' => 'text',
        ]);

        $channel->increment('messages_count');
        $channel->update(['last_message_at' => now()]);

        return response()->json($message->load('user'), 201);
    }

    public function join(Request $request, int $id): JsonResponse
    {
        $channel = ChatChannel::findOrFail($id);
        $user = $request->user();

        $existingMember = ChatChannelMember::where('chat_channel_id', $channel->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingMember) {
            return response()->json(['message' => 'Already a member'], 400);
        }

        $member = ChatChannelMember::create([
            'chat_channel_id' => $channel->id,
            'user_id' => $user->id,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $channel->increment('members_count');

        return response()->json($member, 201);
    }

    public function leave(Request $request, int $id): JsonResponse
    {
        $channel = ChatChannel::findOrFail($id);
        $user = $request->user();

        $member = ChatChannelMember::where('chat_channel_id', $channel->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$member) {
            return response()->json(['message' => 'Not a member'], 400);
        }

        $member->delete();
        $channel->decrement('members_count');

        return response()->json(['message' => 'Left channel successfully']);
    }

    public function members(int $id): JsonResponse
    {
        $members = ChatChannelMember::where('chat_channel_id', $id)
            ->with(['user'])
            ->get();

        return response()->json($members);
    }

    public function addReaction(Request $request, int $messageId): JsonResponse
    {
        $request->validate([
            'reaction' => 'required|string|max:10',
        ]);

        $message = ChatMessage::findOrFail($messageId);
        $user = $request->user();

        $reactions = $message->reactions ?? [];
        $userId = (string) $user->id;

        if (!isset($reactions[$request->reaction])) {
            $reactions[$request->reaction] = [];
        }

        if (!in_array($userId, $reactions[$request->reaction])) {
            $reactions[$request->reaction][] = $userId;
            $message->increment('reactions_count');
        }

        $message->update(['reactions' => $reactions]);

        return response()->json($message);
    }
}