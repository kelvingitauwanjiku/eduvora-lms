<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeBaseTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $query = KnowledgeBase::with('topic');

        if ($request->has('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }

        if ($request->has('is_published')) {
            $query->where('is_published', $request->boolean('is_published'));
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:knowledge_base_topics,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:knowledge_bases,slug',
            'content' => 'required|string',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        $article = KnowledgeBase::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully',
            'data' => $article,
        ], 201);
    }

    public function show(int $id)
    {
        $article = KnowledgeBase::with('topic')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $article,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $article = KnowledgeBase::findOrFail($id);

        $validated = $request->validate([
            'topic_id' => 'exists:knowledge_base_topics,id',
            'title' => 'string|max:255',
            'slug' => 'string|unique:knowledge_bases,slug,'.$id,
            'content' => 'string',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $article->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully',
            'data' => $article,
        ]);
    }

    public function destroy(int $id)
    {
        $article = KnowledgeBase::findOrFail($id);
        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully',
        ]);
    }

    public function topics()
    {
        $topics = KnowledgeBaseTopic::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $topics,
        ]);
    }

    public function storeTopic(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:knowledge_base_topics,slug',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        $topic = KnowledgeBaseTopic::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Topic created successfully',
            'data' => $topic,
        ], 201);
    }

    public function updateTopic(Request $request, int $id)
    {
        $topic = KnowledgeBaseTopic::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'slug' => 'string|unique:knowledge_base_topics,slug,'.$id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $topic->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Topic updated successfully',
            'data' => $topic,
        ]);
    }

    public function destroyTopic(int $id)
    {
        $topic = KnowledgeBaseTopic::findOrFail($id);
        $topic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Topic deleted successfully',
        ]);
    }
}
