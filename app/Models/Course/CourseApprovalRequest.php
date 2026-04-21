<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseApprovalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'reviewer_id',
        'status',
        'submitted_at',
        'review_started_at',
        'review_completed_at',
        'feedback',
        'detailed_feedback',
        'rejection_reason',
        'requested_changes',
        'priority',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'review_started_at' => 'datetime',
        'review_completed_at' => 'datetime',
        'requested_changes' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}