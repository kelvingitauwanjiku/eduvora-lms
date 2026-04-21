<?php

namespace App\Models\Course;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'course_id',
        'coupon_id',
        'coupon_usage_id',
        'enrollment_type',
        'original_price',
        'paid_amount',
        'discount_amount',
        'tax_amount',
        'admin_revenue',
        'instructor_revenue',
        'status',
        'start_date',
        'end_date',
        'expiry_date',
        'lifetime_access',
        'completion_percentage',
        'lessons_completed',
        'total_lessons',
        'quizzes_completed',
        'assignments_completed',
        'first_lesson_at',
        'last_activity_at',
        'completed_at',
        'total_time_spent',
        'certificate_id',
        'certificate_url',
        'invoice_id',
        'invoice_url',
        'progress_data',
        'completed_lessons',
        'completed_quizzes',
        'completed_assignments',
        'metadata',
    ];

    protected $casts = [
        'lifetime_access' => 'boolean',
        'original_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'admin_revenue' => 'decimal:2',
        'instructor_revenue' => 'decimal:2',
        'completion_percentage' => 'integer',
        'lessons_completed' => 'integer',
        'total_lessons' => 'integer',
        'quizzes_completed' => 'integer',
        'assignments_completed' => 'integer',
        'total_time_spent' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'expiry_date' => 'datetime',
        'first_lesson_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'completed_at' => 'datetime',
        'completed_lessons' => 'array',
        'completed_quizzes' => 'array',
        'completed_assignments' => 'array',
        'progress_data' => 'array',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class);
    }

    public function quizSubmissions(): HasMany
    {
        return $this->hasMany(QuizSubmission::class);
    }

    public function submittedAssignments(): HasMany
    {
        return $this->hasMany(SubmittedAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function getIsExpiredAttribute(): bool
    {
        if ($this->lifetime_access || ! $this->expiry_date) {
            return false;
        }

        return now()->gt($this->expiry_date);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed' || $this->completion_percentage >= 100;
    }

    public function getRemainingDaysAttribute(): ?int
    {
        if ($this->lifetime_access || ! $this->expiry_date) {
            return null;
        }

        return max(0, now()->diffInDays($this->expiry_date, false));
    }

    public function markLessonComplete(int $lessonId): void
    {
        $completed = $this->completed_lessons ?? [];
        if (! in_array($lessonId, $completed)) {
            $completed[] = $lessonId;
            $this->completed_lessons = $completed;
            $this->lessons_completed = count($completed);
            $this->updateProgress();
            $this->save();
        }
    }

    public function updateProgress(): void
    {
        if ($this->total_lessons > 0) {
            $this->completion_percentage = round(($this->lessons_completed / $this->total_lessons) * 100);
            if ($this->completion_percentage >= 100) {
                $this->status = 'completed';
                $this->completed_at = now();
            }
        }
    }
}
