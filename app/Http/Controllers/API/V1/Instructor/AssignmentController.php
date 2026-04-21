<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course\Assignment;
use App\Models\Course\SubmittedAssignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'total_points' => 'required|integer|min:1',
            'allow_late_submission' => 'boolean',
            'late_penalty_percentage' => 'nullable|integer|min:0|max:100',
        ]);

        $assignment = Assignment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Assignment created successfully',
            'data' => $assignment,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $assignment = Assignment::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'total_points' => 'integer|min:1',
            'allow_late_submission' => 'boolean',
            'late_penalty_percentage' => 'nullable|integer|min:0|max:100',
        ]);

        $assignment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Assignment updated successfully',
            'data' => $assignment,
        ]);
    }

    public function destroy(int $id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assignment deleted successfully',
        ]);
    }

    public function status(int $id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->update(['is_active' => ! $assignment->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $assignment->is_active,
        ]);
    }

    public function submissions(int $assignmentId)
    {
        $submissions = SubmittedAssignment::with('user')
            ->where('assignment_id', $assignmentId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $submissions,
        ]);
    }

    public function review(Request $request, int $id)
    {
        $submission = SubmittedAssignment::findOrFail($id);

        $validated = $request->validate([
            'feedback' => 'nullable|string',
            'points_earned' => 'required|integer|min:0',
            'status' => 'required|in:graded,needs_revision',
        ]);

        $submission->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Assignment reviewed successfully',
            'data' => $submission,
        ]);
    }

    public function downloadSubmission(int $id)
    {
        $submission = SubmittedAssignment::findOrFail($id);

        return response()->json([
            'success' => true,
            'file_url' => $submission->file_url,
            'file_name' => $submission->file_name,
        ]);
    }
}
