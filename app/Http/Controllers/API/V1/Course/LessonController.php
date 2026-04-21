<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use App\Models\Course\Lesson;
use App\Models\Course\CourseProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function updateProgress(Request $request, int $id): JsonResponse
    {
        $lesson = Lesson::findOrFail($id);
        $user = $request->user();

        $enrollment = \App\Models\Course\Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->section->course->id)
            ->first();

        if (!$enrollment) {
            return response()->json(['message' => 'Not enrolled'], 403);
        }

        $progress = CourseProgress::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
                'watched_duration' => $request->get('duration', $lesson->duration),
            ]
        );

        return response()->json($progress);
    }

    public function markComplete(Request $request, int $id): JsonResponse
    {
        $lesson = Lesson::findOrFail($id);
        $user = $request->user();

        $enrollment = \App\Models\Course\Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->section->course->id)
            ->first();

        if (!$enrollment) {
            return response()->json(['message' => 'Not enrolled'], 403);
        }

        CourseProgress::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        return response()->json(['message' => 'Lesson marked as complete']);
    }
}