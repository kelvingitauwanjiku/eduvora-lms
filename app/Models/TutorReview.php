<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TutorReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'tutor_id',
        'student_id',
        'booking_id',
        'rating',
        'review',
        'ratings_breakdown',
        'is_published',
        'is_featured',
        'helpful_count',
        'status',
        'tutor_reply',
        'replied_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'ratings_breakdown' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'helpful_count' => 'integer',
        'replied_at' => 'datetime',
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

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(TutorBooking::class, 'booking_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('status', 'approved');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}