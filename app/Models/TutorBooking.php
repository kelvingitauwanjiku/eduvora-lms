<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TutorBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'tutor_id',
        'student_id',
        'schedule_id',
        'category_id',
        'subject_id',
        'invoice',
        'title',
        'session_date',
        'start_time',
        'end_time',
        'duration',
        'session_type',
        'teaching_mode',
        'meeting_link',
        'meeting_id',
        'meeting_password',
        'joining_data',
        'price',
        'tax',
        'total_amount',
        'admin_revenue',
        'tutor_revenue',
        'payment_method',
        'transaction_id',
        'payment_details',
        'student_notes',
        'tutor_notes',
        'cancellation_reason',
        'status',
        'is_paid',
        'is_rated',
        'rating',
        'review',
        'started_at',
        'ended_at',
        'actual_duration',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'admin_revenue' => 'decimal:2',
        'tutor_revenue' => 'decimal:2',
        'duration' => 'integer',
        'is_paid' => 'boolean',
        'is_rated' => 'boolean',
        'session_date' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'actual_duration' => 'integer',
        'joining_data' => 'array',
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

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(TutorSchedule::class, 'schedule_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TutorCategory::class, 'category_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(TutorSubject::class, 'subject_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}