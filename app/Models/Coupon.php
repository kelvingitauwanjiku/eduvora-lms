<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_purchase_amount',
        'min_course_price',
        'usage_limit',
        'usage_count',
        'per_user_limit',
        'is_global',
        'course_ids',
        'category_ids',
        'user_ids',
        'first_purchase_only',
        'is_featured',
        'status',
        'valid_from',
        'valid_until',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'is_featured' => 'boolean',
        'first_purchase_only' => 'boolean',
        'discount_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'min_course_price' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'per_user_limit' => 'integer',
        'course_ids' => 'array',
        'category_ids' => 'array',
        'user_ids' => 'array',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'settings' => 'array',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getIsValidAttribute(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now();
        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }
        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if (! $this->is_valid) {
            return 0;
        }

        if ($this->min_purchase_amount && $amount < $this->min_purchase_amount) {
            return 0;
        }

        $discount = $this->discount_type === 'percentage'
            ? ($amount * $this->discount_value / 100)
            : $this->discount_value;

        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            return $this->max_discount_amount;
        }

        return min($discount, $amount);
    }
}
