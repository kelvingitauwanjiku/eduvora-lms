<?php

namespace App\Services\Media;

use App\Models\Course\Lesson;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class WatermarkService
{
    private string $watermarkText;

    private float $opacity = 0.3;

    private string $fontPath;

    private int $fontSize = 24;

    private string $fontColor = '#ffffff';

    public function __construct()
    {
        $this->watermarkText = config('app.name', 'Eduvora');
        $this->fontPath = public_path('fonts/Roboto-Regular.ttf');
    }

    public function createVideoWatermark(string $videoPath, array $options = []): ?string
    {
        $text = $options['text'] ?? $this->watermarkText;
        $position = $options['position'] ?? 'center';
        $opacity = $options['opacity'] ?? $this->opacity;
        $fontSize = $options['font_size'] ?? $this->fontSize;
        $color = $options['color'] ?? $this->fontColor;

        $extension = pathinfo($videoPath, PATHINFO_EXTENSION);
        $outputFilename = 'watermarked/'.Str::uuid().'.'.$extension;
        $watermarkedPath = 'videos/'.$outputFilename;

        if ($this->isFfmpegAvailable()) {
            return $this->addWatermarkWithFfmpeg($videoPath, $watermarkedPath, $text, $position, $opacity, $fontSize, $color);
        }

        return $this->addWatermarkSimple($videoPath, $watermarkedPath, $text);
    }

    protected function isFfmpegAvailable(): bool
    {
        try {
            $process = new Process(['ffmpeg', '-version']);
            $process->run();

            return $process->isSuccessful();
        } catch (\Exception $e) {
            Log::warning('FFmpeg not available: '.$e->getMessage());

            return false;
        }
    }

    protected function addWatermarkWithFfmpeg(
        string $inputPath,
        string $outputPath,
        string $text,
        string $position,
        float $opacity,
        int $fontSize,
        string $color
    ): ?string {
        $rgb = $this->hexToRgb($color);

        $positionFilter = match ($position) {
            'top-left' => '10:10',
            'top-right' => 'W-tw-10:10',
            'bottom-left' => '10:H-th-10',
            'bottom-right' => 'W-tw-10:H-th-10',
            default => 'W/2-tw/2:H/2-th/2',
        };

        $filter = "drawtext=text='{$text}':fontsize={$fontSize}:fontcolor={$rgb}:x={$positionFilter}:alpha={$opacity}";

        try {
            $process = new Process([
                'ffmpeg',
                '-i', $inputPath,
                '-vf', $filter,
                '-c:a', 'copy',
                '-y',
                $outputPath,
            ]);
            $process->run();

            if ($process->isSuccessful()) {
                return $outputPath;
            }

            Log::error('FFmpeg error: '.$process->getErrorOutput());

            return null;
        } catch (\Exception $e) {
            Log::error('Watermark error: '.$e->getMessage());

            return null;
        }
    }

    protected function addWatermarkSimple(string $inputPath, string $outputPath, string $text): ?string
    {
        $s3 = Storage::disk('s3');

        if (! $s3->exists($inputPath)) {
            Log::error("Video not found: {$inputPath}");

            return null;
        }

        $videoContent = $s3->get($inputPath);

        $s3->put($outputPath, $videoContent, [
            'visibility' => 'private',
            'Metadata' => [
                'watermark' => $text,
                'original' => $inputPath,
            ],
        ]);

        return $outputPath;
    }

    public function removeWatermark(string $videoPath): bool
    {
        try {
            Storage::disk('s3')->delete($videoPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Remove watermark error: '.$e->getMessage());

            return false;
        }
    }

    public function processLessonWatermark(Lesson $lesson): ?string
    {
        $videoPath = $lesson->video_url ?? $lesson->file_path;

        if (! $videoPath) {
            return null;
        }

        $watermarkedPath = $this->createVideoWatermark($videoPath, [
            'text' => $this->watermarkText,
            'position' => 'center',
            'opacity' => $this->opacity,
        ]);

        if ($watermarkedPath) {
            $lesson->update([
                'watermarked_video_url' => $watermarkedPath,
                'watermarked_at' => now(),
            ]);
        }

        return $watermarkedPath;
    }

    public function batchProcess(array $lessonIds): array
    {
        $results = [];

        foreach ($lessonIds as $lessonId) {
            try {
                $lesson = Lesson::findOrFail($lessonId);

                if ($lesson->video_url) {
                    $watermarkedPath = $this->processLessonWatermark($lesson);

                    $results[] = [
                        'lesson_id' => $lessonId,
                        'status' => $watermarkedPath ? 'success' : 'error',
                    ];
                } else {
                    $results[] = [
                        'lesson_id' => $lessonId,
                        'status' => 'skipped',
                        'reason' => 'no video',
                    ];
                }
            } catch (\Exception $e) {
                $results[] = [
                    'lesson_id' => $lessonId,
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    protected function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return sprintf('%d,%d,%d', $r, $g, $b);
    }

    public function setText(string $text): self
    {
        $this->watermarkText = $text;

        return $this;
    }

    public function setOpacity(float $opacity): self
    {
        $this->opacity = $opacity;

        return $this;
    }
}
