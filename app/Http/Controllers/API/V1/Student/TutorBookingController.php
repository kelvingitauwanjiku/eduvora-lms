<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\TutorBooking;
use App\Models\TutorReview;
use App\Models\TutorSubject;
use Illuminate\Http\Request;

class TutorBookingController extends Controller
{
    public function myBookings()
    {
        $bookings = TutorBooking::with(['tutor.user', 'subject', 'schedule'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    public function book(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:tutor_subjects,id',
            'schedule_id' => 'required|exists:tutor_schedules,id',
            'notes' => 'nullable|string',
        ]);

        $subject = TutorSubject::with('tutor')->findOrFail($validated['subject_id']);

        $validated['user_id'] = auth()->id();
        $validated['tutor_id'] = $subject->tutor_id;
        $validated['status'] = 'pending';
        $validated['price'] = $subject->price_per_hour;

        $booking = TutorBooking::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => $booking->load('tutor.user', 'subject', 'schedule'),
        ], 201);
    }

    public function show(int $id)
    {
        $booking = TutorBooking::with(['tutor.user', 'subject', 'schedule', 'user'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $booking,
        ]);
    }

    public function cancel(int $id)
    {
        $booking = TutorBooking::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
        ]);
    }

    public function invoice(int $id)
    {
        $booking = TutorBooking::with(['tutor.user', 'subject', 'schedule', 'user'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $booking,
        ]);
    }

    public function joinClass(int $bookingId)
    {
        $booking = TutorBooking::where('id', $bookingId)
            ->where('user_id', auth()->id())
            ->where('status', 'confirmed')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'meeting_link' => $booking->meeting_link ?? null,
            'scheduled_at' => $booking->scheduled_at,
        ]);
    }

    public function review(Request $request, int $id)
    {
        $booking = TutorBooking::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->firstOrFail();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $review = TutorReview::create([
            'tutor_id' => $booking->tutor_id,
            'user_id' => auth()->id(),
            'booking_id' => $booking->id,
            'rating' => $validated['rating'],
            'review' => $validated['review'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'data' => $review,
        ], 201);
    }
}
