<?php

namespace App\Models\Course;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'quiz_id',
        'user_id',
        'course_id',
        'enrollment_id',
        'answers',
        'correct_answers',
        'wrong_answers',
        'skipped_answers',
        'correct_count',
        'wrong_count',
        'skipped_count',
        'total_marks',
        'obtained_marks',
        'score_percentage',
        'time_taken',
        'status',
        'result',
        'is_graded',
        'attempt_number',
        'started_at',
        'submitted_at',
        'graded_at',
        'metadata',
    ];

    protected $casts = [
        'is_graded' => 'boolean',
        'answers' => 'array',
        'correct_answers' => 'array',
        'wrong_answers' => 'array',
        'skipped_answers' => 'array',
        'score_percentage' => 'decimal:2',
        'time_taken' => 'decimal:2',
        'metadata' => 'array',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePassed($query)
    {
        return $query->where('result', 'passed');
    }

    public function getIsPassedAttribute(): bool
    {
        return $this->result === 'passed';
    }
}
