<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonView extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'views_count',
        'total_watch_time',
        'last_watched_at',
        'is_completed',
        'completed_at',
        'progress_percentage',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'progress_percentage' => 'decimal:2',
        'last_watched_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
