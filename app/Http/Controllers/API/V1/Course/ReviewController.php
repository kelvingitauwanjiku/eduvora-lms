<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Course\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function courseReviews(int $id): JsonResponse
    {
        $reviews = Review::where('course_id', $id)
            ->where('status', 'approved')
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($reviews);
    }

    public function show(int $id): JsonResponse
    {
        $review = Review::with(['user', 'course'])->findOrFail($id);
        return response()->json($review);
    }

    public function store(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'review' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $course = Course::findOrFail($id);
        
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'Already reviewed'], 400);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'review' => $request->review,
            'rating' => $request->rating,
            'rating_quality' => $request->rating,
            'rating_instructor' => $request->rating,
            'rating_value' => $request->rating,
            'status' => 'pending',
        ]);

        return response()->json($review, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $review = Review::where('user_id', $request->user()->id)->findOrFail($id);
        
        $request->validate([
            'review' => 'string|min:10',
            'rating' => 'integer|min:1|max:5',
        ]);

        $review->update($request->only(['review', 'rating']));

        return response()->json($review);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $review = Review::where('user_id', $request->user()->id)->findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Review deleted']);
    }
}