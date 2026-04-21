<?php

namespace App\Services;

use App\Models\Course\Course;
use App\Models\Course\CourseProgress;
use App\Models\Course\Enrollment;
use App\Models\Course\Lesson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseService
{
    public function getCourses(array $filters = [])
    {
        $query = Course::with(['user', 'category'])
            ->published();

        if (! empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (! empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('short_description', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('published_at', 'desc')
            ->paginate($filters['per_page'] ?? 12);
    }

    public function getCourse(int $id)
    {
        return Course::with([
            'user',
            'category',
            'sections.lessons',
            'tags',
        ])->findOrFail($id);
    }

    public function enroll(int $courseId, int $userId): Enrollment
    {
        $course = Course::findOrFail($courseId);

        $existingEnrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if ($existingEnrollment) {
            throw new \Exception('Already enrolled in this course');
        }

        return DB::transaction(function () use ($course, $userId) {
            $enrollment = Enrollment::create([
                'user_id' => $userId,
                'course_id' => $course->id,
                'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                'price' => $course->discount_price ?? $course->price,
                'total_amount' => $course->discount_price ?? $course->price,
                'status' => 'active',
                'enrollment_type' => 'paid',
            ]);

            $course->increment('students_count');

            return $enrollment;
        });
    }

    public function updateLessonProgress(int $enrollmentId, int $lessonId): CourseProgress
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $lesson = Lesson::findOrFail($lessonId);

        $progress = CourseProgress::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lessonId,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
                'watched_duration' => $lesson->duration,
            ]
        );

        $this->recalculateProgress($enrollment);

        return $progress;
    }

    public function recalculateProgress(Enrollment $enrollment): void
    {
        $totalLessons = $enrollment->course->lessons()->count();
        $completedLessons = CourseProgress::where('enrollment_id', $enrollment->id)
            ->where('is_completed', true)
            ->count();

        $percentage = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100)
            : 0;

        $enrollment->update([
            'completion_percentage' => $percentage,
            'completed_lessons' => $completedLessons,
            'total_lessons' => $totalLessons,
            'progress' => $percentage,
            'status' => $percentage >= 100 ? 'completed' : 'active',
            'completed_at' => $percentage >= 100 ? now() : null,
        ]);
    }

    public function getFeaturedCourses(int $limit = 8)
    {
        return Course::published()
            ->where('is_featured', true)
            ->with(['user'])
            ->orderBy('average_rating', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMyEnrollments(int $userId, array $filters = [])
    {
        $query = Enrollment::where('user_id', $userId)
            ->with(['course', 'course.user']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 10);
    }
}
