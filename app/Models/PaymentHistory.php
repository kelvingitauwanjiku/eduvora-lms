<?php

namespace App\Models;

use App\Models\Bootcamp\Bootcamp;
use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'course_id',
        'ebook_id',
        'bootcamp_id',
        'bundle_id',
        'coupon_id',
        'gateway_id',
        'order_number',
        'payment_type',
        'payment_method',
        'transaction_id',
        'session_id',
        'gateway_response',
        'subtotal',
        'tax_amount',
        'coupon_discount',
        'total_amount',
        'amount',
        'currency',
        'exchange_rate',
        'admin_revenue',
        'instructor_revenue',
        'affiliate_revenue',
        'status',
        'payment_date',
        'invoice_number',
        'invoice_url',
        'receipt_url',
        'notes',
        'metadata',
        'payout_status',
        'completed_at',
        'refunded_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'admin_revenue' => 'decimal:2',
        'instructor_revenue' => 'decimal:2',
        'affiliate_revenue' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'payment_date' => 'datetime',
        'completed_at' => 'datetime',
        'refunded_at' => 'datetime',
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

    public function ebook(): BelongsTo
    {
        return $this->belongsTo(Ebook::class);
    }

    public function bootcamp(): BelongsTo
    {
        return $this->belongsTo(Bootcamp::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'completed';
    }
}
