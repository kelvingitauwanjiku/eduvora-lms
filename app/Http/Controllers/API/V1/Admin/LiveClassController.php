<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveClass\LiveClass;
use App\Models\LiveClass\LiveClassParticipant;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    public function index(Request $request)
    {
        $query = LiveClass::with(['course', 'instructor']);

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $classes = $query->orderBy('scheduled_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $classes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'duration' => 'required|integer|min:15',
            'meeting_link' => 'nullable|string',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|string',
        ]);

        $validated['instructor_id'] = auth()->id();

        $liveClass = LiveClass::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Live class scheduled successfully',
            'data' => $liveClass,
        ], 201);
    }

    public function show(int $id)
    {
        $liveClass = LiveClass::with(['course', 'instructor', 'participants'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $liveClass,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $liveClass = LiveClass::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'date',
            'duration' => 'integer|min:15',
            'meeting_link' => 'nullable|string',
            'status' => 'in:scheduled,live,ended,cancelled',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|string',
        ]);

        $liveClass->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Live class updated successfully',
            'data' => $liveClass,
        ]);
    }

    public function destroy(int $id)
    {
        $liveClass = LiveClass::findOrFail($id);
        $liveClass->delete();

        return response()->json([
            'success' => true,
            'message' => 'Live class deleted successfully',
        ]);
    }

    public function start(int $id)
    {
        $liveClass = LiveClass::findOrFail($id);
        $liveClass->update(['status' => 'live']);

        return response()->json([
            'success' => true,
            'message' => 'Live class started',
            'data' => $liveClass,
        ]);
    }

    public function end(int $id)
    {
        $liveClass = LiveClass::findOrFail($id);
        $liveClass->update(['status' => 'ended']);

        return response()->json([
            'success' => true,
            'message' => 'Live class ended',
            'data' => $liveClass,
        ]);
    }

    public function participants(int $id)
    {
        $participants = LiveClassParticipant::with('user')
            ->where('live_class_id', $id)
            ->orderBy('joined_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $participants,
        ]);
    }

    public function settings()
    {
        $settings = [
            'default_duration' => 60,
            'max_participants' => 100,
            'enable_recording' => true,
            'enable_chat' => true,
            'enable_screen_share' => true,
        ];

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'default_duration' => 'integer|min:15',
            'max_participants' => 'integer|min:1',
            'enable_recording' => 'boolean',
            'enable_chat' => 'boolean',
            'enable_screen_share' => 'boolean',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $validated,
        ]);
    }
}
