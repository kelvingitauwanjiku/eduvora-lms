<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use App\Models\PaymentHistory;
use App\Models\Review;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExportService
{
    protected array $modelMap = [
        'users' => User::class,
        'courses' => Course::class,
        'enrollments' => Enrollment::class,
        'payments' => PaymentHistory::class,
        'reviews' => Review::class,
    ];

    public function export(string $model, array $filters = [], string $format = 'csv')
    {
        $modelClass = $this->modelMap[$model] ?? null;
        
        if (!$modelClass) {
            throw new \InvalidArgumentException("Invalid model: {$model}");
        }

        $query = $modelClass::query();
        
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $query->where($key, $value);
            }
        }

        return match ($format) {
            'json' => $this->toJson($query->get()),
            'xlsx' => $this->toExcel($query->get()),
            default => $this->toCsv($query->get()),
        };
    }

    public function getColumns(string $model): array
    {
        return match ($model) {
            'users' => ['id', 'name', 'email', 'role_id', 'status', 'created_at'],
            'courses' => ['id', 'title', 'price', 'status', 'is_featured', 'created_at'],
            'enrollments' => ['id', 'user_id', 'course_id', 'completed', 'created_at'],
            'payments' => ['id', 'user_id', 'course_id', 'amount', 'currency', 'status', 'created_at'],
            'reviews' => ['id', 'user_id', 'course_id', 'rating', 'status', 'created_at'],
            default => [],
        };
    }

    public function toJson(Collection $data): string
    {
        return $data->toJson(JSON_PRETTY_PRINT);
    }

    public function toCsv(Collection $data): string
    {
        $columns = $data->first() ? array_keys($data->first()->toArray()) : [];
        
        $handle = fopen('php://memory', 'w');
        
        if (!empty($columns)) {
            fputcsv($handle, $columns);
        }
        
        foreach ($data as $row) {
            fputcsv($handle, $row->toArray());
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return $content;
    }

    public function toExcel(Collection $data)
    {
        return $data;
    }
}