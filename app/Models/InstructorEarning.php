<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstructorEarning extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'course_id',
        'bootcamp_id',
        'ebook_id',
        'order_id',
        'order_number',
        'gross_amount',
        'admin_commission',
        'instructor_amount',
        'tax_amount',
        'refunded_amount',
        'net_amount',
        'pending_amount',
        'paid_amount',
        'currency',
        'status',
        'available_at',
        'payout_at',
        'payout_id',
        'metadata',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'admin_commission' => 'decimal:2',
        'instructor_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'pending_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'available_at' => 'datetime',
        'payout_at' => 'datetime',
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

    public function bootcamp(): BelongsTo
    {
        return $this->belongsTo(Bootcamp::class);
    }

    public function ebook(): BelongsTo
    {
        return $this->belongsTo(Ebook::class);
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'payout_completed';
    }
}
