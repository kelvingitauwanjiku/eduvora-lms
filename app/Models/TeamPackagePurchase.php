<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TeamPackagePurchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'team_package_purchases';

    protected $fillable = [
        'uuid',
        'user_id',
        'package_id',
        'coupon_id',
        'order_number',
        'invoice',
        'invoice_url',
        'company_name',
        'company_address',
        'company_vat',
        'quantity',
        'price',
        'tax',
        'coupon_discount',
        'total_amount',
        'admin_revenue',
        'instructor_revenue',
        'payment_method',
        'transaction_id',
        'payment_details',
        'status',
        'subscription_start',
        'subscription_end',
        'auto_renew',
        'members_count',
        'max_members',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'tax' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'admin_revenue' => 'decimal:2',
        'instructor_revenue' => 'decimal:2',
        'auto_renew' => 'boolean',
        'members_count' => 'integer',
        'max_members' => 'integer',
        'subscription_start' => 'datetime',
        'subscription_end' => 'datetime',
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

    public function package(): BelongsTo
    {
        return $this->belongsTo(TeamTrainingPackage::class, 'package_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamPackageMember::class, 'package_purchase_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'completed');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->subscription_end && now()->gt($this->subscription_end);
    }
}