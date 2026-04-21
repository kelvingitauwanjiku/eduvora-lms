<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Course\CourseApprovalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseApprovalController extends Controller
{
    public function pending(Request $request): JsonResponse
    {
        $courses = Course::where('status', 'pending')
            ->orWhere('status', 'under_review')
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($courses);
    }

    public function submitForApproval(Request $request, int $id): JsonResponse
    {
        $course = Course::where('user_id', $request->user()->id)->findOrFail($id);

        if ($course->status !== 'draft' && $course->status !== 'rejected') {
            return response()->json([
                'error' => 'Course cannot be submitted for approval',
                'current_status' => $course->status,
            ], 400);
        }

        $hasContent = $course->sections()
            ->withCount('lessons')
            ->get()
            ->sum('lessons_count');

        if ($hasContent < 1) {
            return response()->json([
                'error' => 'Course must have at least one lesson',
            ], 400);
        }

        DB::transaction(function () use ($course) {
            $course->update(['status' => 'pending']);

            CourseApprovalRequest::updateOrCreate(
                ['course_id' => $course->id],
                [
                    'status' => 'pending',
                    'submitted_at' => now(),
                    'notes' => 'Course submitted for approval',
                ]
            );
        });

        Log::info("Course {$course->id} submitted for approval");

        return response()->json([
            'message' => 'Course submitted for approval',
            'course' => $course->fresh(),
        ]);
    }

    public function startReview(Request $request, int $id): JsonResponse
    {
        $course = Course::where('status', 'pending')->findOrFail($id);

        $course->update([
            'status' => 'under_review',
            'reviewed_by' => $request->user()->id,
        ]);

        CourseApprovalRequest::where('course_id', $course->id)->update([
            'reviewer_id' => $request->user()->id,
            'review_started_at' => now(),
            'status' => 'under_review',
        ]);

        return response()->json([
            'message' => 'Review started',
            'course' => $course->fresh(),
        ]);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'feedback' => 'nullable|string',
            'auto_publish' => 'nullable|boolean',
        ]);

        $course = Course::whereIn('status', ['pending', 'under_review'])->findOrFail($id);

        DB::transaction(function () use ($course, $request) {
            $newStatus = $request->boolean('auto_publish', false) ? 'published' : 'approved';
            
            $course->update([
                'status' => $newStatus,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'admin_notes' => $request->feedback,
            ]);

            CourseApprovalRequest::where('course_id', $course->id)->update([
                'status' => 'approved',
                'reviewer_id' => $request->user()->id,
                'review_completed_at' => now(),
                'feedback' => $request->feedback,
            ]);
        });

        Log::info("Course {$course->id} approved by {$request->user()->id}");

        return response()->json([
            'message' => 'Course approved',
            'course' => $course->fresh(),
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string',
            'detailed_feedback' => 'nullable|string',
        ]);

        $course = Course::whereIn('status', ['pending', 'under_review'])->findOrFail($id);

        DB::transaction(function () use ($course, $request) {
            $course->update([
                'status' => 'rejected',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'rejection_reason' => $request->reason,
                'admin_notes' => $request->detailed_feedback,
            ]);

            CourseApprovalRequest::where('course_id', $course->id)->update([
                'status' => 'rejected',
                'reviewer_id' => $request->user()->id,
                'review_completed_at' => now(),
                'rejection_reason' => $request->reason,
                'detailed_feedback' => $request->detailed_feedback,
            ]);
        });

        Log::info("Course {$course->id} rejected: {$request->reason}");

        return response()->json([
            'message' => 'Course rejected',
            'course' => $course->fresh(),
        ]);
    }

    public function requestChanges(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'changes' => 'required|array',
            'changes.*.section' => 'required|string',
            'changes.*.description' => 'required|string',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $course = Course::whereIn('status', ['pending', 'under_review'])->findOrFail($id);

        $changes = $request->changes;
        $priority = $request->priority ?? 'medium';

        DB::transaction(function () use ($course, $request) {
            $course->update([
                'status' => 'changes_requested',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            CourseApprovalRequest::where('course_id', $course->id)->update([
                'status' => 'changes_requested',
                'reviewer_id' => $request->user()->id,
                'requested_changes' => $request->changes,
                'priority' => $request->priority ?? 'medium',
            ]);
        });

        Log::info("Changes requested for course {$course->id}");

        return response()->json([
            'message' => 'Changes requested',
            'course' => $course->fresh(),
            'requested_changes' => $changes,
        ]);
    }

    public function history(int $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $history = CourseApprovalRequest::where('course_id', $course->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'course' => $course,
            'history' => $history,
        ]);
    }

    public function stats(): JsonResponse
    {
        $stats = Course::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = "draft" THEN 1 ELSE 0 END) as drafts,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = "under_review" THEN 1 ELSE 0 END) as under_review,
            SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = "published" THEN 1 ELSE 0 END) as published,
            SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN status = "changes_requested" THEN 1 ELSE 0 END) as changes_requested
        ')->first();

        return response()->json([
            'total' => $stats->total,
            'drafts' => $stats->drafts,
            'pending' => $stats->pending,
            'under_review' => $stats->under_review,
            'approved' => $stats->approved,
            'published' => $stats->published,
            'rejected' => $stats->rejected,
            'changes_requested' => $stats->changes_requested,
        ]);
    }

    public function bulkApprove(Request $request): JsonResponse
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $count = 0;
        
        foreach ($request->course_ids as $id) {
            $course = Course::whereIn('status', ['pending', 'under_review'])->find($id);
            
            if ($course) {
                DB::transaction(function () use ($course, $request) {
                    $course->update([
                        'status' => 'published',
                        'reviewed_by' => $request->user()->id,
                        'reviewed_at' => now(),
                    ]);

                    CourseApprovalRequest::updateOrCreate(
                        ['course_id' => $course->id],
                        [
                            'status' => 'approved',
                            'reviewer_id' => $request->user()->id,
                            'review_completed_at' => now(),
                        ]
                    );
                });
                $count++;
            }
        }

        return response()->json([
            'message' => "{$count} courses approved",
            'approved_count' => $count,
        ]);
    }

    public function setAutoApprove(Request $request): JsonResponse
    {
        $request->validate([
            'enabled' => 'required|boolean',
            'min_rating' => 'nullable|numeric|min:0|max:5',
            'min_enrollments' => 'nullable|integer|min:0',
        ]);

        config(['settings.course.auto_approve' => $request->boolean('enabled')]);
        config(['settings.course.min_rating_for_autoapprove' => $request->min_rating ?? 4.5]);
        config(['settings.course.min_enrollments_for_autoapprove' => $request->min_enrollments ?? 10]);

        return response()->json([
            'enabled' => $request->boolean('enabled'),
            'min_rating' => $request->min_rating ?? 4.5,
            'min_enrollments' => $request->min_enrollments ?? 10,
        ]);
    }

    public function checkAutoApprove(int $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        if (!config('settings.course.auto_approve', false)) {
            return response()->json(['eligible' => false, 'reason' => 'auto_approve_disabled']);
        }

        if ($course->rating < config('settings.course.min_rating_for_autoapprove', 4.5)) {
            return response()->json([
                'eligible' => false, 
                'reason' => 'rating_too_low',
                'current_rating' => $course->rating,
                'required' => config('settings.course.min_rating_for_autoapprove', 4.5),
            ]);
        }

        if ($course->students_count < config('settings.course.min_enrollments_for_autoapprove', 10)) {
            return response()->json([
                'eligible' => false,
                'reason' => 'enrollments_too_low',
                'current_enrollments' => $course->students_count,
                'required' => config('settings.course.min_enrollments_for_autoapprove', 10),
            ]);
        }

        return response()->json(['eligible' => true]);
    }
}