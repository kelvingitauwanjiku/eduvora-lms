<?php

namespace App\Http\Controllers\Api\V1\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(): JsonResponse
    {
        $blogs = Blog::published()
            ->with(['user'])
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return response()->json($blogs);
    }

    public function featured(): JsonResponse
    {
        $blogs = Blog::published()
            ->where('is_featured', true)
            ->limit(6)
            ->get();

        return response()->json($blogs);
    }

    public function show(int $id): JsonResponse
    {
        $blog = Blog::with(['user', 'comments'])->findOrFail($id);
        $blog->increment('views_count');

        return response()->json($blog);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $blog = Blog::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'slug' => \Illuminate\Support\Str::slug($request->title),
            'content' => $request->content,
            'status' => 'draft',
        ]);

        return response()->json($blog, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $blog = Blog::where('user_id', $request->user()->id)->findOrFail($id);
        
        $blog->update($request->only(['title', 'content']));

        return response()->json($blog);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $blog = Blog::where('user_id', $request->user()->id)->findOrFail($id);
        $blog->delete();

        return response()->json(['message' => 'Blog deleted']);
    }

    public function like(Request $request, int $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);
        $likes = $blog->likes ?? [];
        $userId = (string) $request->user()->id;

        if (!in_array($userId, $likes)) {
            $likes[] = $userId;
            $blog->update(['likes' => $likes, 'likes_count' => count($likes)]);
        }

        return response()->json($blog);
    }

    public function comment(Request $request, int $id): JsonResponse
    {
        $request->validate(['comment' => 'required|string']);

        $blog = Blog::findOrFail($id);

        $comment = \App\Models\Blog\BlogComment::create([
            'blog_id' => $blog->id,
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
        ]);

        $blog->increment('comments_count');

        return response()->json($comment, 201);
    }
}