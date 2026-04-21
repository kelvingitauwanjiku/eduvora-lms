<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'course_id',
        'enrollment_id',
        'rating',
        'review',
        'title',
        'pros',
        'cons',
        'is_recommended',
        'is_anonymous',
        'is_verified_purchase',
        'is_featured',
        'is_published',
        'helpful_count',
        'report_count',
        'status',
        'admin_notes',
        'replied_by',
        'instructor_reply',
        'replied_at',
        'published_at',
    ];

    protected $casts = [
        'is_recommended' => 'boolean',
        'is_anonymous' => 'boolean',
        'is_verified_purchase' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'pros' => 'array',
        'cons' => 'array',
        'replied_at' => 'datetime',
        'published_at' => 'datetime',
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

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(ReviewReaction::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ReviewReport::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('status', 'approved');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getIsHelpfulAttribute(): bool
    {
        return $this->helpful_count > 0;
    }
}
