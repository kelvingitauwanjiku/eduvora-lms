<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use App\Models\User;
use App\Models\PaymentHistory;
use App\Models\Support\Ticket;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KpiController extends Controller
{
    public function dashboard(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);
        
        $data = [
            'revenue' => $this->getRevenueStats($period),
            'enrollments' => $this->getEnrollmentStats($period),
            'courses' => $this->getCourseStats($period),
            'users' => $this->getUserStats($period),
            'reviews' => $this->getReviewStats($period),
            'tickets' => $this->getTicketStats($period),
            'top_courses' => $this->getTopCourses($period),
            'top_instructors' => $this->getTopInstructors($period),
            'revenue_chart' => $this->getRevenueChart($period),
            'enrollment_chart' => $this->getEnrollmentChart($period),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'period' => $period,
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    protected function getRevenueStats($period)
    {
        $revenue = PaymentHistory::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays($period))
            ->sum('amount');

        $previousRevenue = PaymentHistory::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays($period * 2))
            ->where('created_at', '<', Carbon::now()->subDays($period))
            ->sum('amount');

        $growth = $previousRevenue > 0 ? (($revenue - $previousRevenue) / $previousRevenue) * 100 : 0;

        return [
            'total' => (float) $revenue,
            'currency' => 'USD',
            'growth' => round($growth, 2),
        ];
    }

    protected function getEnrollmentStats($period)
    {
        $enrollments = Enrollment::where('created_at', '>=', Carbon::now()->subDays($period))->count();

        $previousEnrollments = Enrollment::where('created_at', '>=', Carbon::now()->subDays($period * 2))
            ->where('created_at', '<', Carbon::now()->subDays($period))
            ->count();

        $growth = $previousEnrollments > 0 ? (($enrollments - $previousEnrollments) / $previousEnrollments) * 100 : 0;

        return [
            'total' => $enrollments,
            'growth' => round($growth, 2),
        ];
    }

    protected function getCourseStats($period)
    {
        $total = Course::count();
        $published = Course::where('status', 'published')->count();
        $newThisPeriod = Course::where('created_at', '>=', Carbon::now()->subDays($period))->count();

        return [
            'total' => $total,
            'published' => $published,
            'draft' => $total - $published,
            'new' => $newThisPeriod,
        ];
    }

    protected function getUserStats($period)
    {
        $total = User::count();
        $students = User::where('role_id', 3)->count();
        $instructors = User::where('role_id', 2)->count();
        $newThisPeriod = User::where('created_at', '>=', Carbon::now()->subDays($period))->count();

        return [
            'total' => $total,
            'students' => $students,
            'instructors' => $instructors,
            'new' => $newThisPeriod,
        ];
    }

    protected function getReviewStats($period)
    {
        $total = Review::where('created_at', '>=', Carbon::now()->subDays($period))->count();
        $avgRating = Review::avg('rating') ?? 0;

        return [
            'total' => $total,
            'average_rating' => round($avgRating, 1),
        ];
    }

    protected function getTicketStats($period)
    {
        $open = SupportTicket::where('status', 'open')->count();
        $resolved = SupportTicket::where('status', 'resolved')
            ->where('updated_at', '>=', Carbon::now()->subDays($period))
            ->count();

        return [
            'open' => $open,
            'resolved' => $resolved,
        ];
    }

    protected function getTopCourses($period)
    {
        return Enrollment::select('course_id', DB::raw('COUNT(*) as enrollments'))
            ->where('created_at', '>=', Carbon::now()->subDays($period))
            ->groupBy('course_id')
            ->orderByDesc('enrollments')
            ->limit(10)
            ->with('course:id,title,thumbnail')
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->course_id,
                    'title' => $enrollment->course?->title,
                    'thumbnail' => $enrollment->course?->thumbnail,
                    'enrollments' => $enrollment->enrollments,
                ];
            });
    }

    protected function getTopInstructors($period)
    {
        return Enrollment::select('user_id', DB::raw('COUNT(*) as enrollments'))
            ->where('created_at', '>=', Carbon::now()->subDays($period))
            ->groupBy('user_id')
            ->orderByDesc('enrollments')
            ->limit(10)
            ->with('user:id,name,avatar')
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->user_id,
                    'name' => $enrollment->user?->name,
                    'avatar' => $enrollment->user?->avatar,
                    'enrollments' => $enrollment->enrollments,
                ];
            });
    }

    protected function getRevenueChart($period)
    {
        $data = [];
        for ($i = $period; $i >= 0; $i -= 7) {
            $date = Carbon::now()->subDays($i);
            $revenue = PaymentHistory::where('status', 'completed')
                ->whereDate('created_at', '>=', $date->copy()->subDays(7))
                ->whereDate('created_at', '<=', $date)
                ->sum('amount');

            $data[] = [
                'period' => $date->format('M d'),
                'revenue' => (float) $revenue,
            ];
        }

        return array_reverse($data);
    }

    protected function getEnrollmentChart($period)
    {
        $data = [];
        for ($i = $period; $i >= 0; $i -= 7) {
            $date = Carbon::now()->subDays($i);
            $enrollments = Enrollment::whereDate('created_at', '>=', $date->copy()->subDays(7))
                ->whereDate('created_at', '<=', $date)
                ->count();

            $data[] = [
                'period' => $date->format('M d'),
                'enrollments' => $enrollments,
            ];
        }

        return array_reverse($data);
    }

    public function searchAnalytics(Request $request)
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 10);
        
        $results = [];
        
        if (strlen($query) >= 2) {
            $courses = Course::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->limit($limit)
                ->get(['id', 'title', 'thumbnail', 'price']);

            $users = User::where('name', 'like', "%{$query}%")
                ->limit($limit)
                ->get(['id', 'name', 'avatar']);

            $results = [
                'courses' => $courses,
                'users' => $users,
                'total' => $courses->count() + $users->count(),
            ];
        }

        $popularSearches = $this->getPopularSearches();

        return response()->json([
            'success' => true,
            'query' => $query,
            'results' => $results,
            'popular_searches' => $popularSearches,
        ]);
    }

    protected function getPopularSearches()
    {
        return [
            'web development',
            'python',
            'javascript',
            'machine learning',
            'react',
            'data science',
        ];
    }

    public function reports(Request $request)
    {
        $type = $request->get('type', 'revenue');
        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());
        
        $data = match($type) {
            'revenue' => $this->generateRevenueReport($startDate, $endDate),
            'enrollments' => $this->generateEnrollmentReport($startDate, $endDate),
            'courses' => $this->generateCourseReport($startDate, $endDate),
            'users' => $this->generateUserReport($startDate, $endDate),
            default => [],
        };

        return response()->json([
            'success' => true,
            'type' => $type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data' => $data,
        ]);
    }

    protected function generateRevenueReport($startDate, $endDate)
    {
        return PaymentHistory::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
    }

    protected function generateEnrollmentReport($startDate, $endDate)
    {
        return Enrollment::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
    }

    protected function generateCourseReport($startDate, $endDate)
    {
        return Course::whereBetween('created_at', [$startDate, $endDate])
            ->select('id', 'title', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    protected function generateUserReport($startDate, $endDate)
    {
        return User::whereBetween('created_at', [$startDate, $endDate])
            ->select('id', 'name', 'email', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}