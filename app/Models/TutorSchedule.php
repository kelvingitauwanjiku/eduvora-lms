<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TutorSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'tutor_id',
        'category_id',
        'subject_id',
        'title',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration',
        'session_type',
        'max_students',
        'booked_slots',
        'price_per_session',
        'group_price',
        'tution_type',
        'description',
        'is_active',
        'is_recurring',
        'effective_from',
        'effective_until',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_recurring' => 'boolean',
        'slot_duration' => 'integer',
        'max_students' => 'integer',
        'booked_slots' => 'integer',
        'price_per_session' => 'decimal:2',
        'group_price' => 'decimal:2',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
        'metadata' => 'array',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(TutorCategory::class, 'category_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(TutorSubject::class, 'subject_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', now());
            });
    }
}