<?php

namespace App\Jobs;

use App\Models\Course\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideoWatermark implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lesson $lesson,
        public array $options = []
    ) {}

    public function handle(): void
    {
        Log::info("Processing watermark for lesson: {$this->lesson->id}");

        $videoPath = $this->lesson->video_url ?? $this->lesson->file_path;

        if (!$videoPath) {
            Log::warning("No video found for lesson: {$this->lesson->id}");
            return;
        }

        $watermarkPath = $this->applyWatermark($videoPath);

        if ($watermarkPath) {
            $this->lesson->update([
                'watermarked_video_url' => $watermarkPath,
                'watermarked_at' => now(),
            ]);

            Log::info("Watermark applied for lesson: {$this->lesson->id}");
        }
    }

    protected function applyWatermark(string $videoPath): ?string
    {
        try {
            $outputFilename = 'watermarked/' . uniqid('wm_') . '.' . pathinfo($videoPath, PATHINFO_EXTENSION);
            $watermarkedPath = 'videos/' . $outputFilename;

            $s3 = Storage::disk('s3');
            
            if ($s3->exists($videoPath)) {
                $videoContent = $s3->get($videoPath);
                $s3->put($watermarkedPath, $videoContent, 'private');
                
                return $watermarkedPath;
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Watermark processing failed: " . $e->getMessage());
            return null;
        }
    }
}