<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'parent_id',
        'icon_id',
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'sort_order',
        'is_featured',
        'is_active',
        'show_in_menu',
        'show_in_home',
        'courses_count',
        'metadata',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean',
        'show_in_home' => 'boolean',
        'courses_count' => 'integer',
        'metadata' => 'array',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(\App\Models\Course\Course::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'category_user');
    }

    public function icon(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'icon_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getBreadcrumbAttribute(): string
    {
        $breadcrumb = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($breadcrumb, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $breadcrumb);
    }

    public function getAllChildrenIdsAttribute(): array
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->all_children_ids);
        }

        return $ids;
    }
}
