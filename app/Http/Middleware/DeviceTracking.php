<?php

namespace App\Http\Middleware;

use App\Models\UserDevice;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DeviceTracking
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $next($request);
        }

        $deviceId = $this->getDeviceId($request);
        $ipAddress = $this->getIpAddress($request);
        
        $device = $this->trackDevice($request->user(), [
            'device_id' => $deviceId,
            'ip_address' => $ipAddress,
            'user_agent' => $request->userAgent(),
            'platform' => $this->getPlatform($request),
            'browser' => $this->getBrowser($request),
            'device_type' => $this->getDeviceType($request),
            'location' => $this->getLocation($request),
        ]);

        $this->checkSecurity($request, $device);

        return $next($request);
    }

    protected function getDeviceId(Request $request): string
    {
        $deviceId = $request->header('X-Device-ID');
        
        if (!$deviceId) {
            $deviceId = $request->cookie('device_id');
        }
        
        if (!$deviceId) {
            $deviceId = sprintf(
                '%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffffffff),
                mt_rand(0, 0xffffffff)
            );
        }

        return $deviceId;
    }

    protected function getIpAddress(Request $request): string
    {
        $ip = $request->ip();
        
        if ($request->header('X-Forwarded-For')) {
            $ips = explode(',', $request->header('X-Forwarded-For'));
            $ip = trim($ips[0]);
        } elseif ($request->header('X-Real-IP')) {
            $ip = $request->header('X-Real-IP');
        }

        return $ip;
    }

    protected function getPlatform(Request $request): string
    {
        $userAgent = $request->userAgent();
        
        if (stripos($userAgent, 'Windows') !== false) {
            return 'Windows';
        } elseif (stripos($userAgent, 'Mac') !== false) {
            return 'macOS';
        } elseif (stripos($userAgent, 'Linux') !== false) {
            return 'Linux';
        } elseif (stripos($userAgent, 'Android') !== false) {
            return 'Android';
        } elseif (stripos($userAgent, 'iOS') !== false || stripos($userAgent, 'iPhone') !== false) {
            return 'iOS';
        }

        return 'Unknown';
    }

    protected function getBrowser(Request $request): string
    {
        $userAgent = $request->userAgent();
        
        if (stripos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (stripos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (stripos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (stripos($userAgent, 'Edge') !== false) {
            return 'Edge';
        } elseif (stripos($userAgent, 'Opera') !== false || stripos($userAgent, 'OPR') !== false) {
            return 'Opera';
        }

        return 'Unknown';
    }

    protected function getDeviceType(Request $request): string
    {
        $userAgent = $request->userAgent();
        
        if (preg_match('/mobile|android/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    protected function getLocation(Request $request): ?array
    {
        $ip = $this->getIpAddress($request);
        
        try {
            $response = \Illuminate\Support\Facades\Http::get("http://ip-api.com/json/{$ip}");
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'country' => $data['country'] ?? null,
                    'country_code' => $data['countryCode'] ?? null,
                    'region' => $data['regionName'] ?? null,
                    'city' => $data['city'] ?? null,
                    'isp' => $data['isp'] ?? null,
                    'org' => $data['org'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            Log::debug("Location lookup failed: " . $e->getMessage());
        }

        return null;
    }

    protected function trackDevice($user, array $data): UserDevice
    {
        $device = UserDevice::where('user_id', $user->id)
            ->where('device_id', $data['device_id'])
            ->first();

        if ($device) {
            $device->update([
                'last_active_at' => now(),
                'ip_address' => $data['ip_address'],
                'user_agent' => $data['user_agent'],
                'is_current' => true,
            ]);

            UserDevice::where('user_id', $user->id)
                ->where('id', '!=', $device->id)
                ->update(['is_current' => false]);

            return $device;
        }

        return UserDevice::create([
            'user_id' => $user->id,
            'device_id' => $data['device_id'],
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'],
            'platform' => $data['platform'],
            'browser' => $data['browser'],
            'device_type' => $data['device_type'],
            'location' => $data['location'],
            'is_current' => true,
            'last_active_at' => now(),
        ]);
    }

    protected function checkSecurity(Request $request, UserDevice $device): void
    {
        $suspiciousThreshold = config('security.max_devices', 3);
        
        $currentDevices = UserDevice::where('user_id', $request->user()->id)
            ->where('last_active_at', '>=', now()->subDays(30))
            ->count();

        if ($currentDevices > $suspiciousThreshold) {
            Log::warning("Multiple devices detected for user {$request->user()->id}: {$currentDevices} devices");
            
            event(new \App\Events\SuspiciousActivity($request->user(), [
                'type' => 'multiple_devices',
                'device_id' => $device->device_id,
                'device_count' => $currentDevices,
                'ip' => $request->ip(),
            ]));
        }

        if ($device->is_banned) {
            Log::warning("Banned device attempted access: {$device->device_id} by user {$request->user()->id}");
            abort(403, 'Your device has been blocked');
        }

        $newIp = $device->ip_address !== $request->ip();
        $allowedIps = config('security.allowed_ips', []);
        
        if ($newIp && !empty($allowedIps) && !in_array($request->ip(), $allowedIps)) {
            $device->update(['ip_address' => $request->ip()]);
        }
    }
}