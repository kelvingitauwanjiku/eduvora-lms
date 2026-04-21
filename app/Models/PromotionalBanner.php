<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PromotionalBanner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'subtitle',
        'image',
        'link',
        'button_text',
        'type',
        'position',
        'background_color',
        'text_color',
        'border_color',
        'is_dismissible',
        'show_close_button',
        'dismiss_duration',
        'is_active',
        'show_on_mobile',
        'show_on_desktop',
        'target',
        'target_pages',
        'starts_at',
        'ends_at',
        'views_count',
        'clicks_count',
        'dismiss_count',
        'sort_order',
    ];

    protected $casts = [
        'target_pages' => 'array',
        'is_dismissible' => 'boolean',
        'show_close_button' => 'boolean',
        'is_active' => 'boolean',
        'show_on_mobile' => 'boolean',
        'show_on_desktop' => 'boolean',
        'views_count' => 'integer',
        'clicks_count' => 'integer',
        'dismiss_count' => 'integer',
        'sort_order' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}