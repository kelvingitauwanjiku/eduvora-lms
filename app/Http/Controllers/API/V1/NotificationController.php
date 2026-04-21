<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\UserNotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->when($request->has('unread'), function ($query) use ($request) {
                if ($request->boolean('unread')) {
                    $query->whereNull('read_at');
                }
            })
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => Notification::where('user_id', $request->user()->id)
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    public function show(Notification $notification)
    {
        $this->authorize('view', $notification);

        return response()->json([
            'success' => true,
            'data' => $notification,
        ]);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        $this->authorize('update', $notification);

        $notification->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }

    public function clear(Request $request)
    {
        Notification::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared',
        ]);
    }

    public function settings(Request $request)
    {
        $settings = UserNotificationSetting::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'email_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
                'types' => [
                    'course_enrolled' => true,
                    'course_completed' => true,
                    'new_review' => true,
                    'support_reply' => true,
                    'new_course' => true,
                    'promotional' => true,
                    'payment' => true,
                ],
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'email_enabled' => 'nullable|boolean',
            'sms_enabled' => 'nullable|boolean',
            'push_enabled' => 'nullable|boolean',
            'types' => 'nullable|array',
            'types.*' => 'nullable|boolean',
        ]);

        $settings = UserNotificationSetting::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return response()->json([
            'success' => true,
            'data' => $settings,
            'message' => 'Notification settings updated',
        ]);
    }

    public function sendTest(Request $request)
    {
        $request->validate([
            'type' => 'nullable|string|in:email,sms,push',
        ]);

        $type = $request->input('type', 'push');

        $notification = Notification::create([
            'user_id' => $request->user()->id,
            'type' => 'test',
            'title' => 'Test Notification',
            'message' => 'This is a test notification to verify your settings work correctly.',
            'data' => ['test' => true],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test notification sent',
            'notification_id' => $notification->id,
        ]);
    }

    public function preferences(Request $request)
    {
        $preferences = config('eduvora.notification_preferences', []);

        return response()->json([
            'success' => true,
            'data' => $preferences,
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'preference' => 'required|string',
            'enabled' => 'required|boolean',
        ]);

        return response()->json([
            'success' => true,
            'data' => $validated,
            'message' => 'Preference updated',
        ]);
    }

    public function unreadCount(Request $request)
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    public function sendToast(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'type' => 'nullable|string|in:success,error,warning,info',
            'duration' => 'nullable|integer|min:1000|max:10000',
            'position' => 'nullable|string|in:top-left,top-right,bottom-left,bottom-right,top-center,bottom-center',
        ]);

        $toast = [
            'id' => uniqid('toast_'),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'] ?? 'info',
            'duration' => $validated['duration'] ?? 3000,
            'position' => $validated['position'] ?? 'top-right',
            'timestamp' => now()->toIso8601String(),
        ];

        return response()->json([
            'success' => true,
            'data' => $toast,
        ]);
    }
}