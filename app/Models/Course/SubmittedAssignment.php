<?php

namespace App\Models\Course;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubmittedAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'submitted_assignments';

    protected $fillable = [
        'uuid',
        'assignment_id',
        'user_id',
        'course_id',
        'enrollment_id',
        'answer',
        'attachments',
        'submission_file',
        'note',
        'submission_number',
        'marks',
        'percentage',
        'feedback',
        'instructor_remarks',
        'status',
        'result',
        'is_late',
        'late_penalty_applied',
        'graded_at',
        'graded_by',
        'notified',
        'metadata',
    ];

    protected $casts = [
        'is_late' => 'boolean',
        'notified' => 'boolean',
        'attachments' => 'array',
        'marks' => 'integer',
        'percentage' => 'integer',
        'late_penalty_applied' => 'integer',
        'graded_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
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

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function scopeGraded($query)
    {
        return $query->whereNotNull('marks');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    public function getIsPassedAttribute(): bool
    {
        return in_array($this->result, ['passed', 'excellent']);
    }
}
