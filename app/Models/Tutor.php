<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tutor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'title',
        'tagline',
        'description',
        'profile_image',
        'video_intro',
        'teaching_experience',
        'teaching_methodology',
        'certifications',
        'hourly_rate',
        'rating',
        'reviews_count',
        'sessions_count',
        'students_count',
        'total_hours_taught',
        'languages',
        'timezone',
        'teaching_mode',
        'available_days',
        'is_verified',
        'is_featured',
        'is_available',
        'is_active',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'teaching_experience' => 'array',
        'teaching_methodology' => 'array',
        'certifications' => 'array',
        'available_days' => 'array',
        'rating' => 'decimal:2',
        'reviews_count' => 'integer',
        'sessions_count' => 'integer',
        'students_count' => 'integer',
        'total_hours_taught' => 'integer',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(TutorCategory::class, 'tutor_can_teach')
            ->withPivot(['description', 'thumbnail', 'price', 'currency', 'is_active'])
            ->withTimestamps();
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(TutorSubject::class, 'tutor_can_teach')
            ->withPivot(['description', 'thumbnail', 'price', 'currency', 'is_active'])
            ->withTimestamps();
    }

    public function canTeach(): HasMany
    {
        return $this->hasMany(TutorCanTeach::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TutorSchedule::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TutorBooking::class, 'tutor_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TutorReview::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}