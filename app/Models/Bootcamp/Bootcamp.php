<?php

namespace App\Models\Bootcamp;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bootcamp extends Model
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
        'thumbnail',
        'banner',
        'video_preview',
        'is_paid',
        'price',
        'discount_price',
        'discount_enabled',
        'discount_start',
        'discount_end',
        'is_featured',
        'level',
        'status',
        'start_date',
        'end_date',
        'max_students',
        'enrolled_count',
        'modules_count',
        'duration_days',
        'daily_hours',
        'total_hours',
        'faqs',
        'requirements',
        'outcomes',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'average_rating',
        'reviews_count',
        'total_revenue',
        'includes',
        'metadata',
        'published_at',
    ];

    protected $casts = [
        'includes' => 'array',
        'metadata' => 'array',
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_at' => 'datetime',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'is_paid' => 'boolean',
        'discount_enabled' => 'boolean',
        'is_featured' => 'boolean',
        'enrolled_count' => 'integer',
        'modules_count' => 'integer',
        'duration_days' => 'integer',
        'daily_hours' => 'integer',
        'total_hours' => 'integer',
        'max_students' => 'integer',
        'reviews_count' => 'integer',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function modules()
    {
        return $this->hasMany(BootcampModule::class)->orderBy('sort_order');
    }

    public function liveClasses()
    {
        return $this->hasMany(BootcampLiveClass::class);
    }

    public function resources()
    {
        return $this->hasMany(BootcampResource::class);
    }

    public function purchases()
    {
        return $this->hasMany(BootcampPurchase::class);
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
