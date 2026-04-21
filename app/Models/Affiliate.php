<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Affiliate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'referral_code',
        'commission_rate',
        'minimum_payout',
        'total_earnings',
        'pending_earnings',
        'paid_earnings',
        'total_referrals',
        'active_referrals',
        'total_conversions',
        'conversion_rate',
        'average_order_value',
        'status',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'minimum_payout' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'pending_earnings' => 'decimal:2',
        'paid_earnings' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(AffiliateReferral::class, 'affiliate_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'affiliate_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getReferralLinkAttribute(): string
    {
        return url('/register?ref='.$this->referral_code);
    }

    public function getCanWithdrawAttribute(): bool
    {
        return $this->pending_earnings >= $this->minimum_payout;
    }
}
