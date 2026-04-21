<?php

namespace App\Models;

use App\Models\Bootcamp\Bootcamp;
use App\Models\Course\Course;
use App\Models\Ebook\Ebook;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'course_id',
        'ebook_id',
        'bootcamp_id',
        'bundle_id',
        'item_type',
        'price',
        'discount',
        'coupon_code',
        'coupon_discount',
        'is_applied_coupon',
        'quantity',
        'is_valid',
        'invalid_reason',
        'expires_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'is_valid' => 'boolean',
        'is_applied_coupon' => 'boolean',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'expires_at' => 'datetime',
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

    public function getTotalAttribute(): float
    {
        return max(0, $this->price - $this->discount - $this->coupon_discount);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }
}
