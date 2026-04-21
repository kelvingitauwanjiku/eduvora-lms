<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function instructors(): JsonResponse
    {
        $instructors = User::where('is_instructor', true)
            ->where('is_featured', true)
            ->orderBy('average_rating', 'desc')
            ->paginate(12);

        return response()->json($instructors);
    }

    public function profile(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        
        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'headline' => $user->headline,
            'bio' => $user->bio,
            'avatar' => $user->avatar,
            'is_verified' => $user->is_verified,
            'average_rating' => $user->average_rating,
            'reviews_count' => $user->reviews_count,
            'students_count' => $user->students_count,
            'courses_count' => $user->courses_count,
        ]);
    }

    public function instructorCourses(int $id): JsonResponse
    {
        $courses = Course::published()
            ->where('user_id', $id)
            ->orderBy('students_count', 'desc')
            ->get();

        return response()->json($courses);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'bio' => 'nullable|string',
            'headline' => 'nullable|string|max:255',
        ]);

        $request->user()->update($request->only([
            'first_name', 'last_name', 'bio', 'headline'
        ]));

        return response()->json($request->user());
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');
        
        $request->user()->update(['avatar' => $path]);

        return response()->json(['avatar' => $path]);
    }
}