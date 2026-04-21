<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnrollmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $enrollments = Enrollment::where('user_id', $request->user()->id)
            ->with(['course', 'course.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($enrollments);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $enrollment = Enrollment::where('user_id', $request->user()->id)
            ->with(['course', 'course.sections.lessons'])
            ->findOrFail($id);

        return response()->json($enrollment);
    }

    public function enroll(Request $request, int $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $existingEnrollment = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return response()->json(['message' => 'Already enrolled'], 400);
        }

        $enrollment = Enrollment::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'order_number' => 'ORD-'.strtoupper(Str::random(10)),
            'price' => $course->discount_price ?? $course->price,
            'total_amount' => $course->discount_price ?? $course->price,
            'status' => 'active',
            'enrollment_type' => 'paid',
        ]);

        $course->increment('students_count');

        return response()->json($enrollment, 201);
    }
}
