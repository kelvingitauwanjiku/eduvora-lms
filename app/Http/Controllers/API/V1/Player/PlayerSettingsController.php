<?php

namespace App\Http\Controllers\Api\V1\Player;

use App\Http\Controllers\Controller;
use App\Models\UserPlayerSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerSettingsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $settings = UserPlayerSetting::firstOrCreate(
            ['user_id' => $request->user()->id],
            $this->getDefaultSettings()
        );

        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'autoplay' => 'nullable|boolean',
            'playback_speed' => 'nullable|numeric|min:0.5|max:2',
            'quality' => 'nullable|in:auto,360p,480p,720p,1080p,1440p,4k',
            'subtitles' => 'nullable|in:off,auto,en,es,fr,de,pt,it,ja,ko,zh',
            'subtitles_size' => 'nullable|integer|min:12|max:32',
            'volume' => 'nullable|integer|min:0|max:100',
            'muted' => 'nullable|boolean',
            'loop' => 'nullable|boolean',
            'show_transcript' => 'nullable|boolean',
            'show_chapters' => 'nullable|boolean',
            'keyboard_shortcuts' => 'nullable|boolean',
            'continue_watching' => 'nullable|boolean',
            'skip_intro' => 'nullable|integer|min:0|max:300',
            'skip_outro' => 'nullable|integer|min:0|max:300',
            'play_next' => 'nullable|boolean',
            'remember_position' => 'nullable|boolean',
            'mini_player' => 'nullable|boolean',
            'theater_mode' => 'nullable|boolean',
            'picture_in_picture' => 'nullable|boolean',
            'download_enabled' => 'nullable|boolean',
            'analytics_enabled' => 'nullable|boolean',
        ]);

        $settings = UserPlayerSetting::firstOrCreate(
            ['user_id' => $request->user()->id],
            $this->getDefaultSettings()
        );

        $settings->update($request->only([
            'autoplay', 'playback_speed', 'quality', 'subtitles', 'subtitles_size',
            'volume', 'muted', 'loop', 'show_transcript', 'show_chapters',
            'keyboard_shortcuts', 'continue_watching', 'skip_intro', 'skip_outro',
            'play_next', 'remember_position', 'mini_player', 'theater_mode',
            'picture_in_picture', 'download_enabled', 'analytics_enabled',
        ]));

        return response()->json($settings);
    }

    public function reset(Request $request): JsonResponse
    {
        $settings = UserPlayerSetting::where('user_id', $request->user()->id)->first();
        
        if ($settings) {
            $settings->delete();
        }

        UserPlayerSetting::create(array_merge(
            ['user_id' => $request->user()->id],
            $this->getDefaultSettings()
        ));

        return response()->json(['message' => 'Settings reset to defaults']);
    }

    public function export(Request $request): JsonResponse
    {
        $settings = UserPlayerSetting::where('user_id', $request->user()->id)->first();
        
        return response()->json([
            'version' => '1.0',
            'exported_at' => now(),
            'settings' => $settings ?? $this->getDefaultSettings(),
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.autoplay' => 'nullable|boolean',
            'settings.playback_speed' => 'nullable|numeric|min:0.5|max:2',
            'settings.quality' => 'nullable|string',
            'settings.volume' => 'nullable|integer|min:0|max:100',
        ]);

        $settings = UserPlayerSetting::updateOrCreate(
            ['user_id' => $request->user()->id],
            array_merge($this->getDefaultSettings(), $request->settings)
        );

        return response()->json([
            'message' => 'Settings imported',
            'settings' => $settings,
        ]);
    }

    public function forCourse(Request $request, int $courseId): JsonResponse
    {
        $userSettings = UserPlayerSetting::firstOrCreate(
            ['user_id' => $request->user()->id],
            $this->getDefaultSettings()
        );

        return response()->json([
            'settings' => $userSettings,
            'course_id' => $courseId,
        ]);
    }

    public function saveForCourse(Request $request, int $courseId): JsonResponse
    {
        $request->validate([
            'playback_speed' => 'nullable|numeric|min:0.5|max:2',
            'quality' => 'nullable|string',
            'last_position' => 'nullable|integer|min:0',
            'completed_lessons' => 'nullable|array',
        ]);

        $settings = UserPlayerSetting::firstOrCreate(
            ['user_id' => $request->user()->id, 'course_id' => $courseId],
            array_merge($this->getDefaultSettings(), [
                'course_id' => $courseId,
            ])
        );

        $settings->update($request->only([
            'playback_speed', 'quality', 'last_position', 'completed_lessons',
        ]));

        return response()->json($settings);
    }

    public function history(int $courseId, Request $request): JsonResponse
    {
        $settings = UserPlayerSetting::where('user_id', $request->user()->id)
            ->where('course_id', $courseId)
            ->get();

        return response()->json($settings);
    }

    public function stats(Request $request): JsonResponse
    {
        $settings = UserPlayerSetting::where('user_id', $request->user()->id)->first();

        return response()->json([
            'autoplay' => $settings->autoplay ?? true,
            'prefered_speed' => $settings->playback_speed ?? 1.0,
            'prefered_quality' => $settings->quality ?? 'auto',
            'volume' => $settings->volume ?? 80,
            'analytics' => $settings->analytics_enabled ?? true,
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'last_sync' => 'nullable|date',
            'settings' => 'required|array',
        ]);

        $clientSettings = $request->settings;
        $serverSettings = UserPlayerSetting::where('user_id', $request->user()->id)->first();

        $mergedSettings = array_merge(
            $this->getDefaultSettings(),
            $serverSettings ? $serverSettings->toArray() : [],
            $clientSettings
        );

        $settings = UserPlayerSetting::updateOrCreate(
            ['user_id' => $request->user()->id],
            $mergedSettings
        );

        return response()->json([
            'synced_at' => now(),
            'settings' => $settings,
        ]);
    }

    protected function getDefaultSettings(): array
    {
        return [
            'autoplay' => true,
            'playback_speed' => 1.0,
            'quality' => 'auto',
            'subtitles' => 'auto',
            'subtitles_size' => 16,
            'volume' => 80,
            'muted' => false,
            'loop' => false,
            'show_transcript' => false,
            'show_chapters' => true,
            'keyboard_shortcuts' => true,
            'continue_watching' => true,
            'skip_intro' => 0,
            'skip_outro' => 0,
            'play_next' => false,
            'remember_position' => true,
            'mini_player' => false,
            'theater_mode' => false,
            'picture_in_picture' => true,
            'download_enabled' => false,
            'analytics_enabled' => true,
        ];
    }
}