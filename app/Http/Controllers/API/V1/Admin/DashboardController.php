<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use App\Models\PaymentHistory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function kpis(Request $request): JsonResponse
    {
        $period = $request->get('period', '30'); // days

        $startDate = now()->subDays($period);

        $totalUsers = User::count();
        $totalCourses = Course::published()->count();
        $totalEnrollments = Enrollment::count();

        $newUsers = User::where('created_at', '>=', $startDate)->count();
        $newEnrollments = Enrollment::where('created_at', '>=', $startDate)->count();

        $revenue = PaymentHistory::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->sum('total_amount');

        $avgRating = Course::published()->avg('average_rating') ?? 0;

        $coursesByCategory = DB::table('courses')
            ->join('categories', 'courses.category_id', '=', 'categories.id')
            ->where('courses.status', 'published')
            ->select('categories.name', DB::raw('COUNT(*) as count'))
            ->groupBy('categories.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $topCourses = Course::published()
            ->orderByDesc('students_count')
            ->limit(5)
            ->get(['id', 'title', 'students_count', 'average_rating']);

        $topInstructors = User::where('is_instructor', true)
            ->orderByDesc('total_earnings')
            ->limit(5)
            ->get(['id', 'first_name', 'last_name', 'total_earnings', 'students_count']);

        $enrollmentTrend = Enrollment::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'period_days' => (int) $period,
            'totals' => [
                'users' => $totalUsers,
                'courses' => $totalCourses,
                'enrollments' => $totalEnrollments,
                'new_users' => $newUsers,
                'new_enrollments' => $newEnrollments,
                'revenue' => (float) $revenue,
                'avg_rating' => round($avgRating, 2),
            ],
            'courses_by_category' => $coursesByCategory,
            'top_courses' => $topCourses,
            'top_instructors' => $topInstructors,
            'enrollment_trend' => $enrollmentTrend,
        ]);
    }

    public function analytics(Request $request): JsonResponse
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        $dailySales = PaymentHistory::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as revenue'),
            DB::raw('COUNT(*) as orders')
        )->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $byPaymentMethod = PaymentHistory::select(
            'payment_method',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total')
        )->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->groupBy('payment_method')
            ->get();

        $byStatus = Enrollment::select(
            'status',
            DB::raw('COUNT(*) as count')
        )->groupBy('status')
            ->get();

        return response()->json([
            'daily_sales' => $dailySales,
            'by_payment_method' => $byPaymentMethod,
            'by_enrollment_status' => $byStatus,
        ]);
    }
}
