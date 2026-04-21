<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGateway extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'model_name',
        'currency',
        'keys',
        'settings',
        'transaction_fee_percentage',
        'fixed_fee',
        'is_active',
        'is_default',
        'test_mode',
        'is_addon',
        'webhook_url',
        'redirect_url',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'test_mode' => 'boolean',
        'is_addon' => 'boolean',
        'transaction_fee_percentage' => 'decimal:2',
        'fixed_fee' => 'decimal:2',
        'keys' => 'array',
        'settings' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function calculateFee(float $amount): array
    {
        $percentageFee = $amount * ($this->transaction_fee_percentage / 100);
        $totalFee = $percentageFee + $this->fixed_fee;

        return [
            'fee' => round($totalFee, 2),
            'percentage_fee' => round($percentageFee, 2),
            'fixed_fee' => $this->fixed_fee,
            'net_amount' => round($amount - $totalFee, 2),
        ];
    }
}
