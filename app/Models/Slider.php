<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Slider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'type',
        'layout',
        'height',
        'auto_play',
        'auto_play_interval',
        'show_indicators',
        'show_arrows',
        'pause_on_hover',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'auto_play' => 'boolean',
        'show_indicators' => 'boolean',
        'show_arrows' => 'boolean',
        'pause_on_hover' => 'boolean',
        'is_active' => 'boolean',
        'height' => 'integer',
        'auto_play_interval' => 'integer',
        'sort_order' => 'integer',
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

    public function slides(): HasMany
    {
        return $this->hasMany(SliderSlide::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}