<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EbookReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ebook_reviews';

    protected $fillable = [
        'uuid',
        'user_id',
        'ebook_id',
        'rating',
        'review',
        'is_published',
        'is_verified_purchase',
        'helpful_count',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_published' => 'boolean',
        'is_verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
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

    public function ebook(): BelongsTo
    {
        return $this->belongsTo(Ebook::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('status', 'approved');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}