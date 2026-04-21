<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TutorSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'category_id',
        'name',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
        'tutors_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'tutors_count' => 'integer',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(TutorCategory::class, 'category_id');
    }

    public function tutors(): HasMany
    {
        return $this->hasMany(Tutor::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}