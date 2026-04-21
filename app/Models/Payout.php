<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payout extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'payment_method',
        'bank_details',
        'transaction_id',
        'status',
        'notes',
        'admin_notes',
        'rejection_reason',
        'approved_by',
        'rejected_by',
        'processed_by',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bank_details' => 'array',
        'processed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'completed';
    }
}
