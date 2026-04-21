<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'course_id',
        'title',
        'description',
        'sort_order',
        'lessons_count',
        'quizzes_count',
        'duration',
        'is_preview',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
        'is_active' => 'boolean',
        'duration' => 'integer',
        'lessons_count' => 'integer',
        'quizzes_count' => 'integer',
        'metadata' => 'array',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePreview($query)
    {
        return $query->where('is_preview', true);
    }

    public function getTotalDurationAttribute(): int
    {
        return $this->lessons()->sum('duration') ?? 0;
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->lessons_count > 0 &&
               $this->lessons()->where('is_completed', true)->count() === $this->lessons_count;
    }
}
