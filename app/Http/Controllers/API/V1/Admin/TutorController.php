<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\TutorBooking;
use App\Models\TutorCategory;
use App\Models\TutorSchedule;
use App\Models\TutorSubject;
use Illuminate\Http\Request;

class TutorController extends Controller
{
    public function categories()
    {
        $categories = TutorCategory::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category = TutorCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    public function updateCategory(Request $request, int $id)
    {
        $category = TutorCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    public function deleteCategory(int $id)
    {
        $category = TutorCategory::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }

    public function categoryStatus(int $id)
    {
        $category = TutorCategory::findOrFail($id);
        $category->update(['is_active' => ! $category->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $category->is_active,
        ]);
    }

    public function index(Request $request)
    {
        $query = Tutor::with(['user', 'category', 'subjects']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        $tutors = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $tutors,
        ]);
    }

    public function show(int $id)
    {
        $tutor = Tutor::with(['user', 'category', 'subjects', 'schedules', 'reviews'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $tutor,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $tutor = Tutor::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'exists:tutor_categories,id',
            'bio' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'languages' => 'nullable|array',
            'qualifications' => 'nullable|array',
            'experience_years' => 'nullable|integer|min:0',
        ]);

        $tutor->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tutor updated successfully',
            'data' => $tutor,
        ]);
    }

    public function subjects()
    {
        $subjects = TutorSubject::with(['category', 'tutor.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }

    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'tutor_id' => 'required|exists:tutors,id',
            'category_id' => 'required|exists:tutor_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $subject = TutorSubject::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Subject created successfully',
            'data' => $subject,
        ], 201);
    }

    public function updateSubject(Request $request, int $id)
    {
        $subject = TutorSubject::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'exists:tutor_categories,id',
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $subject->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Subject updated successfully',
            'data' => $subject,
        ]);
    }

    public function deleteSubject(int $id)
    {
        $subject = TutorSubject::findOrFail($id);
        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subject deleted successfully',
        ]);
    }

    public function subjectStatus(int $id)
    {
        $subject = TutorSubject::findOrFail($id);
        $subject->update(['is_active' => ! $subject->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $subject->is_active,
        ]);
    }

    public function schedules(int $tutorId)
    {
        $schedules = TutorSchedule::where('tutor_id', $tutorId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'tutor_id' => 'required|exists:tutors,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'boolean',
        ]);

        $schedule = TutorSchedule::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Schedule created successfully',
            'data' => $schedule,
        ], 201);
    }

    public function updateSchedule(Request $request, int $id)
    {
        $schedule = TutorSchedule::findOrFail($id);

        $validated = $request->validate([
            'day_of_week' => 'integer|min:0|max:6',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i',
            'is_available' => 'boolean',
        ]);

        $schedule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully',
            'data' => $schedule,
        ]);
    }

    public function deleteSchedule(int $id)
    {
        $schedule = TutorSchedule::findOrFail($id);
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully',
        ]);
    }

    public function bookings(Request $request)
    {
        $query = TutorBooking::with(['user', 'tutor.user', 'subject']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('tutor_id')) {
            $query->where('tutor_id', $request->tutor_id);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    public function updateBookingStatus(Request $request, int $id)
    {
        $booking = TutorBooking::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated',
            'data' => $booking,
        ]);
    }
}
