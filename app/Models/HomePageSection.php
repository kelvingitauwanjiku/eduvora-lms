<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HomePageSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'section_type',
        'settings',
        'filters',
        'limit',
        'columns',
        'order_by',
        'order_dir',
        'layout',
        'style',
        'background_color',
        'text_color',
        'show_title',
        'show_subtitle',
        'show_view_all',
        'view_all_url',
        'view_all_text',
        'is_active',
        'is_full_width',
        'is_lazy_load',
        'sort_order',
    ];

    protected $casts = [
        'settings' => 'array',
        'filters' => 'array',
        'limit' => 'integer',
        'columns' => 'integer',
        'show_title' => 'boolean',
        'show_subtitle' => 'boolean',
        'show_view_all' => 'boolean',
        'is_active' => 'boolean',
        'is_full_width' => 'boolean',
        'is_lazy_load' => 'boolean',
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

    public function items(): HasMany
    {
        return $this->hasMany(HomePageSectionItem::class, 'section_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}