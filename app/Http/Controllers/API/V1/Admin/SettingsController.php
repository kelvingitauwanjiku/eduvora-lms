<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\FrontendSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $settings = Setting::all()->groupBy('group');

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function show(Request $request, string $group)
    {
        $settings = Setting::where('group', $group)->get();

        return response()->json([
            'success' => true,
            'data' => $settings,
            'group' => $group,
        ]);
    }

    public function update(Request $request)
    {
        $settings = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
        ]);

        DB::transaction(function () use ($settings) {
            foreach ($settings['settings'] as $setting) {
                Setting::updateOrCreate(
                    ['key' => $setting['key']],
                    [
                        'value' => $setting['value'],
                        'group' => $setting['group'] ?? 'general',
                    ]
                );
            }
        });

        Cache::forget('settings');

        return response()->json([
            'success' => true,
            'message' => 'Settings updated',
        ]);
    }

    public function updateKey(Request $request, string $key)
    {
        $request->validate([
            'value' => 'required',
            'group' => 'nullable|string',
        ]);

        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $request->input('value'),
                'group' => $request->input('group', 'general'),
            ]
        );

        Cache::forget('settings');

        return response()->json([
            'success' => true,
            'message' => 'Setting updated',
        ]);
    }

    public function destroy(Request $request, string $key)
    {
        Setting::where('key', $key)->delete();

        Cache::forget('settings');

        return response()->json([
            'success' => true,
            'message' => 'Setting deleted',
        ]);
    }

    public function frontend(Request $request)
    {
        $settings = FrontendSetting::all();

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function updateFrontend(Request $request)
    {
        $settings = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
        ]);

        DB::transaction(function () use ($settings) {
            foreach ($settings['settings'] as $setting) {
                FrontendSetting::updateOrCreate(
                    ['key' => $setting['key']],
                    ['value' => $setting['value']]
                );
            }
        });

        Cache::forget('frontend_settings');

        return response()->json([
            'success' => true,
            'message' => 'Frontend settings updated',
        ]);
    }

    public function export(Request $request)
    {
        $settings = Setting::all();
        $frontend = FrontendSetting::all();

        return response()->json([
            'success' => true,
            'data' => [
                'settings' => $settings,
                'frontend' => $frontend,
                'exported_at' => now()->toIso8601String(),
            ],
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'frontend' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->input('settings', []) as $setting) {
                Setting::updateOrCreate(
                    ['key' => $setting['key'], 'group' => $setting['group'] ?? 'general'],
                    ['value' => $setting['value']]
                );
            }

            foreach ($request->input('frontend', []) as $setting) {
                FrontendSetting::updateOrCreate(
                    ['key' => $setting['key']],
                    ['value' => $setting['value']]
                );
            }
        });

        Cache::forget('settings');
        Cache::forget('frontend_settings');

        return response()->json([
            'success' => true,
            'message' => 'Settings imported',
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'group' => 'nullable|string',
        ]);

        $query = Setting::query();
        
        if ($request->has('group')) {
            $query->where('group', $request->input('group'));
        }

        $query->delete();

        Cache::forget('settings');

        return response()->json([
            'success' => true,
            'message' => 'Settings reset',
        ]);
    }
}