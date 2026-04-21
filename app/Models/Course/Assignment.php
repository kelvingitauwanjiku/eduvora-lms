<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'course_id',
        'section_id',
        'lesson_id',
        'title',
        'description',
        'instructions',
        'questions',
        'attachments',
        'question_file',
        'total_marks',
        'pass_marks',
        'time_limit',
        'attempts_allowed',
        'available_from',
        'available_until',
        'deadline',
        'allow_late_submission',
        'late_penalty_percentage',
        'show_correct_answers',
        'is_active',
        'is_published',
        'is_required',
        'resubmission_allowed',
        'max_resubmissions',
        'submissions_count',
        'average_score',
        'completion_rate',
        'settings',
        'metadata',
    ];

    protected $casts = [
        'allow_late_submission' => 'boolean',
        'show_correct_answers' => 'boolean',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'is_required' => 'boolean',
        'resubmission_allowed' => 'boolean',
        'attachments' => 'array',
        'total_marks' => 'integer',
        'pass_marks' => 'integer',
        'time_limit' => 'integer',
        'attempts_allowed' => 'integer',
        'max_resubmissions' => 'integer',
        'late_penalty_percentage' => 'integer',
        'average_score' => 'decimal:2',
        'completion_rate' => 'decimal:2',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'deadline' => 'datetime',
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

    public function submissions(): HasMany
    {
        return $this->hasMany(SubmittedAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getIsOpenAttribute(): bool
    {
        $now = now();
        $isAfterAvailable = ! $this->available_from || $now->gte($this->available_from);
        $isBeforeDeadline = ! $this->available_until || $now->lte($this->available_until);

        return $isAfterAvailable && $isBeforeDeadline;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline && now()->gt($this->deadline);
    }
}
