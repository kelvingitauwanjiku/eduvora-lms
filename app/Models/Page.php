<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'content',
        'template',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_tags',
        'featured_image',
        'is_featured',
        'show_in_footer',
        'show_in_header',
        'show_in_sitemap',
        'require_authentication',
        'is_published',
        'views_count',
        'sort_order',
        'settings',
        'metadata',
    ];

    protected $casts = [
        'og_tags' => 'array',
        'settings' => 'array',
        'metadata' => 'array',
        'is_featured' => 'boolean',
        'show_in_footer' => 'boolean',
        'show_in_header' => 'boolean',
        'show_in_sitemap' => 'boolean',
        'require_authentication' => 'boolean',
        'is_published' => 'boolean',
        'views_count' => 'integer',
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function seoField()
    {
        return $this->hasOne(SeoField::class)->where('page_id', $this->id);
    }
}
