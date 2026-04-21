<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HomePageSectionItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'home_page_section_items';

    protected $fillable = [
        'uuid',
        'section_id',
        'item_type',
        'item_id',
        'title',
        'subtitle',
        'description',
        'image',
        'link',
        'link_text',
        'icon',
        'badge',
        'badge_color',
        'price',
        'rating',
        'button_text',
        'button_color',
        'target',
        'metadata',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'metadata' => 'array',
        'price' => 'decimal:2',
        'sort_order' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
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

    public function section(): BelongsTo
    {
        return $this->belongsTo(HomePageSection::class, 'section_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}