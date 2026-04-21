<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $pages = $query->orderBy('title')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $pages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:pages,slug',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        $page = Page::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Page created successfully',
            'data' => $page,
        ], 201);
    }

    public function show(int $id)
    {
        $page = Page::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $page,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|unique:pages,slug,'.$id,
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $page->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Page updated successfully',
            'data' => $page,
        ]);
    }

    public function destroy(int $id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully',
        ]);
    }

    public function status(int $id)
    {
        $page = Page::findOrFail($id);
        $page->update(['is_active' => ! $page->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $page->is_active,
        ]);
    }
}
