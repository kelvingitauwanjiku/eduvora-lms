<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeatureToggleController extends Controller
{
    protected $cachePrefix = 'feature_toggle_';

    public function index(Request $request)
    {
        $features = $this->getAllFeatures();
        $user = $request->user();
        $userRole = $user?->role_id ?? 'guest';

        $toggles = [];
        foreach ($features as $key => $feature) {
            $toggles[$key] = [
                'name' => $feature['name'],
                'description' => $feature['description'],
                'enabled' => $this->isEnabled($key),
                'enabled_for_role' => $this->getEnabledForRole($key, $userRole),
                ' Rollout' => $feature['rollout'] ?? 100,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $toggles,
            'user_role' => $userRole,
        ]);
    }

    public function show(string $feature)
    {
        if (! $this->featureExists($feature)) {
            return response()->json([
                'success' => false,
                'error' => 'Feature not found',
            ], 404);
        }

        $features = $this->getAllFeatures();
        $featureData = $features[$feature];

        return response()->json([
            'success' => true,
            'data' => [
                'key' => $feature,
                'name' => $featureData['name'],
                'description' => $featureData['description'],
                'enabled' => $this->isEnabled($feature),
                'rollout' => $this->getRollout($feature),
                'roles' => $this->getEnabledRoles($feature),
                'users' => $this->getEnabledUsers($feature),
                'created_at' => $this->getCreatedAt($feature),
                'updated_at' => $this->getUpdatedAt($feature),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:64|regex:/^[a-z0-9_]+$/',
            'name' => 'required|string|max:128',
            'description' => 'nullable|string|max:500',
            'rollout' => 'nullable|integer|min:0|max:100',
            'roles' => 'nullable|array',
            'users' => 'nullable|array',
        ]);

        $key = $request->input('key');

        if ($this->featureExists($key)) {
            return response()->json([
                'success' => false,
                'error' => 'Feature already exists',
            ], 422);
        }

        $this->saveFeature($key, [
            'name' => $request->input('name'),
            'description' => $request->input('description', ''),
            'rollout' => $request->input('rollout', 100),
            'roles' => $request->input('roles', []),
            'users' => $request->input('users', []),
            'enabled' => false,
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'data' => ['key' => $key],
            'message' => 'Feature toggle created',
        ], 201);
    }

    public function update(Request $request, string $feature)
    {
        if (! $this->featureExists($feature)) {
            return response()->json([
                'success' => false,
                'error' => 'Feature not found',
            ], 404);
        }

        $request->validate([
            'enabled' => 'nullable|boolean',
            'rollout' => 'nullable|integer|min:0|max:100',
            'roles' => 'nullable|array',
            'users' => 'nullable|array',
        ]);

        $data = $this->getFeatureData($feature);

        if ($request->has('enabled')) {
            $data['enabled'] = $request->boolean('enabled');
        }
        if ($request->has('rollout')) {
            $data['rollout'] = $request->integer('rollout');
        }
        if ($request->has('roles')) {
            $data['roles'] = $request->input('roles');
        }
        if ($request->has('users')) {
            $data['users'] = $request->input('users');
        }

        $data['updated_at'] = now()->toIso8601String();

        $this->saveFeature($feature, $data);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Feature toggle updated',
        ]);
    }

    public function destroy(string $feature)
    {
        if (! $this->featureExists($feature)) {
            return response()->json([
                'success' => false,
                'error' => 'Feature not found',
            ], 404);
        }

        $this->deleteFeature($feature);

        return response()->json([
            'success' => true,
            'message' => 'Feature toggle deleted',
        ]);
    }

    public function toggle(Request $request, string $feature)
    {
        if (! $this->featureExists($feature)) {
            return response()->json([
                'success' => false,
                'error' => 'Feature not found',
            ], 404);
        }

        $currentState = $this->isEnabled($feature);
        $this->setEnabled($feature, ! $currentState);

        return response()->json([
            'success' => true,
            'data' => [
                'key' => $feature,
                'enabled' => ! $currentState,
            ],
            'message' => $currentState ? 'Feature disabled' : 'Feature enabled',
        ]);
    }

    public function isEnabled(string $feature): bool
    {
        $features = $this->getAllFeatures();

        if (! isset($features[$feature])) {
            return false;
        }

        $data = $this->getFeatureData($feature);

        return $data['enabled'] ?? false;
    }

    public function check(Request $request, string $feature)
    {
        $enabled = $this->isEnabledForUser($feature, $request->user());

        return response()->json([
            'success' => true,
            'feature' => $feature,
            'enabled' => $enabled,
        ]);
    }

    public function isEnabledForUser(string $feature, $user): bool
    {
        $data = $this->getFeatureData($feature);

        if (! $data['enabled'] ?? false) {
            return false;
        }

        if ($user) {
            if (! empty($data['users']) && in_array($user->id, $data['users'])) {
                return true;
            }

            if (! empty($data['roles']) && in_array($user->role_id, $data['roles'])) {
                return true;
            }
        }

        $rollout = $data['rollout'] ?? 100;

        if ($rollout >= 100) {
            return true;
        }

        if ($rollout <= 0) {
            return false;
        }

        if ($user) {
            $userHash = crc32($user->id.$feature);
            $userPercent = $userHash % 100;

            return $userPercent < $rollout;
        }

        return random_int(0, 100) < $rollout;
    }

    protected function getAllFeatures(): array
    {
        try {
            $cached = Cache::get($this->cachePrefix.'all');
            if ($cached) {
                return json_decode($cached, true);
            }
        } catch (\Exception $e) {
        }

        return config('features', []);
    }

    protected function featureExists(string $feature): bool
    {
        $features = $this->getAllFeatures();

        return isset($features[$feature]);
    }

    protected function getFeatureData(string $feature): array
    {
        $features = $this->getAllFeatures();

        return $features[$feature] ?? [];
    }

    protected function isFeatureEnabled(string $feature): bool
    {
        $data = $this->getFeatureData($feature);

        return $data['enabled'] ?? false;
    }

    protected function setEnabled(string $feature, bool $enabled): void
    {
        $data = $this->getFeatureData($feature);
        $data['enabled'] = $enabled;
        $data['updated_at'] = now()->toIso8601String();

        $this->saveFeature($feature, $data);
    }

    protected function saveFeature(string $feature, array $data): void
    {
        $features = $this->getAllFeatures();
        $features[$feature] = $data;

        config(['features' => $features]);

        try {
            Cache::put($this->cachePrefix.'all', json_encode($features), now()->addHours(24));
        } catch (\Exception $e) {
        }
    }

    protected function deleteFeature(string $feature): void
    {
        $features = $this->getAllFeatures();
        unset($features[$feature]);

        config(['features' => $features]);

        try {
            Cache::forget($this->cachePrefix.'all');
        } catch (\Exception $e) {
        }
    }

    protected function getRollout(string $feature): int
    {
        $data = $this->getFeatureData($feature);

        return $data['rollout'] ?? 100;
    }

    protected function getEnabledForRole(string $feature, string $role): bool
    {
        $data = $this->getFeatureData($feature);

        if (! $data['enabled'] ?? false) {
            return false;
        }

        if (! empty($data['roles']) && in_array($role, $data['roles'])) {
            return true;
        }

        return ($data['rollout'] ?? 100) >= 100;
    }

    protected function getEnabledRoles(string $feature): array
    {
        $data = $this->getFeatureData($feature);

        return $data['roles'] ?? [];
    }

    protected function getEnabledUsers(string $feature): array
    {
        $data = $this->getFeatureData($feature);

        return $data['users'] ?? [];
    }

    protected function getCreatedAt(string $feature): ?string
    {
        $data = $this->getFeatureData($feature);

        return $data['created_at'] ?? null;
    }

    protected function getUpdatedAt(string $feature): ?string
    {
        $data = $this->getFeatureData($feature);

        return $data['updated_at'] ?? null;
    }
}
