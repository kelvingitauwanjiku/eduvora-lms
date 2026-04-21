<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::with(['category', 'instructor', 'enrollments'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $courses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:courses,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|string',
            'preview_video' => 'nullable|string',
            'requirements' => 'nullable|array',
            'outcomes' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_public' => 'boolean',
            'publish_immediately' => 'boolean',
            'level' => 'in:beginner,intermediate,advanced',
            'duration' => 'nullable|integer',
            'drip_content' => 'boolean',
            'drip_content_days' => 'nullable|integer',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();

        $course = Course::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully',
            'data' => $course,
        ], 201);
    }

    public function show(int $id)
    {
        $course = Course::with([
            'category',
            'instructor',
            'sections.lessons',
            'sections.quiz',
            'reviews',
            'enrollments',
        ])->findOrFail($id);

        $this->authorize('view', $course);

        return response()->json([
            'success' => true,
            'data' => $course,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $course = Course::findOrFail($id);
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|unique:courses,slug,'.$id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|string',
            'preview_video' => 'nullable|string',
            'requirements' => 'nullable|array',
            'outcomes' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_public' => 'boolean',
            'publish_immediately' => 'boolean',
            'level' => 'in:beginner,intermediate,advanced',
            'duration' => 'nullable|integer',
            'drip_content' => 'boolean',
            'drip_content_days' => 'nullable|integer',
        ]);

        $course->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => $course,
        ]);
    }

    public function destroy(int $id)
    {
        $course = Course::findOrFail($id);
        $this->authorize('delete', $course);

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully',
        ]);
    }

    public function duplicate(int $id)
    {
        $course = Course::with(['sections.lessons', 'sections.quiz'])->findOrFail($id);

        $newCourse = $course->replicate();
        $newCourse->title = $course->title.' (Copy)';
        $newCourse->slug = $course->slug.'-'.time();
        $newCourse->is_active = false;
        $newCourse->save();

        foreach ($course->sections as $section) {
            $newSection = $section->replicate();
            $newSection->course_id = $newCourse->id;
            $newSection->save();

            foreach ($section->lessons as $lesson) {
                $newLesson = $lesson->replicate();
                $newLesson->section_id = $newSection->id;
                $newLesson->save();
            }

            if ($section->quiz) {
                $newQuiz = $section->quiz->replicate();
                $newQuiz->section_id = $newSection->id;
                $newQuiz->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Course duplicated successfully',
            'data' => $newCourse->load('sections.lessons'),
        ], 201);
    }

    public function draft(int $id)
    {
        $course = Course::findOrFail($id);
        $course->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Course set to draft',
        ]);
    }

    public function publish(int $id)
    {
        $course = Course::findOrFail($id);
        $course->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Course published',
        ]);
    }

    public function enrollments(int $courseId)
    {
        $enrollments = Enrollment::with('user')
            ->where('course_id', $courseId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $enrollments,
        ]);
    }

    public function studentsProgress(int $courseId)
    {
        $enrollments = Enrollment::with(['user', 'progress', 'certificate'])
            ->where('course_id', $courseId)
            ->where('completed', false)
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $enrollments,
        ]);
    }

    public function revenue(Request $request, int $courseId)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $enrollments = Enrollment::where('course_id', $courseId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('completed', true)
            ->get();

        $totalRevenue = $enrollments->sum('price');
        $studentCount = $enrollments->count();

        return response()->json([
            'success' => true,
            'total_revenue' => $totalRevenue,
            'student_count' => $studentCount,
            'enrollments' => $enrollments,
        ]);
    }
}
