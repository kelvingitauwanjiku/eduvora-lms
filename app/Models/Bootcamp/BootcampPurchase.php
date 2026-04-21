<?php

namespace App\Models\Bootcamp;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BootcampPurchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'bootcamp_id',
        'order_number',
        'invoice',
        'price',
        'tax',
        'total_amount',
        'coupon_discount',
        'admin_revenue',
        'instructor_revenue',
        'payment_method',
        'transaction_id',
        'payment_details',
        'status',
        'access_start',
        'access_end',
    ];

    protected $casts = [
        'access_start' => 'datetime',
        'access_end' => 'datetime',
        'price' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'admin_revenue' => 'decimal:2',
        'instructor_revenue' => 'decimal:2',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bootcamp()
    {
        return $this->belongsTo(Bootcamp::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
