<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportExportController extends Controller
{
    // ========== EXPORT ==========

    public function exportUsers(Request $request): JsonResponse
    {
        $users = User::query();

        if ($request->has('role')) {
            $users->where('is_instructor', $request->role === 'instructor');
        }

        if ($request->has('status')) {
            $users->where('status', $request->status);
        }

        $data = $users->select([
            'id', 'uuid', 'first_name', 'last_name', 'email', 'phone',
            'is_instructor', 'is_verified', 'is_featured', 'status',
            'balance', 'total_earnings', 'total_spent',
            'courses_count', 'students_count', 'average_rating',
            'created_at',
        ])->orderBy('created_at', 'desc')->get();

        return $this->generateCsv($data, 'users_export');
    }

    public function exportCourses(Request $request): JsonResponse
    {
        $courses = Course::query();

        if ($request->has('status')) {
            $courses->where('status', $request->status);
        }

        $data = $courses->select([
            'id', 'uuid', 'title', 'slug', 'category_id',
            'is_paid', 'price', 'discount_price', 'level',
            'language', 'duration', 'students_count',
            'average_rating', 'reviews_count', 'status',
            'published_at', 'created_at',
        ])->with(['user', 'category'])->orderBy('created_at', 'desc')->get();

        return $this->generateCsv($data, 'courses_export');
    }

    public function exportEnrollments(Request $request): JsonResponse
    {
        $enrollments = Enrollment::query()
            ->select([
                'id', 'uuid', 'user_id', 'course_id',
                'order_number', 'price', 'total_amount',
                'status', 'enrollment_type',
                'started_at', 'completed_at', 'created_at',
            ])
            ->with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->generateCsv($enrollments, 'enrollments_export');
    }

    // ========== IMPORT ==========

    public function importUsers(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('imports', 'local');

        $results = $this->processCsvUserImport(storage_path('app/'.$path));

        return response()->json([
            'message' => 'Import completed',
            'imported' => $results['imported'],
            'skipped' => $results['skipped'],
            'errors' => $results['errors'],
        ]);
    }

    public function importCourses(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('imports', 'local');

        $results = $this->processCsvCourseImport(storage_path('app/'.$path));

        return response()->json([
            'message' => 'Import completed',
            'imported' => $results['imported'],
            'skipped' => $results['skipped'],
            'errors' => $results['errors'],
        ]);
    }

    // ========== HELPERS ==========

    private function generateCsv($data, string $filename)
    {
        $headers = [];
        if ($data->isNotEmpty()) {
            $headers = array_keys($data->first()->toArray());
        }

        $csv = implode(',', $headers)."\n";

        foreach ($data as $row) {
            $values = array_values($row->toArray());
            $csv .= implode(',', $values)."\n";
        }

        return response()->json([
            'filename' => $filename.'_'.date('Y-m-d').'.csv',
            'headers' => $headers,
            'count' => $data->count(),
            'data' => $data,
        ]);
    }

    private function processCsvUserImport(string $path): array
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        $lines = file($path);
        $header = str_getcsv(array_shift($lines));

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $data = array_combine($header, str_getcsv($line));

            try {
                $exists = User::where('email', $data['email'] ?? null)->exists();

                if ($exists) {
                    $skipped++;

                    continue;
                }

                User::create([
                    'uuid' => Str::uuid()->toString(),
                    'first_name' => $data['first_name'] ?? 'User',
                    'last_name' => $data['last_name'] ?? '',
                    'name' => trim(($data['first_name'] ?? 'User').' '.($data['last_name'] ?? '')),
                    'email' => $data['email'] ?? '',
                    'password' => Hash::make('password'),
                    'is_instructor' => $data['is_instructor'] ?? false,
                    'status' => $data['status'] ?? 'active',
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return ['imported' => $imported, 'skipped' => $skipped, 'errors' => $errors];
    }

    private function processCsvCourseImport(string $path): array
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        $lines = file($path);
        $header = str_getcsv(array_shift($lines));

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $data = array_combine($header, str_getcsv($line));

            try {
                $exists = Course::where('title', $data['title'] ?? null)->exists();

                if ($exists) {
                    $skipped++;

                    continue;
                }

                Course::create([
                    'uuid' => Str::uuid()->toString(),
                    'title' => $data['title'] ?? 'Untitled Course',
                    'slug' => Str::slug($data['title'] ?? 'untitled'),
                    'short_description' => $data['short_description'] ?? '',
                    'description' => $data['description'] ?? '',
                    'price' => $data['price'] ?? 0,
                    'status' => $data['status'] ?? 'draft',
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return ['imported' => $imported, 'skipped' => $skipped, 'errors' => $errors];
    }
}
