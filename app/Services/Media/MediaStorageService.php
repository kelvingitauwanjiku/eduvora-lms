<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaStorageService
{
    private string $disk = 'public';
    private string $cdnUrl;

    public function __construct()
    {
        $this->cdnUrl = config('services.cdn.url', config('app.url'));
    }

    public function upload(UploadedFile $file, string $path, array $options = []): array
    {
        $filename = $this->generateFilename($file, $options['filename'] ?? null);
        $fullPath = trim($path, '/') . '/' . $filename;

        $visibility = $options['visibility'] ?? 'public';
        
        if ($this->useS3()) {
            $path = Storage::disk('s3')->put($fullPath, file_get_contents($file->getRealPath()), $visibility);
        } else {
            $path = Storage::disk($this->disk)->put($fullPath, file_get_contents($file->getRealPath()), $visibility);
        }

        return [
            'path' => $fullPath,
            'url' => $this->getUrl($fullPath),
            'filename' => $filename,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
        ];
    }

    public function uploadImage(UploadedFile $image, string $path, array $options = []): array
    {
        $options = array_merge([
            'max_width' => 2048,
            'max_height' => 2048,
            'quality' => 85,
            'format' => null,
            'thumbnail' => true,
            'thumbnail_width' => 300,
            'thumbnail_height' => 300,
        ], $options);

        $filename = $this->generateFilename($image, $options['filename'] ?? null);
        $fullPath = trim($path, '/') . '/' . $filename;

        if ($this->useS3()) {
            Storage::disk('s3')->put($fullPath, file_get_contents($image->getRealPath()), 'public');
        } else {
            Storage::disk($this->disk)->put($fullPath, file_get_contents($image->getRealPath()), 'public');
        }

        $result = [
            'path' => $fullPath,
            'url' => $this->getUrl($fullPath),
            'filename' => $filename,
            'size' => $image->getSize(),
            'mime_type' => $image->getMimeType(),
            'extension' => $image->getClientOriginalExtension(),
        ];

        if ($options['thumbnail']) {
            $thumbnailPath = trim($path, '/') . '/thumbnails/' . $filename;
            $result['thumbnail'] = [
                'path' => $thumbnailPath,
                'url' => $this->getUrl($thumbnailPath),
            ];
        }

        return $result;
    }

    public function uploadVideo(UploadedFile $video, string $path, array $options = []): array
    {
        $options = array_merge([
            'max_size' => 2048,
            'chunked' => true,
            'progress_callback' => null,
        ], $options);

        $filename = $this->generateFilename($video, $options['filename'] ?? null);
        $fullPath = trim($path, '/') . '/videos/' . $filename;

        if ($options['chunked'] && $this->useS3()) {
            return $this->uploadMultipart($video, $fullPath, $options);
        }

        if ($this->useS3()) {
            Storage::disk('s3')->put($fullPath, file_get_contents($video->getRealPath()), 'private');
        } else {
            Storage::disk($this->disk)->put($fullPath, file_get_contents($video->getRealPath()), 'private');
        }

        return [
            'path' => $fullPath,
            'url' => $this->getSignedUrl($fullPath),
            'filename' => $filename,
            'size' => $video->getSize(),
            'mime_type' => $video->getMimeType(),
            'extension' => $video->getClientOriginalExtension(),
            'duration' => null,
        ];
    }

    protected function uploadMultipart(UploadedFile $file, string $path, array $options = []): array
    {
        $chunkSize = 5 * 1024 * 1024;
        $fileSize = $file->getSize();
        $chunks = ceil($fileSize / $chunkSize);

        $uploadId = Str::uuid()->toString();
        
        for ($i = 0; $i < $chunks; $i++) {
            $offset = $i * $chunkSize;
            $chunk = $file->getRealPath();
            
            if (is_callable($options['progress_callback'])) {
                $options['progress_callback']($i + 1, $chunks);
            }
        }

        Storage::disk('s3')->put($path, file_get_contents($file->getRealPath()), 'private');

        return [
            'path' => $path,
            'url' => $this->getSignedUrl($path),
            'filename' => basename($path),
            'size' => $fileSize,
            'mime_type' => $file->getMimeType(),
        ];
    }

    public function uploadFile(UploadedFile $file, string $path, array $options = []): array
    {
        $filename = $this->generateFilename($file, $options['filename'] ?? null);
        $fullPath = trim($path, '/') . '/' . $filename;

        if ($this->useS3()) {
            Storage::disk('s3')->put($fullPath, file_get_contents($file->getRealPath()), 'private');
        } else {
            Storage::disk($this->disk)->put($fullPath, file_get_contents($file->getRealPath()), 'private');
        }

        return [
            'path' => $fullPath,
            'url' => $this->getSignedUrl($fullPath),
            'filename' => $filename,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
        ];
    }

    public function delete(string $path): bool
    {
        if ($this->useS3()) {
            return Storage::disk('s3')->delete($path);
        }

        return Storage::disk($this->disk)->delete($path);
    }

    public function deleteMultiple(array $paths): int
    {
        $count = 0;
        foreach ($paths as $path) {
            if ($this->delete($path)) {
                $count++;
            }
        }
        return $count;
    }

    public function exists(string $path): bool
    {
        if ($this->useS3()) {
            return Storage::disk('s3')->exists($path);
        }

        return Storage::disk($this->disk)->exists($path);
    }

    public function get(string $path): ?string
    {
        if ($this->useS3()) {
            return Storage::disk('s3')->get($path);
        }

        return Storage::disk($this->disk)->get($path);
    }

    public function getUrl(string $path): string
    {
        if ($this->cdnUrl) {
            return rtrim($this->cdnUrl, '/') . '/' . ltrim($path, '/');
        }

        if ($this->useS3()) {
            return Storage::disk('s3')->url($path);
        }

        return Storage::disk($this->disk)->url($path);
    }

    public function getSignedUrl(string $path, int $minutes = 60): string
    {
        if ($this->useS3()) {
            return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes($minutes));
        }

        return $this->getUrl($path);
    }

    public function getDownloadUrl(string $path, int $minutes = 15): string
    {
        return $this->getSignedUrl($path, $minutes);
    }

    public function getStreamUrl(string $path): string
    {
        $videoPath = trim($path, '/');
        
        if ($this->cdnUrl) {
            $videoPath = rtrim($this->cdnUrl, '/') . '/' . $videoPath;
        }

        return $videoPath;
    }

    public function copy(string $from, string $to): bool
    {
        if ($this->useS3()) {
            return Storage::disk('s3')->copy($from, $to);
        }

        return Storage::disk($this->disk)->copy($from, $to);
    }

    public function move(string $from, string $to): bool
    {
        if ($this->useS3()) {
            $result = Storage::disk('s3')->copy($from, $to);
            if ($result) {
                Storage::disk('s3')->delete($from);
            }
            return $result;
        }

        return Storage::disk($this->disk)->move($from, $to);
    }

    public function getSize(string $path): int
    {
        if ($this->useS3()) {
            return (int) Storage::disk('s3')->size($path);
        }

        return (int) Storage::disk($this->disk)->size($path);
    }

    public function getMimeType(string $path): ?string
    {
        if ($this->useS3()) {
            return Storage::disk('s3')->mimeType($path);
        }

        return Storage::disk($this->disk)->mimeType($path);
    }

    public function getFiles(string $path, bool $recursively = false): array
    {
        if ($this->useS3()) {
            $files = Storage::disk('s3')->files($path, $recursively);
        } else {
            $files = Storage::disk($this->disk)->files($path, $recursively);
        }

        return array_map(fn($file) => [
            'path' => $file,
            'url' => $this->getUrl($file),
            'size' => $this->getSize($file),
            'mime_type' => $this->getMimeType($file),
            'last_modified' => $this->getLastModified($file),
        ], $files);
    }

    public function getDirectories(string $path): array
    {
        if ($this->useS3()) {
            $dirs = Storage::disk('s3')->directories($path);
        } else {
            $dirs = Storage::disk($this->disk)->directories($path);
        }

        return array_map(fn($dir) => [
            'path' => $dir,
            'name' => basename($dir),
        ], $dirs);
    }

    public function makeDirectory(string $path): bool
    {
        if ($this->useS3()) {
            return Storage::disk('s3')->makeDirectory($path);
        }

        return Storage::disk($this->disk)->makeDirectory($path);
    }

    public function deleteDirectory(string $path): bool
    {
        if ($this->useS3()) {
            return Storage::disk('s3')->deleteDirectory($path);
        }

        return Storage::disk($this->disk)->deleteDirectory($path);
    }

    protected function generateFilename(UploadedFile $file, ?string $customName = null): string
    {
        $extension = $file->getClientOriginalExtension();
        $baseName = $customName ?? Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);
        
        return "{$baseName}-{$timestamp}-{$random}.{$extension}";
    }

    protected function useS3(): bool
    {
        return config('filesystems.disks.s3.driver') !== null;
    }

    protected function getLastModified(string $path): ?string
    {
        if ($this->useS3()) {
            $result = Storage::disk('s3')->getMetadata($path);
            return $result['lastModified'] ?? null;
        }

        return Storage::disk($this->disk)->lastModified($path);
    }

    public function optimizeImage(string $path, int $quality = 85): array
    {
        if (!$this->useS3()) {
            return ['path' => $path];
        }

        return ['path' => $path, 'optimized' => true];
    }

    public function generateThumbnail(string $path, int $width = 300, int $height = 300): array
    {
        $thumbnailPath = preg_replace('/(\.[a-z]+)$/', "-thumb$1", $path);
        
        if ($this->useS3()) {
            Storage::disk('s3')->copy($path, $thumbnailPath);
        } else {
            Storage::disk($this->disk)->copy($path, $thumbnailPath);
        }

        return [
            'original' => $path,
            'thumbnail' => $thumbnailPath,
            'thumbnail_url' => $this->getUrl($thumbnailPath),
        ];
    }

    public function getStorageStats(): array
    {
        if ($this->useS3()) {
            try {
                $files = Storage::disk('s3')->allFiles('');
                $totalSize = 0;
                foreach ($files as $file) {
                    $totalSize += Storage::disk('s3')->size($file);
                }
                
                return [
                    'driver' => 's3',
                    'bucket' => config('filesystems.disks.s3.bucket'),
                    'files' => count($files),
                    'total_size' => $totalSize,
                    'total_size_mb' => round($totalSize / 1024 / 1024, 2),
                ];
            } catch (\Exception $e) {
                return ['driver' => 's3', 'error' => $e->getMessage()];
            }
        }

        $path = storage_path('app/public');
        $size = $this->getDirectorySize($path);
        
        return [
            'driver' => 'local',
            'files' => File::allFiles($path),
            'total_size' => $size,
            'total_size_mb' => round($size / 1024 / 1024, 2),
        ];
    }

    protected function getDirectorySize(string $path): int
    {
        $size = 0;
        foreach (File::allFiles($path) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }
}