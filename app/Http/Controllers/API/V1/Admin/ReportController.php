<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\BundlePurchase;
use App\Models\Course\Enrollment;
use App\Models\InstructorEarning;
use App\Models\PaymentHistory;
use App\Models\Payout;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function adminRevenue(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $revenue = PaymentHistory::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        $transactions = PaymentHistory::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'total_revenue' => $revenue,
            'transactions' => $transactions,
        ]);
    }

    public function instructorRevenue(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $earnings = InstructorEarning::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalEarnings = $earnings->sum('amount');

        return response()->json([
            'success' => true,
            'total_earnings' => $totalEarnings,
            'earnings' => $earnings,
        ]);
    }

    public function purchaseHistory(Request $request)
    {
        $type = $request->get('type', 'all');

        $query = Enrollment::with(['user', 'course']);

        if ($type === 'course') {
            $query = Enrollment::with(['user', 'course']);
        } elseif ($type === 'bundle') {
            $query = BundlePurchase::with(['user', 'bundle']);
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $purchases,
        ]);
    }

    public function courseEnrollments(Request $request, int $courseId)
    {
        $enrollments = Enrollment::with('user')
            ->where('course_id', $courseId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalRevenue = $enrollments->sum('price');

        return response()->json([
            'success' => true,
            'total_revenue' => $totalRevenue,
            'enrollments' => $enrollments,
        ]);
    }

    public function instructorPayouts(Request $request)
    {
        $payouts = Payout::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $payouts,
        ]);
    }

    public function exportRevenue(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $data = PaymentHistory::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
            'export_format' => 'csv',
        ]);
    }
}
