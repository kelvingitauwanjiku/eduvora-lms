<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotificationSetting;
use Illuminate\Http\Request;

class NotificationManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with('user');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'link' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        $notification = Notification::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully',
            'data' => $notification,
        ], 201);
    }

    public function sendToAll(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'link' => 'nullable|string',
        ]);

        $users = User::where('is_active', true)->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $validated['type'],
                'title' => $validated['title'],
                'message' => $validated['message'],
                'link' => $validated['link'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification sent to '.$users->count().' users',
        ]);
    }

    public function sendToRole(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'link' => 'nullable|string',
        ]);

        $users = User::where('role_id', $validated['role_id'])->where('is_active', true)->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $validated['type'],
                'title' => $validated['title'],
                'message' => $validated['message'],
                'link' => $validated['link'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification sent to '.$users->count().' users',
        ]);
    }

    public function show(int $id)
    {
        $notification = Notification::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $notification,
        ]);
    }

    public function destroy(int $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully',
        ]);
    }

    public function markAllRead(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        Notification::where('is_read', false)
            ->when($request->user_id, fn ($q) => $q->where('user_id', $request->user_id))
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    public function userSettings(int $userId)
    {
        $settings = UserNotificationSetting::where('user_id', $userId)->get();

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function updateSettings(Request $request, int $userId)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'course_enrollment' => 'boolean',
            'course_completion' => 'boolean',
            'promotional' => 'boolean',
            'new_message' => 'boolean',
            'new_review' => 'boolean',
            'payout_processed' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            UserNotificationSetting::updateOrCreate(
                ['user_id' => $userId, 'setting_key' => $key],
                ['setting_value' => $value]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated',
        ]);
    }

    public function templates()
    {
        $templates = [
            'welcome' => ['title' => 'Welcome to Eduvora', 'message' => 'Get started with your learning journey!'],
            'course_enrolled' => ['title' => 'New Course Enrolled', 'message' => 'You have successfully enrolled in {course_name}'],
            'course_completed' => ['title' => 'Course Completed', 'message' => 'Congratulations on completing {course_name}!'],
            'certificate_issued' => ['title' => 'Certificate Earned', 'message' => 'Your certificate for {course_name} is ready'],
            'payment_received' => ['title' => 'Payment Received', 'message' => 'Payment of {amount} received'],
            'payout_processed' => ['title' => 'Payout Processed', 'message' => 'Your payout of {amount} has been processed'],
            'new_review' => ['title' => 'New Review Received', 'message' => 'You received a new {rating}-star review'],
            'new_message' => ['title' => 'New Message', 'message' => 'You have a new message from {sender}'],
        ];

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }
}
