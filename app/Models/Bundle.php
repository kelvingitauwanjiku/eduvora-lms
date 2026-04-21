<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bundle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'title',
        'slug',
        'short_description',
        'description',
        'thumbnail',
        'banner',
        'is_paid',
        'price',
        'discount_price',
        'discount_enabled',
        'is_featured',
        'status',
        'courses_count',
        'enrollments_count',
        'average_rating',
        'reviews_count',
        'total_revenue',
        'published_at',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'discount_enabled' => 'boolean',
        'is_featured' => 'boolean',
        'courses_count' => 'integer',
        'enrollments_count' => 'integer',
        'average_rating' => 'decimal:2',
        'reviews_count' => 'integer',
        'total_revenue' => 'decimal:2',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
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

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'bundle_courses')
            ->withPivot(['sort_order', 'is_required'])
            ->withTimestamps();
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(BundlePurchase::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
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
}