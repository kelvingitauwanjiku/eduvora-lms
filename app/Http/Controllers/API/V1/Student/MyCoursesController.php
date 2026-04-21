<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Course\Certificate;
use App\Models\Course\CourseProgress;
use App\Models\Course\Enrollment;
use Illuminate\Http\Request;

class MyCoursesController extends Controller
{
    public function index(Request $request)
    {
        $enrollments = Enrollment::with(['course.category', 'course.instructor', 'progress'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $enrollments,
        ]);
    }

    public function show(int $id)
    {
        $enrollment = Enrollment::with([
            'course.category',
            'course.instructor',
            'course.sections.lessons',
            'course.sections.quiz',
            'progress',
            'certificate',
        ])->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $enrollment,
        ]);
    }

    public function progress(int $enrollmentId)
    {
        $progress = CourseProgress::where('enrollment_id', $enrollmentId)
            ->where('user_id', auth()->id())
            ->get();

        $enrollment = Enrollment::findOrFail($enrollmentId);

        $totalLessons = $enrollment->course->sections->reduce(function ($carry, $section) {
            return $carry + $section->lessons->count();
        }, 0);

        $completedLessons = $progress->where('completed', true)->count();
        $percentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        return response()->json([
            'success' => true,
            'completed_lessons' => $completedLessons,
            'total_lessons' => $totalLessons,
            'percentage' => $percentage,
            'progress' => $progress,
        ]);
    }

    public function certificate(int $enrollmentId)
    {
        $enrollment = Enrollment::where('id', $enrollmentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (! $enrollment->completed) {
            return response()->json([
                'success' => false,
                'error' => 'Course not completed yet',
            ], 400);
        }

        $certificate = Certificate::where('enrollment_id', $enrollmentId)->first();

        return response()->json([
            'success' => true,
            'data' => $certificate,
        ]);
    }

    public function downloadCertificate(int $enrollmentId)
    {
        $enrollment = Enrollment::where('id', $enrollmentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (! $enrollment->completed) {
            return response()->json([
                'success' => false,
                'error' => 'Course not completed yet',
            ], 400);
        }

        $certificate = Certificate::where('enrollment_id', $enrollmentId)->firstOrFail();

        return response()->json([
            'success' => true,
            'download_url' => route('certificates.download', $certificate->id),
        ]);
    }
}
