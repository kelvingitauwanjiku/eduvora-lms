<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SliderSlide extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'slider_slides';

    protected $fillable = [
        'uuid',
        'slider_id',
        'title',
        'subtitle',
        'description',
        'button_text',
        'button_url',
        'button_target',
        'button_color',
        'second_button_text',
        'second_button_url',
        'background_type',
        'background_image',
        'background_video',
        'background_video_poster',
        'background_color',
        'background_gradient_start',
        'background_gradient_end',
        'text_color',
        'text_alignment',
        'content_position',
        'show_overlay',
        'overlay_color',
        'overlay_opacity',
        'badge',
        'badge_color',
        'thumbnail',
        'custom_css',
        'sort_order',
        'is_featured',
        'is_active',
        'starts_at',
        'ends_at',
        'clicks_count',
        'views_count',
    ];

    protected $casts = [
        'custom_css' => 'array',
        'show_overlay' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'clicks_count' => 'integer',
        'views_count' => 'integer',
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

    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}