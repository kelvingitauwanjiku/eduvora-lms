<?php

namespace App\Models\Course;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'course_id',
        'section_id',
        'parent_id',
        'title',
        'description',
        'content',
        'lesson_type',
        'video_source',
        'video_url',
        'video_provider',
        'video_id',
        'video_duration',
        'thumbnail',
        'preview_video',
        'is_preview',
        'is_free',
        'is_draft',
        'sort_order',
        'duration',
        'download_size',
        'attachment',
        'attachment_type',
        'attachment_name',
        'download_count',
        'video_properties',
        'subtitles',
        'chapters',
        'metadata',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
        'is_free' => 'boolean',
        'is_draft' => 'boolean',
        'duration' => 'integer',
        'video_duration' => 'integer',
        'download_count' => 'integer',
        'download_size' => 'integer',
        'video_properties' => 'array',
        'subtitles' => 'array',
        'chapters' => 'array',
        'metadata' => 'array',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Lesson::class, 'parent_id')->orderBy('sort_order');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(LessonView::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_draft', false);
    }

    public function scopeVideo($query)
    {
        return $query->where('lesson_type', 'video');
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopePreview($query)
    {
        return $query->where('is_preview', true);
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        return match ($this->video_provider) {
            'youtube' => "https://www.youtube.com/embed/{$this->video_id}",
            'vimeo' => "https://player.vimeo.com/video/{$this->video_id}",
            default => $this->video_url,
        };
    }

    public function getDurationTextAttribute(): string
    {
        if (! $this->duration) {
            return '0 min';
        }

        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes} min";
    }

    public function getIsCompletedByUserAttribute(): bool
    {
        return $this->views()->where('is_completed', true)->exists();
    }
}
