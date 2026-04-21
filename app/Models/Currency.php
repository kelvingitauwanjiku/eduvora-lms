<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'symbol_position',
        'decimal_separator',
        'thousands_separator',
        'decimal_places',
        'exchange_rate',
        'min_amount',
        'max_amount',
        'is_default',
        'is_active',
        'payment_gateways',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'exchange_rate' => 'decimal:6',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'decimal_places' => 'integer',
        'payment_gateways' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function format(float $amount): string
    {
        $formatted = number_format(
            $amount,
            $this->decimal_places,
            $this->decimal_separator,
            $this->thousands_separator
        );

        return $this->symbol_position === 'before'
            ? $this->symbol.$formatted
            : $formatted.$this->symbol;
    }
}
