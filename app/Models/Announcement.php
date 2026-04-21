<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'content',
        'link',
        'link_text',
        'image',
        'type',
        'target',
        'target_users',
        'target_courses',
        'is_pinned',
        'is_dismissible',
        'show_on_dashboard',
        'show_on_course',
        'is_published',
        'starts_at',
        'ends_at',
        'views_count',
        'clicks_count',
    ];

    protected $casts = [
        'target_users' => 'array',
        'target_courses' => 'array',
        'is_pinned' => 'boolean',
        'is_dismissible' => 'boolean',
        'show_on_dashboard' => 'boolean',
        'show_on_course' => 'boolean',
        'is_published' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'views_count' => 'integer',
        'clicks_count' => 'integer',
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

    public function scopeActive($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForTarget($query, string $target)
    {
        return $query->where('target', $target)->orWhere('target', 'all');
    }

    public function scopeCurrent($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('starts_at')
                ->orWhere('starts_at', '<=', now());
        })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }
}
