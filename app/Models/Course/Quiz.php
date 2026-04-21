<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'course_id',
        'section_id',
        'lesson_id',
        'title',
        'description',
        'summary',
        'duration',
        'time_limit',
        'total_marks',
        'pass_marks',
        'attempts_allowed',
        'questions_count',
        'random_questions',
        'show_correct_answers',
        'show_score',
        'allow_review',
        'shuffle_questions',
        'shuffle_answers',
        'quiz_type',
        'is_required',
        'is_active',
        'is_published',
        'sort_order',
        'average_score',
        'total_attempts',
        'completion_rate',
        'settings',
        'metadata',
    ];

    protected $casts = [
        'show_correct_answers' => 'boolean',
        'show_score' => 'boolean',
        'allow_review' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'duration' => 'integer',
        'time_limit' => 'integer',
        'total_marks' => 'integer',
        'pass_marks' => 'integer',
        'attempts_allowed' => 'integer',
        'questions_count' => 'integer',
        'random_questions' => 'integer',
        'total_attempts' => 'integer',
        'average_score' => 'decimal:2',
        'completion_rate' => 'decimal:2',
        'settings' => 'array',
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

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(QuizSubmission::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getPassPercentageAttribute(): float
    {
        if (! $this->total_marks) {
            return 0;
        }

        return ($this->pass_marks / $this->total_marks) * 100;
    }

    public function getDurationTextAttribute(): string
    {
        if (! $this->duration) {
            return 'No limit';
        }

        return $this->duration.' min';
    }
}
