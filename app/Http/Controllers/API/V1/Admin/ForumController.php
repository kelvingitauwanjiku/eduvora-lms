<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $query = Forum::with('user');

        if ($request->has('is_published')) {
            $query->where('is_published', $request->boolean('is_published'));
        }

        $forums = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $forums,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:forums,slug',
            'description' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();

        $forum = Forum::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Forum created successfully',
            'data' => $forum,
        ], 201);
    }

    public function show(int $id)
    {
        $forum = Forum::with('user', 'replies.user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $forum,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $forum = Forum::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|unique:forums,slug,'.$id,
            'description' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $forum->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Forum updated successfully',
            'data' => $forum,
        ]);
    }

    public function destroy(int $id)
    {
        $forum = Forum::findOrFail($id);
        $forum->delete();

        return response()->json([
            'success' => true,
            'message' => 'Forum deleted successfully',
        ]);
    }

    public function replies(int $forumId)
    {
        $replies = ForumReply::with('user')
            ->where('forum_id', $forumId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $replies,
        ]);
    }

    public function deleteReply(int $id)
    {
        $reply = ForumReply::findOrFail($id);
        $reply->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reply deleted successfully',
        ]);
    }
}
