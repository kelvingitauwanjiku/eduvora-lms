<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $section = Section::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Section created successfully',
            'data' => $section,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $section = Section::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $section->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully',
            'data' => $section,
        ]);
    }

    public function destroy(int $id)
    {
        $section = Section::findOrFail($id);
        $section->delete();

        return response()->json([
            'success' => true,
            'message' => 'Section deleted successfully',
        ]);
    }

    public function sort(Request $request)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:sections,id',
            'sections.*.order' => 'required|integer',
        ]);

        foreach ($request->input('sections') as $sectionData) {
            Section::where('id', $sectionData['id'])->update(['order' => $sectionData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sections sorted successfully',
        ]);
    }
}
