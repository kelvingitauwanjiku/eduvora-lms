<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\LanguagePhrase;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $languages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages,code',
            'native_name' => 'nullable|string|max:255',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'direction' => 'nullable|in:ltr,rtl',
        ]);

        if ($request->boolean('is_default')) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $language = Language::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Language created successfully',
            'data' => $language,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $language = Language::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'code' => 'string|max:10|unique:languages,code,'.$id,
            'native_name' => 'nullable|string|max:255',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'direction' => 'in:ltr,rtl',
        ]);

        if ($request->boolean('is_default')) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $language->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Language updated successfully',
            'data' => $language,
        ]);
    }

    public function destroy(int $id)
    {
        $language = Language::findOrFail($id);

        if ($language->is_default) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot delete default language',
            ], 400);
        }

        $language->delete();

        return response()->json([
            'success' => true,
            'message' => 'Language deleted successfully',
        ]);
    }

    public function phrases(int $id, Request $request)
    {
        $language = Language::findOrFail($id);

        $query = LanguagePhrase::where('language_id', $id);

        if ($request->has('search')) {
            $query->where('phrase', 'like', '%'.$request->search.'%');
        }

        $phrases = $query->orderBy('phrase')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $phrases,
        ]);
    }

    public function updatePhrase(int $id, int $phraseId, Request $request)
    {
        $phrase = LanguagePhrase::where('id', $phraseId)
            ->where('language_id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'translation' => 'required|string',
        ]);

        $phrase->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Phrase updated successfully',
            'data' => $phrase,
        ]);
    }

    public function export(int $id)
    {
        $phrases = LanguagePhrase::where('language_id', $id)->get();

        return response()->json([
            'success' => true,
            'data' => $phrases,
        ]);
    }

    public function setDefault(int $id)
    {
        Language::where('is_default', true)->update(['is_default' => false]);
        $language = Language::findOrFail($id);
        $language->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default language set',
        ]);
    }
}
