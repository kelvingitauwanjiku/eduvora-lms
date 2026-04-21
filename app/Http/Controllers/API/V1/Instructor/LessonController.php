<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course\CourseProgress;
use App\Models\Course\Enrollment;
use App\Models\Course\Lesson;
use App\Models\Course\LessonView;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lesson_type' => 'required|in:video,audio,text,pdf,image,scorm,youtube,vimeo',
            'video_url' => 'nullable|string',
            'video_source' => 'nullable|in:upload,youtube,vimeo,url',
            'duration' => 'nullable|integer|min:0',
            'is_preview' => 'boolean',
            'is_free' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'content' => 'nullable|string',
        ]);

        $lesson = Lesson::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lesson created successfully',
            'data' => $lesson,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $lesson = Lesson::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'lesson_type' => 'in:video,audio,text,pdf,image,scorm,youtube,vimeo',
            'video_url' => 'nullable|string',
            'video_source' => 'nullable|in:upload,youtube,vimeo,url',
            'duration' => 'nullable|integer|min:0',
            'is_preview' => 'boolean',
            'is_free' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'content' => 'nullable|string',
        ]);

        $lesson->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lesson updated successfully',
            'data' => $lesson,
        ]);
    }

    public function destroy(int $id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lesson deleted successfully',
        ]);
    }

    public function sort(Request $request)
    {
        $request->validate([
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|exists:lessons,id',
            'lessons.*.order' => 'required|integer',
        ]);

        foreach ($request->input('lessons') as $lessonData) {
            Lesson::where('id', $lessonData['id'])->update(['order' => $lessonData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lessons sorted successfully',
        ]);
    }

    public function views(int $lessonId)
    {
        $views = LessonView::with('user')
            ->where('lesson_id', $lessonId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $views,
        ]);
    }

    public function markComplete(Request $request, int $id)
    {
        $lesson = Lesson::findOrFail($id);

        $enrollment = Enrollment::where('course_id', $lesson->section->course_id)
            ->where('user_id', auth()->id())
            ->first();

        if (! $enrollment) {
            return response()->json([
                'success' => false,
                'error' => 'Not enrolled in this course',
            ], 403);
        }

        $progress = CourseProgress::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'completed' => true,
                'completed_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Lesson marked as complete',
            'data' => $progress,
        ]);
    }
}
