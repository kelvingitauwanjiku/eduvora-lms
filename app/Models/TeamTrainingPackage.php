<?php

namespace App\Models;

use App\Models\Course\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TeamTrainingPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'course_id',
        'bundle_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'preview_video',
        'price',
        'price_per_user',
        'course_privacy',
        'allocation',
        'min_allocation',
        'max_allocation',
        'expiry_type',
        'expiry_value',
        'start_date',
        'expiry_date',
        'features',
        'included_courses',
        'user_roles',
        'pricing_type',
        'tiered_pricing',
        'allow_upgrade',
        'upgrade_price',
        'is_active',
        'is_featured',
        'purchases_count',
        'total_revenue',
    ];

    protected $casts = [
        'allocation' => 'integer',
        'min_allocation' => 'integer',
        'max_allocation' => 'integer',
        'expiry_value' => 'integer',
        'features' => 'array',
        'included_courses' => 'array',
        'user_roles' => 'array',
        'tiered_pricing' => 'array',
        'allow_upgrade' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'purchases_count' => 'integer',
        'total_revenue' => 'decimal:2',
        'price' => 'decimal:2',
        'price_per_user' => 'decimal:2',
        'upgrade_price' => 'decimal:2',
        'start_date' => 'datetime',
        'expiry_date' => 'datetime',
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

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(TeamPackagePurchase::class, 'package_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamPackageMember::class, 'package_purchase_id');
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