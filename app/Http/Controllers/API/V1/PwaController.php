<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PwaController extends Controller
{
    public function manifest(Request $request)
    {
        $appName = config('app.name', 'Eduvora');
        $shortName = config('eduvora.short_name', 'Eduvora');
        $themeColor = config('eduvora.pwa.theme_color', '#1a73e8');
        $backgroundColor = config('eduvora.pwa.background_color', '#ffffff');

        $manifest = [
            'name' => $appName,
            'short_name' => $shortName,
            'description' => 'Eduvora - E-Learning Platform',
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => $backgroundColor,
            'theme_color' => $themeColor,
            'orientation' => 'portrait-primary',
            'icons' => [
                [
                    'src' => asset('/icons/icon-72x72.png'),
                    'sizes' => '72x72',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => asset('/icons/icon-96x96.png'),
                    'sizes' => '96x96',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => asset('/icons/icon-128x128.png'),
                    'sizes' => '128x128',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => asset('/icons/icon-144x144.png'),
                    'sizes' => '144x144',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => asset('/icons/icon-152x152.png'),
                    'sizes' => '152x152',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => asset('/icons/icon-192x192.png'),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => asset('/icons/icon-384x384.png'),
                    'sizes' => '384x384',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => asset('/icons/icon-512x512.png'),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
            'categories' => ['education', 'business'],
            'screenshots' => [],
            'shortcuts' => [
                [
                    'name' => 'Browse Courses',
                    'short_name' => 'Courses',
                    'url' => '/courses?source=pwa',
                    'icons' => [
                        ['src' => '/icons/courses.png', 'sizes' => '96x96']
                    ],
                ],
                [
                    'name' => 'My Learning',
                    'short_name' => 'Learning',
                    'url' => '/my-learning?source=pwa',
                    'icons' => [
                        ['src' => '/icons/learning.png', 'sizes' => '96x96']
                    ],
                ],
                [
                    'name' => 'Chat',
                    'short_name' => 'Chat',
                    'url' => '/chat?source=pwa',
                    'icons' => [
                        ['src' => '/icons/chat.png', 'sizes' => '96x96']
                    ],
                ],
            ],
            'related_applications' => [],
            'prefer_related_applications' => false,
        ];

        return response()->json($manifest)
            ->header('Cache-Control', 'public, max-age=86400')
            ->header('Content-Type', 'application/manifest+json');
    }

    public function serviceWorker(Request $request)
    {
        $version = config('eduvora.pwa.version', '1.0.0');
        $cacheName = 'eduvora-v' . $version;
        $staticFiles = [
            '/',
            '/offline',
            '/manifest.json',
            '/favicon.ico',
            '/icons/icon-192x192.png',
            '/icons/icon-512x512.png',
            '/css/app.css',
            '/js/app.js',
        ];

        $sw = <<<JS
        const CACHE_NAME = '{$cacheName}';
        const STATIC_ASSETS = {json_encode($staticFiles)};
        
        self.addEventListener('install', (event) => {
            event.waitUntil(
                caches.open(CACHE_NAME)
                    .then((cache) => {
                        return cache.addAll(STATIC_ASSETS);
                    })
                    .then(() => self.skipWaiting())
            );
        });

        self.addEventListener('activate', (event) => {
            event.waitUntil(
                caches.keys().then((cacheNames) => {
                    return Promise.all(
                        cacheNames
                            .filter((name) => name !== CACHE_NAME)
                            .map((name) => caches.delete(name))
                    );
                }).then(() => self.clients.claim())
            );
        });

        self.addEventListener('fetch', (event) => {
            if (event.request.method !== 'GET') return;
            
            const url = new URL(event.request.url);
            
            if (url.origin !== location.origin) return;
            
            if (event.request.mode === 'navigate') {
                event.respondWith(
                    fetch(event.request)
                        .catch(() => caches.match('/offline'))
                        .then((response) => {
                            return response || caches.match('/offline');
                        })
                );
                return;
            }
            
            event.respondWith(
                caches.match(event.request)
                    .then((cachedResponse) => {
                        if (cachedResponse) {
                            fetch(event.request).then((response) => {
                                caches.open(CACHE_NAME).then((cache) => {
                                    cache.put(event.request, response.clone());
                                });
                            });
                            return cachedResponse;
                        }
                        return fetch(event.request).then((response) => {
                            if (response.ok && response.status === 200) {
                                const responseClone = response.clone();
                                caches.open(CACHE_NAME).then((cache) => {
                                    cache.put(event.request, responseClone);
                                });
                            }
                            return response;
                        });
                    })
            );
        });

        self.addEventListener('push', (event) => {
            const data = event.data ? event.data.json() : {};
            const options = {
                body: data.body || 'New notification',
                icon: '/icons/icon-192x192.png',
                badge: '/icons/badge-72x72.png',
                vibrate: [100, 50, 100],
                data: {
                    url: data.url || '/',
                    timestamp: Date.now(),
                },
                actions: data.actions || [],
                renotify: true,
                tag: data.tag || 'default',
            };
            
            event.waitUntil(
                self.registration.showNotification(data.title || 'Eduvora', options)
            );
        });

        self.addEventListener('notificationclick', (event) => {
            event.notification.close();
            
            const url = event.notification.data.url || '/';
            
            event.waitUntil(
                clients.openWindow(url)
            );
        });

        self.addEventListener('message', (event) => {
            if (event.data && event.data.type === 'SKIP_WAITING') {
                self.skipWaiting();
            }
        });
        JS;

        return response($sw, 200, [
            'Content-Type' => 'application/javascript',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Service-Worker-Allowed' => '/',
        ]);
    }

    public function offline(Request $request)
    {
        return view('pwa.offline')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function updateAvailable(Request $request)
    {
        $request->validate([
            'version' => 'required|string',
        ]);

        $currentVersion = config('eduvora.pwa.version', '1.0.0');
        $newVersion = $request->input('version');

        $updateAvailable = version_compare($newVersion, $currentVersion) > 0;

        return response()->json([
            'update_available' => $updateAvailable,
            'current_version' => $currentVersion,
            'new_version' => $newVersion,
            'update_url' => route('pwa.update'),
        ]);
    }

    public function registerDevice(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'device_type' => 'required|string|in:android,ios,web',
            'player_id' => 'nullable|string',
        ]);

        $deviceData = [
            'user_id' => $request->user()->id ?? null,
            'device_token' => $request->input('device_token'),
            'device_type' => $request->input('device_type'),
            'player_id' => $request->input('player_id'),
            'enabled' => true,
            'last_active' => now(),
        ];

        $device = \App\Models\UserDevice::updateOrCreate(
            [
                'device_token' => $request->input('device_token'),
            ],
            $deviceData
        );

        return response()->json([
            'success' => true,
            'data' => $device,
            'message' => 'Device registered successfully',
        ]);
    }

    public function unregisterDevice(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
        ]);

        \App\Models\UserDevice::where('device_token', $request->input('device_token'))->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device unregistered successfully',
        ]);
    }

    public function pushSubscription(Request $request)
    {
        $request->validate([
            'subscription' => 'required|array',
            'subscription.endpoint' => 'required|url',
        ]);

        $user = $request->user();
        
        $subscription = $user->pushSubscriptions()->updateOrCreate(
            ['endpoint' => $request->input('subscription.endpoint')],
            [
                'keys' => $request->input('subscription.keys'),
                'expiration_time' => $request->input('subscription.expirationTime'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Push subscription saved',
        ]);
    }
}