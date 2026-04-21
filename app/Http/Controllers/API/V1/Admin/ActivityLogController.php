<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('search')) {
            $query->where('description', 'like', '%'.$request->search.'%');
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    public function show(int $id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $log,
        ]);
    }

    public function userActivity(int $userId)
    {
        $logs = ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    public function byAction(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    public function statistics()
    {
        $totalLogs = ActivityLog::count();
        $todayLogs = ActivityLog::whereDate('created_at', today())->count();
        $thisWeekLogs = ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        $topUsers = ActivityLog::select('user_id')
            ->groupBy('user_id')
            ->selectRaw('COUNT(*) as count')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_logs' => $totalLogs,
                'today_logs' => $todayLogs,
                'this_week_logs' => $thisWeekLogs,
                'top_users' => $topUsers,
            ],
        ]);
    }
}
