<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::with(['user', 'category'])
            ->published()
            ->withCount(['enrollments']);

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('short_description', 'like', "%{$request->search}%");
            });
        }

        $courses = $query->orderBy('published_at', 'desc')
            ->paginate($request->get('per_page', 12));

        return response()->json($courses);
    }

    public function featured(): JsonResponse
    {
        $courses = Course::published()
            ->where('is_featured', true)
            ->with(['user'])
            ->orderBy('average_rating', 'desc')
            ->limit(8)
            ->get();

        return response()->json($courses);
    }

    public function show(int $id): JsonResponse
    {
        $course = Course::with([
            'user',
            'category',
            'sections.lessons',
        ])->findOrFail($id);

        return response()->json($course);
    }

    public function enroll(Request $request, int $id): JsonResponse
    {
        $course = Course::findOrFail($id);
        $user = $request->user();

        $existingEnrollment = Enrollment::where('user_id', $user->id)
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

    public function myEnrollments(Request $request): JsonResponse
    {
        $enrollments = Enrollment::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->with(['course', 'course.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($enrollments);
    }

    public function categories(): JsonResponse
    {
        $categories = Category::active()
            ->withCount(['courses'])
            ->orderBy('sort_order')
            ->get();

        return response()->json($categories);
    }

    public function categoryCourses(int $id): JsonResponse
    {
        $courses = Course::published()
            ->where('category_id', $id)
            ->with(['user'])
            ->orderBy('students_count', 'desc')
            ->paginate(12);

        return response()->json($courses);
    }

    public function popular(): JsonResponse
    {
        $courses = Course::published()
            ->orderBy('students_count', 'desc')
            ->with(['user'])
            ->limit(8)
            ->get();

        return response()->json($courses);
    }

    public function myWishlist(Request $request): JsonResponse
    {
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->with(['course'])
            ->get();

        return response()->json($wishlist);
    }

    public function addToWishlist(Request $request, int $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $existing = Wishlist::where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Already in wishlist'], 400);
        }

        $wishlist = Wishlist::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
        ]);

        $course->increment('wishlists_count');

        return response()->json($wishlist, 201);
    }

    public function removeFromWishlist(Request $request, int $id): JsonResponse
    {
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('course_id', $id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            Course::find($id)?->decrement('wishlists_count');
        }

        return response()->json(['message' => 'Removed from wishlist']);
    }
}
