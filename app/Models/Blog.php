<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'thumbnail',
        'banner',
        'video_url',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_tags',
        'structured_data',
        'is_featured',
        'is_popular',
        'is_trending',
        'allow_comments',
        'is_pinned',
        'need_authentication',
        'status',
        'published_at',
        'scheduled_at',
        'view_count',
        'shares_count',
        'comments_count',
        'likes_count',
        'bookmarks_count',
        'reading_time',
        'tags',
        'related_blogs',
        'metadata',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_trending' => 'boolean',
        'allow_comments' => 'boolean',
        'is_pinned' => 'boolean',
        'need_authentication' => 'boolean',
        'view_count' => 'integer',
        'shares_count' => 'integer',
        'comments_count' => 'integer',
        'likes_count' => 'integer',
        'bookmarks_count' => 'integer',
        'tags' => 'array',
        'related_blogs' => 'array',
        'og_tags' => 'array',
        'structured_data' => 'array',
        'metadata' => 'array',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(BlogLike::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(BlogBookmark::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
