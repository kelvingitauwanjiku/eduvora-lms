<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EbookPurchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ebook_purchases';

    protected $fillable = [
        'uuid',
        'user_id',
        'ebook_id',
        'order_number',
        'session_id',
        'transaction_id',
        'invoice',
        'invoice_url',
        'price',
        'tax',
        'coupon_discount',
        'total_amount',
        'currency',
        'payment_type',
        'status',
        'admin_revenue',
        'instructor_revenue',
        'payment_date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'tax' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'admin_revenue' => 'decimal:2',
        'instructor_revenue' => 'decimal:2',
        'payment_date' => 'datetime',
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

    public function ebook(): BelongsTo
    {
        return $this->belongsTo(Ebook::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }
}