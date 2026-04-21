<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\PaymentHistory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:users,courses,payments,enrollments',
            'format' => 'nullable|in:csv,xlsx,json',
            'filters' => 'nullable|array',
        ]);

        $format = $request->get('format', 'csv');

        return match ($request->type) {
            'users' => $this->exportUsers($request, $format),
            'courses' => $this->exportCourses($request, $format),
            'payments' => $this->exportPayments($request, $format),
            'enrollments' => $this->exportEnrollments($request, $format),
            default => response()->json(['error' => 'Invalid type'], 400),
        };
    }

    protected function exportUsers(Request $request, string $format): JsonResponse
    {
        $users = User::query()
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->when($request->status, fn ($q) => $q->where('is_active', $request->status))
            ->get();

        return response()->json([
            'count' => $users->count(),
            'data' => $users,
            'exported_at' => now(),
        ]);
    }

    protected function exportCourses(Request $request, string $format): JsonResponse
    {
        $courses = Course::query()
            ->with(['user', 'category'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->get();

        return response()->json([
            'count' => $courses->count(),
            'data' => $courses,
            'exported_at' => now(),
        ]);
    }

    protected function exportPayments(Request $request, string $format): JsonResponse
    {
        $payments = PaymentHistory::query()
            ->with(['user', 'course'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn ($q) => $q->where('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->where('created_at', '<=', $request->date_to))
            ->get();

        return response()->json([
            'count' => $payments->count(),
            'total_amount' => $payments->sum('total_amount'),
            'data' => $payments,
            'exported_at' => now(),
        ]);
    }

    protected function exportEnrollments(Request $request, string $format): JsonResponse
    {
        $enrollments = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->select('enrollments.*', 'users.name as user_name', 'users.email as user_email', 'courses.title as course_title')
            ->when($request->date_from, fn ($q) => $q->where('enrollments.created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->where('enrollments.created_at', '<=', $request->date_to))
            ->get();

        return response()->json([
            'count' => $enrollments->count(),
            'data' => $enrollments,
            'exported_at' => now(),
        ]);
    }

    public function template(Request $request): JsonResponse
    {
        return response()->json([
            'type' => $request->type ?? 'users',
            'columns' => match ($request->type) {
                'users' => ['name', 'email', 'role', 'is_active'],
                'courses' => ['title', 'slug', 'status', 'price', 'category_id'],
                'payments' => ['course_id', 'user_id', 'amount', 'status'],
                default => [],
            },
            'required' => ['email'],
        ]);
    }

    public function bulk(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:users,courses',
            'data' => 'required|array',
        ]);

        $results = [];

        foreach ($request->data as $item) {
            try {
                $result = match ($request->type) {
                    'users' => $this->importUser($item),
                    'courses' => $this->importCourse($item),
                    default => null,
                };
                $results[] = ['status' => 'success', 'data' => $result];
            } catch (\Exception $e) {
                $results[] = ['status' => 'error', 'error' => $e->getMessage(), 'data' => $item];
            }
        }

        return response()->json([
            'total' => count($request->data),
            'success' => collect($results)->where('status', 'success')->count(),
            'errors' => collect($results)->where('status', 'error')->count(),
            'results' => $results,
        ]);
    }

    protected function importUser(array $data): User
    {
        return User::create($data);
    }

    protected function importCourse(array $data): Course
    {
        return Course::create($data);
    }
}
