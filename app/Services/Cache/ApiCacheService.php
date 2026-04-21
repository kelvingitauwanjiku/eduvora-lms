<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ApiCacheService
{
    private string $prefix = 'api:';
    private int $ttl = 3600;

    public function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $fullKey = $this->prefix . $key;
        $ttl = $ttl ?? $this->ttl;

        if ($this->isRedisEnabled()) {
            return Cache::store('redis')->remember($fullKey, $ttl, $callback);
        }

        return Cache::remember($fullKey, $ttl, $callback);
    }

    public function rememberForever(string $key, callable $callback): mixed
    {
        $fullKey = $this->prefix . $key;

        if ($this->isRedisEnabled()) {
            return Cache::store('redis')->remember($fullKey, now()->addYear(), $callback);
        }

        return Cache::remember($fullKey, now()->addYear(), $callback);
    }

    public function forget(string $key): bool
    {
        $fullKey = $this->prefix . $key;
        return Cache::forget($fullKey);
    }

    public function has(string $key): bool
    {
        return Cache::has($this->prefix . $key);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($this->prefix . $key, $default);
    }

    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        $fullKey = $this->prefix . $key;
        $ttl = $ttl ?? $this->ttl;

        if ($ttl) {
            return Cache::put($fullKey, $value, $ttl);
        }

        return Cache::put($fullKey, $value);
    }

    public function increment(string $key, int $value = 1): int|false
    {
        $fullKey = $this->prefix . $key;
        return Cache::increment($fullKey, $value);
    }

    public function decrement(string $key, int $value = 1): int|false
    {
        $fullKey = $this->prefix . $key;
        return Cache::decrement($fullKey, $value);
    }

    public function flush(string $pattern = '*'): int
    {
        if (!$this->isRedisEnabled()) {
            return 0;
        }

        $keys = Redis::keys($this->prefix . $pattern);
        
        if (empty($keys)) {
            return 0;
        }

        return Redis::del($keys);
    }

    public function tags(array $tags): self
    {
        if ($this->isRedisEnabled()) {
            Cache::tags($tags);
        }
        
        return $this;
    }

    public function invalidateTags(array $tags): bool
    {
        if (!$this->isRedisEnabled()) {
            return false;
        }

        return Cache::tags($tags)->flush();
    }

    protected function isRedisEnabled(): bool
    {
        try {
            Redis::connection()->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function cacheResponse(string $routeKey, callable $callback, ?int $ttl = null): mixed
    {
        return $this->remember("response:{$routeKey}", $callback, $ttl);
    }

    public function cacheCourseList(int $categoryId, callable $callback): mixed
    {
        return $this->remember("courses:list:{$categoryId}", $callback);
    }

    public function cacheCourse(int $courseId, callable $callback): mixed
    {
        return $this->remember("course:{$courseId}", $callback);
    }

    public function cacheCategory(int $categoryId, callable $callback): mixed
    {
        return $this->remember("category:{$categoryId}", $callback);
    }

    public function cacheUser(int $userId, callable $callback): mixed
    {
        return $this->remember("user:{$userId}", $callback);
    }

    public function cacheDashboard(string $userType, int $userId, callable $callback): mixed
    {
        return $this->remember("dashboard:{$userType}:{$userId}", $callback);
    }

    public function invalidateCourse(int $courseId): void
    {
        $this->forget("course:{$courseId}");
        $this->forget("courses:list:*");
        $this->forget("category:*");
    }

    public function invalidateUser(int $userId): void
    {
        $this->forget("user:{$userId}");
        $this->forget("dashboard:*:{$userId}");
    }

    public function invalidateCategory(int $categoryId): void
    {
        $this->forget("category:{$categoryId}");
        $this->forget("courses:list:{$categoryId}");
    }

    public function warmCourseCache(int $courseId, array $data): void
    {
        $this->remember("course:{$courseId}", fn() => $data);
    }

    public function warmCategoryCache(int $categoryId, array $data): void
    {
        $this->remember("category:{$categoryId}", fn() => $data);
    }

    public function getStats(): array
    {
        if (!$this->isRedisEnabled()) {
            return [
                'driver' => 'file',
                'keys' => 0,
                'memory' => 0,
            ];
        }

        $info = Redis::connection()->info('memory');
        $keys = Redis::keys($this->prefix . '*');

        return [
            'driver' => 'redis',
            'keys' => count($keys),
            'used_memory' => $info['used_memory_estimated'] ?? 0,
            'connected' => true,
        ];
    }
}