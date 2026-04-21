<?php

namespace App\Models;

use App\Models\User;
use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CourseRevision extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_revisions';

    protected $fillable = [
        'uuid',
        'course_id',
        'user_id',
        'approval_request_id',
        'changes',
        'previous_data',
        'new_data',
        'notes',
        'status',
        'version',
    ];

    protected $casts = [
        'changes' => 'array',
        'previous_data' => 'array',
        'new_data' => 'array',
        'version' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvalRequest(): BelongsTo
    {
        return $this->belongsTo(CourseApprovalRequest::class, 'approval_request_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}