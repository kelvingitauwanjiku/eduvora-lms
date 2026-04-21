<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseApprovalRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'user_id', 'course_id', 'message', 'checklist', 'attachments',
        'read_status', 'status', 'admin_notes', 'rejection_reason',
        'reviewed_by', 'reviewed_at', 'revision_count', 'submitted_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'checklist' => 'array',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];
}
