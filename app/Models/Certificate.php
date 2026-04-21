<?php

namespace App\Models;

use App\Models\Course\Course;
use App\Models\Course\Enrollment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'course_id',
        'enrollment_id',
        'certificate_number',
        'title',
        'description',
        'content',
        'template',
        'qr_code',
        'pdf_url',
        'verify_url',
        'background_image',
        'logo',
        'signature',
        'grade',
        'grade_text',
        'issued_at',
        'valid_until',
        'is_verified',
        'is_shared',
        'views_count',
        'downloads_count',
        'metadata',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_shared' => 'boolean',
        'grade' => 'decimal:2',
        'template' => 'array',
        'issued_at' => 'datetime',
        'valid_until' => 'datetime',
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

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function getVerifyUrlAttribute(): ?string
    {
        return route('certificates.verify', $this->certificate_number);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until && now()->gt($this->valid_until);
    }

    public function getGradeTextAttribute(): string
    {
        if ($this->grade >= 90) {
            return 'A+';
        } elseif ($this->grade >= 80) {
            return 'A';
        } elseif ($this->grade >= 70) {
            return 'B';
        } elseif ($this->grade >= 60) {
            return 'C';
        } elseif ($this->grade >= 50) {
            return 'D';
        }

        return 'F';
    }
}
