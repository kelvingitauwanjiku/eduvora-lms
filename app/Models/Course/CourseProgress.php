<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'quiz_id',
        'assignment_id',
        'item_type',
        'status',
        'progress_percentage',
        'time_spent',
        'attempts',
        'best_score',
        'is_completed',
        'started_at',
        'completed_at',
        'data',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'progress_percentage' => 'integer',
        'attempts' => 'integer',
        'best_score' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
