<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'plan_id',
        'subscription_number',
        'billing_cycle',
        'price',
        'setup_fee',
        'tax_amount',
        'total_amount',
        'coupon_discount',
        'coupon_code',
        'currency',
        'status',
        'is_trial',
        'trial_days',
        'trial_ends_at',
        'starts_at',
        'current_period_start',
        'current_period_end',
        'cancels_at',
        'cancelled_at',
        'auto_renew',
        'payment_method',
        'transaction_id',
        'gateway_subscription_id',
        'metadata',
    ];

    protected $casts = [
        'is_trial' => 'boolean',
        'auto_renew' => 'boolean',
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancels_at' => 'datetime',
        'cancelled_at' => 'datetime',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeTrial($query)
    {
        return $query->where('is_trial', true);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->current_period_end && now()->gt($this->current_period_end);
    }
}