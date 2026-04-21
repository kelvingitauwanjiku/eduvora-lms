<?php

namespace App\Http\Controllers\Api\V1\Media;

use App\Http\Controllers\Controller;
use App\Models\Course\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WatermarkController extends Controller
{
    private string $fontPath;
    private string $watermarkText;
    private float $opacity = 0.3;
    private int $quality = 85;

    public function __construct()
    {
        $this->fontPath = public_path('fonts/Roboto-Regular.ttf');
        $this->watermarkText = config('app.name', 'Eduvora');
    }

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'text' => 'nullable|string',
            'position' => 'nullable|in:center,top-left,top-right,bottom-left,bottom-right',
            'opacity' => 'nullable|numeric|min:0.1|max:1',
        ]);

        $lesson = Lesson::findOrFail($request->lesson_id);
        $videoPath = $lesson->video_url ?? $lesson->file_path;

        if (!$videoPath) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        $watermarkPath = $this->createWatermark($videoPath, [
            'text' => $request->text ?? $this->watermarkText,
            'position' => $request->position ?? 'center',
            'opacity' => $request->opacity ?? $this->opacity,
        ]);

        return response()->json([
            'original_video' => $videoPath,
            'watermarked_video' => $watermarkPath,
            'url' => Storage::disk('s3')->url($watermarkPath),
        ]);
    }

    public function processVideo(string $lessonId): JsonResponse
    {
        $lesson = Lesson::findOrFail($lessonId);
        
        if (!$lesson->video_url) {
            return response()->json(['error' => 'No video to watermark'], 400);
        }

        $watermarkPath = $this->createWatermark($lesson->video_url, [
            'text' => $this->watermarkText,
            'position' => 'center',
            'opacity' => $this->opacity,
        ]);

        $lesson->update([
            'watermarked_video_url' => $watermarkPath,
            'watermarked_at' => now(),
        ]);

        return response()->json([
            'lesson_id' => $lesson->id,
            'watermarked_video' => $watermarkPath,
            'processed_at' => $lesson->watermarked_at,
        ]);
    }

    public function addTextWatermark(Request $request): JsonResponse
    {
        $request->validate([
            'video_path' => 'required|string',
            'text' => 'required|string',
            'position' => 'nullable|in:center,top-left,top-right,bottom-left,bottom-right',
            'font_size' => 'nullable|integer|min:8|max:72',
            'color' => 'nullable|string',
            'opacity' => 'nullable|numeric|min:0.1|max:1',
        ]);

        $options = [
            'text' => $request->text,
            'position' => $request->position ?? 'center',
            'font_size' => $request->font_size ?? 24,
            'color' => $request->color ?? '#ffffff',
            'opacity' => $request->opacity ?? $this->opacity,
        ];

        $watermarkedPath = $this->addTextOverlay($request->video_path, $options);

        return response()->json([
            'path' => $watermarkedPath,
            'url' => $this->getSignedUrl($watermarkedPath),
        ]);
    }

    public function addImageWatermark(Request $request): JsonResponse
    {
        $request->validate([
            'video_path' => 'required|string',
            'watermark_image' => 'required|string',
            'position' => 'nullable|in:center,top-left,top-right,bottom-left,bottom-right',
            'opacity' => 'nullable|numeric|min:0.1|max:1',
            'scale' => 'nullable|numeric|min:0.1|max:1',
        ]);

        $options = [
            'position' => $request->position ?? 'center',
            'opacity' => $request->opacity ?? $this->opacity,
            'scale' => $request->scale ?? 0.3,
        ];

        $watermarkedPath = $this->addImageOverlay($request->video_path, $request->watermark_image, $options);

        return response()->json([
            'path' => $watermarkedPath,
            'url' => $this->getSignedUrl($watermarkedPath),
        ]);
    }

    public function batchProcess(Request $request): JsonResponse
    {
        $request->validate([
            'lesson_ids' => 'required|array',
            'lesson_ids.*' => 'exists:lessons,id',
        ]);

        $results = [];
        
        foreach ($request->lesson_ids as $lessonId) {
            try {
                $lesson = Lesson::findOrFail($lessonId);
                
                if ($lesson->video_url) {
                    $watermarkPath = $this->createWatermark($lesson->video_url, [
                        'text' => $this->watermarkText,
                    ]);

                    $lesson->update([
                        'watermarked_video_url' => $watermarkPath,
                        'watermarked_at' => now(),
                    ]);

                    $results[] = ['lesson_id' => $lessonId, 'status' => 'success'];
                } else {
                    $results[] = ['lesson_id' => $lessonId, 'status' => 'skipped', 'reason' => 'no video'];
                }
            } catch (\Exception $e) {
                $results[] = ['lesson_id' => $lessonId, 'status' => 'error', 'error' => $e->getMessage()];
            }
        }

        return response()->json([
            'total' => count($request->lesson_ids),
            'results' => $results,
        ]);
    }

    public function settings(Request $request): JsonResponse
    {
        if ($request->isMethod('GET')) {
            return response()->json([
                'enabled' => config('services.watermark.enabled', true),
                'text' => $this->watermarkText,
                'opacity' => $this->opacity,
                'positions' => ['center', 'top-left', 'top-right', 'bottom-left', 'bottom-right'],
                'enabled_formats' => ['mp4', 'webm', 'mov'],
            ]);
        }

        if ($request->isMethod('PUT')) {
            $this->watermarkText = $request->text ?? $this->watermarkText;
            $this->opacity = $request->opacity ?? $this->opacity;

            return response()->json([
                'message' => 'Watermark settings updated',
                'text' => $this->watermarkText,
                'opacity' => $this->opacity,
            ]);
        }
    }

    protected function createWatermark(string $videoPath, array $options = []): string
    {
        $outputFilename = 'watermarked/' . Str::uuid() . '.' . pathinfo($videoPath, PATHINFO_EXTENSION);
        $watermarkedPath = 'videos/' . $outputFilename;

        $s3 = Storage::disk('s3');
        
        if ($s3->exists($videoPath)) {
            $videoContent = $s3->get($videoPath);
            $s3->put($watermarkedPath, $text, 'private');
        }

        return $watermarkedPath;
    }

    protected function addTextOverlay(string $videoPath, array $options = []): string
    {
        return $this->createWatermark($videoPath, $options);
    }

    protected function addImageOverlay(string $videoPath, string $watermarkImage, array $options = []): string
    {
        return $this->createWatermark($videoPath, $options);
    }

    protected function getSignedUrl(string $path): string
    {
        return Storage::disk('s3')->temporaryUrl($path, now()->addHours(2));
    }

    public function removeWatermark(string $lessonId): JsonResponse
    {
        $lesson = Lesson::findOrFail($lessonId);
        
        if ($lesson->watermarked_video_url) {
            Storage::disk('s3')->delete($lesson->watermarked_video_url);
            
            $lesson->update([
                'watermarked_video_url' => null,
                'watermarked_at' => null,
            ]);
        }

        return response()->json(['message' => 'Watermark removed successfully']);
    }
}