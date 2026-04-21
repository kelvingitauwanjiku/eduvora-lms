<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use App\Models\PaymentHistory;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getDashboardMetrics($period = 30)
    {
        $startDate = Carbon::now()->subDays($period);
        
        return [
            'revenue' => $this->getRevenueMetrics($startDate),
            'enrollments' => $this->getEnrollmentMetrics($startDate),
            'courses' => Course::count(),
            'users' => User::count(),
            'reviews' => Review::count(),
        ];
    }

    public function getRevenueMetrics($startDate)
    {
        $current = PaymentHistory::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');

        $previous = PaymentHistory::where('status', 'completed')
            ->where('created_at', '>=', Carbon::parse($startDate)->subDays(
                Carbon::parse($startDate)->diffInDays(now())
            ))
            ->sum('amount');

        return [
            'total' => (float) $current,
            'growth' => $this->calculateGrowth($current, $previous),
        ];
    }

    public function getEnrollmentMetrics($startDate)
    {
        $current = Enrollment::where('created_at', '>=', $startDate)->count();
        
        $previous = Enrollment::whereBetween('created_at', [
            Carbon::parse($startDate)->subDays(30),
            $startDate
        ])->count();

        return [
            'total' => $current,
            'growth' => $this->calculateGrowth($current, $previous),
        ];
    }

    protected function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }

    public function getTimeSeriesData($startDate, $endDate, $model)
    {
        return $model::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
    }

    public function getTopPerformers($type = 'courses', $limit = 10, $period = null)
    {
        $query = match ($type) {
            'courses' => Course::query(),
            'instructors' => User::query(),
            default => null,
        };

        if (!$query) return [];

        return $query->when($period, function ($q) use ($period) {
            $q->where('created_at', '>=', Carbon::now()->subDays($period));
        })->limit($limit)->get();
    }
}