<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AffiliateReferral extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'affiliate_referrals';

    protected $fillable = [
        'uuid',
        'affiliate_id',
        'referred_user_id',
        'referral_code',
        'ip_address',
        'user_agent',
        'source',
        'medium',
        'campaign',
        'registered_at',
        'has_purchased',
        'total_spent',
        'purchases_count',
    ];

    protected $casts = [
        'has_purchased' => 'boolean',
        'registered_at' => 'datetime',
        'total_spent' => 'decimal:2',
        'purchases_count' => 'integer',
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

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function scopeConverted($query)
    {
        return $query->where('has_purchased', true);
    }
}