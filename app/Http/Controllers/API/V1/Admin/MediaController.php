<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $media = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $media,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240',
            'name' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('media', 'public');

        $type = 'image';
        if (str_starts_with($file->getMimeType(), 'video/')) {
            $type = 'video';
        } elseif (str_starts_with($file->getMimeType(), 'audio/')) {
            $type = 'audio';
        } elseif ($file->getExtension() === 'pdf') {
            $type = 'document';
        }

        $media = Media::create([
            'name' => $validated['name'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'type' => $type,
            'alt_text' => $validated['alt_text'] ?? null,
            'caption' => $validated['caption'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Media uploaded successfully',
            'data' => $media,
        ], 201);
    }

    public function show(int $id)
    {
        $media = Media::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $media,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $media = Media::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
        ]);

        $media->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Media updated successfully',
            'data' => $media,
        ]);
    }

    public function destroy(int $id)
    {
        $media = Media::findOrFail($id);

        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully',
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:media,id',
        ]);

        $media = Media::whereIn('id', $request->ids)->get();

        foreach ($media as $item) {
            if (Storage::disk('public')->exists($item->file_path)) {
                Storage::disk('public')->delete($item->file_path);
            }
        }

        Media::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully',
        ]);
    }
}
