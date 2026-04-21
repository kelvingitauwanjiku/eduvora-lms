<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Ebook extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'author_name',
        'publisher',
        'isbn',
        'edition',
        'pages',
        'language',
        'published_date',
        'is_paid',
        'price',
        'discount_price',
        'discount_enabled',
        'is_featured',
        'thumbnail',
        'banner',
        'preview_file',
        'preview_pages',
        'full_file',
        'file_type',
        'file_size',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'average_rating',
        'ratings_count',
        'reviews_count',
        'downloads_count',
        'reads_count',
        'wishlists_count',
        'total_revenue',
        'tags',
        'includes',
        'metadata',
        'published_at',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'discount_enabled' => 'boolean',
        'is_featured' => 'boolean',
        'edition' => 'integer',
        'pages' => 'integer',
        'file_size' => 'integer',
        'average_rating' => 'decimal:2',
        'ratings_count' => 'integer',
        'reviews_count' => 'integer',
        'downloads_count' => 'integer',
        'reads_count' => 'integer',
        'wishlists_count' => 'integer',
        'total_revenue' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'price' => 'decimal:2',
        'tags' => 'array',
        'includes' => 'array',
        'metadata' => 'array',
        'published_at' => 'datetime',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EbookCategory::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(EbookPurchase::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(EbookReview::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['approved', 'published']);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function getCurrentPriceAttribute(): float
    {
        if (! $this->is_paid) {
            return 0;
        }

        if ($this->discount_enabled && $this->discount_price) {
            return (float) $this->discount_price;
        }

        return (float) $this->price;
    }

    public function getIsOnDiscountAttribute(): bool
    {
        return $this->discount_enabled && $this->discount_price;
    }
}