<?php

namespace App\Models\Course;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Review;
use App\Models\SeoField;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'thumbnail',
        'banner',
        'preview_video',
        'video_preview_type',
        'course_type',
        'status',
        'level',
        'language',
        'is_paid',
        'price',
        'discount_price',
        'discount_enabled',
        'discount_start',
        'discount_end',
        'is_free',
        'is_featured',
        'is_bestseller',
        'is_new',
        'is_popular',
        'is_trending',
        'enable_drip_content',
        'drip_content_settings',
        'enable_certificate',
        'certificate_title',
        'duration',
        'duration_text',
        'sections_count',
        'lessons_count',
        'quizzes_count',
        'assignments_count',
        'enrollments_count',
        'completion_rate',
        'average_rating',
        'ratings_count',
        'reviews_count',
        'views_count',
        'wishlists_count',
        'total_revenue',
        'instructor_revenue',
        'admin_revenue',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'requirements',
        'outcomes',
        'faqs',
        'includes',
        'metadata',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'is_promoted',
        'published_at',
        'scheduled_publish_at',
        'expiry_period',
        'max_access_days',
        'lifetime_access',
        'allow_download',
        'allow_guest_access',
        'require_sequential_progress',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_free' => 'boolean',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_new' => 'boolean',
        'is_popular' => 'boolean',
        'is_trending' => 'boolean',
        'enable_drip_content' => 'boolean',
        'enable_certificate' => 'boolean',
        'is_promoted' => 'boolean',
        'lifetime_access' => 'boolean',
        'allow_download' => 'boolean',
        'allow_guest_access' => 'boolean',
        'require_sequential_progress' => 'boolean',
        'discount_enabled' => 'boolean',
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
        'discount_price' => 'decimal:2',
        'price' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'instructor_revenue' => 'decimal:2',
        'admin_revenue' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'completion_rate' => 'decimal:2',
        'requirements' => 'array',
        'outcomes' => 'array',
        'faqs' => 'array',
        'includes' => 'array',
        'drip_content_settings' => 'array',
        'metadata' => 'array',
        'published_at' => 'datetime',
        'scheduled_publish_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_instructor')
            ->withPivot(['role', 'revenue_share', 'can_edit', 'can_delete', 'is_accepted'])
            ->withTimestamps();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('sort_order');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'course_tag');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function seo(): HasOne
    {
        return $this->hasOne(SeoField::class, 'course_id');
    }

    public function approvalRequest(): HasOne
    {
        return $this->hasOne(CourseApprovalRequest::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['published', 'approved']);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true)->where('is_free', false);
    }

    public function getCurrentPriceAttribute(): float
    {
        if ($this->is_free) {
            return 0;
        }

        if ($this->discount_enabled && $this->discount_price) {
            if ($this->discount_start && $this->discount_end) {
                $now = now();
                if ($now->between($this->discount_start, $this->discount_end)) {
                    return (float) $this->discount_price;
                }
            }
        }

        return (float) $this->price;
    }

    public function getIsOnDiscountAttribute(): bool
    {
        if (! $this->discount_enabled || ! $this->discount_price) {
            return false;
        }

        if ($this->discount_start && $this->discount_end) {
            return now()->between($this->discount_start, $this->discount_end);
        }

        return true;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (! $this->is_on_discount || ! $this->price) {
            return 0;
        }

        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function getTotalDurationAttribute(): int
    {
        return (int) ($this->lessons()->sum('duration') ?: 0);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? asset("storage/{$this->thumbnail}") : null;
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? asset("storage/{$this->banner}") : null;
    }
}
