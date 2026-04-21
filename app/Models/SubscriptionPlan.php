<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'features',
        'icon',
        'thumbnail',
        'monthly_price',
        'yearly_price',
        'lifetime_price',
        'discount_percentage',
        'courses_limit',
        'ebooks_limit',
        'bootcamps_limit',
        'storage_limit',
        'tutors_limit',
        'allow_download',
        'allow_certificate',
        'allow_live_classes',
        'allow_consultation',
        'allow_affiliate',
        'allow_custom_domain',
        'allow_priority_support',
        'allow_api_access',
        'sort_order',
        'is_featured',
        'is_recommended',
        'is_active',
        'is_free',
        'show_on_pricing',
        'subscribers_count',
        'metadata',
    ];

    protected $casts = [
        'features' => 'array',
        'allow_download' => 'boolean',
        'allow_certificate' => 'boolean',
        'allow_live_classes' => 'boolean',
        'allow_consultation' => 'boolean',
        'allow_affiliate' => 'boolean',
        'allow_custom_domain' => 'boolean',
        'allow_priority_support' => 'boolean',
        'allow_api_access' => 'boolean',
        'is_featured' => 'boolean',
        'is_recommended' => 'boolean',
        'is_active' => 'boolean',
        'is_free' => 'boolean',
        'show_on_pricing' => 'boolean',
        'sort_order' => 'integer',
        'subscribers_count' => 'integer',
        'metadata' => 'array',
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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(SubscriptionFeature::class, 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }
}