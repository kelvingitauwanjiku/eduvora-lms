<?php

namespace App\Services\Logging;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class ApiLoggerService
{
    private string $table = 'api_logs';

    public function log(array $data): void
    {
        $payload = [
            'user_id' => $data['user_id'] ?? null,
            'method' => $data['method'] ?? Request::method(),
            'path' => $data['path'] ?? Request::path(),
            'route_name' => $data['route_name'] ?? Request::route()->getName(),
            'ip_address' => $this->getIpAddress(),
            'user_agent' => Request::userAgent(),
            'request_headers' => $this->sanitizeHeaders(Request::header()),
            'request_body' => $this->sanitizeBody(Request::all()),
            'response_status' => $data['response_status'] ?? null,
            'response_body' => $data['response_body'] ?? null,
            'duration' => $data['duration'] ?? null,
            'memory' => $data['memory'] ?? null,
            'endpoint' => $data['endpoint'] ?? null,
            'action' => $data['action'] ?? null,
            'status' => $data['status'] ?? 'success',
            'created_at' => now(),
        ];

        try {
            DB::table($this->table)->insert($payload);
        } catch (\Exception $e) {
            Log::error('API Log failed: ' . $e->getMessage());
        }
    }

    public function logRequest(array $data = []): void
    {
        $this->log(array_merge([
            'status' => 'request',
            'request_body' => $this->sanitizeBody(Request::all()),
        ], $data));
    }

    public function logResponse(int $status, mixed $body = null, ?float $duration = null): void
    {
        $this->log([
            'response_status' => $status,
            'response_body' => $this->sanitizeResponse($body),
            'duration' => $duration,
            'memory' => memory_get_peak_usage(true) / 1024 / 1024,
        ]);
    }

    public function logError(\Throwable $e, array $context = []): void
    {
        $this->log(array_merge([
            'status' => 'error',
            'response_body' => [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ],
            'context' => $context,
        ], $context));
    }

    public function logAuth(array $data): void
    {
        $this->log(array_merge([
            'action' => 'auth',
            'status' => $data['status'] ?? 'success',
        ], $data));
    }

    public function logPayment(array $data): void
    {
        $this->log(array_merge([
            'action' => 'payment',
            'status' => $data['status'] ?? 'success',
            'endpoint' => $data['endpoint'],
        ], $data));
    }

    public function logCourseAccess(int $courseId, string $action): void
    {
        $this->log([
            'action' => 'course_access',
            'endpoint' => "course:{$courseId}",
            'user_agent' => $action,
        ]);
    }

    public function logSearch(string $query, array $results): void
    {
        $this->log([
            'action' => 'search',
            'request_body' => ['query' => $query],
            'response_body' => ['count' => count($results)],
        ]);
    }

    public function getLogs(array $filters = [], int $perPage = 50)
    {
        $query = DB::table($this->table)
            ->orderBy('created_at', 'desc');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['ip_address'])) {
            $query->where('ip_address', $filters['ip_address']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage);
    }

    public function getEndpointStats(string $endpoint, ?string $dateFrom = null): array
    {
        $query = DB::table($this->table)
            ->where('endpoint', $endpoint);

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        $stats = $query->selectRaw('
            COUNT(*) as total_requests,
            AVG(duration) as avg_duration,
            MAX(duration) as max_duration,
            SUM(CASE WHEN status = "error" THEN 1 ELSE 0 END) as error_count
        ')->first();

        return [
            'total_requests' => $stats->total_requests ?? 0,
            'avg_duration' => round($stats->avg_duration ?? 0, 2),
            'max_duration' => round($stats->max_duration ?? 0, 2),
            'error_count' => $stats->error_count ?? 0,
            'success_rate' => $stats->total_requests 
                ? round(($stats->total_requests - ($stats->error_count ?? 0)) / $stats->total_requests * 100, 2)
                : 100,
        ];
    }

    public function getUserActivity(int $userId, int $days = 30): array
    {
        return DB::table($this->table)
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as requests,
                SUM(CASE WHEN status = "error" THEN 1 ELSE 0 END) as errors
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    public function getTopEndpoints(int $limit = 10): array
    {
        return DB::table($this->table)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('
                endpoint,
                COUNT(*) as requests,
                AVG(duration) as avg_duration,
                SUM(CASE WHEN status = "error" THEN 1 ELSE 0 END) as errors
            ')
            ->groupBy('endpoint')
            ->orderBy('requests', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getIpAnalysis(): array
    {
        return DB::table($this->table)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('
                ip_address,
                COUNT(*) as requests,
                COUNT(DISTINCT user_id) as unique_users,
                SUM(CASE WHEN status = "error" THEN 1 ELSE 0 END) as errors
            ')
            ->groupBy('ip_address')
            ->orderBy('requests', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function cleanOldLogs(int $days = 90): int
    {
        return DB::table($this->table)
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }

    protected function getIpAddress(): string
    {
        $ip = Request::ip();
        
        if (Request::header('X-Forwarded-For')) {
            $ip = explode(',', Request::header('X-Forwarded-For'))[0];
        } elseif (Request::header('X-Real-IP')) {
            $ip = Request::header('X-Real-IP');
        }
        
        return trim($ip);
    }

    protected function sanitizeHeaders(array $headers): array
    {
        $sanitized = [];
        $sensitive = ['authorization', 'cookie', 'x-api-key', 'x-auth-token'];
        
        foreach ($headers as $key => $value) {
            if (in_array(strtolower($key), $sensitive)) {
                $sanitized[$key] = '***';
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }

    protected function sanitizeBody(array $body): array
    {
        $sanitized = [];
        $sensitive = ['password', 'password_confirmation', 'token', 'secret', 'api_key', 'credit_card', 'card_number'];
        
        foreach ($body as $key => $value) {
            if (in_array(strtolower($key), $sensitive)) {
                $sanitized[$key] = '***';
            } else {
                $sanitized[$key] = is_array($value) 
                    ? $this->sanitizeBody($value) 
                    : $value;
            }
        }
        
        return $sanitized;
    }

    protected function sanitizeResponse(mixed $response): ?array
    {
        if (is_string($response)) {
            return ['message' => $response];
        }
        
        if (is_array($response)) {
            return $this->sanitizeBody($response);
        }
        
        return null;
    }
}