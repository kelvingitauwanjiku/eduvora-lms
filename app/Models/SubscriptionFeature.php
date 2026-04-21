<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionFeature extends Model
{
    use HasFactory;

    protected $table = 'subscription_features';

    protected $fillable = [
        'plan_id',
        'name',
        'slug',
        'description',
        'icon',
        'value',
        'unit',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'value' => 'integer',
        'sort_order' => 'integer',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }
}