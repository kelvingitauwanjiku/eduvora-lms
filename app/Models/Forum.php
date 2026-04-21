<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Forum extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'course_id',
        'user_id',
        'parent_id',
        'title',
        'description',
        'content',
        'attachments',
        'images',
        'likes',
        'likes_count',
        'dislikes_count',
        'views_count',
        'replies_count',
        'shares_count',
        'is_pinned',
        'is_locked',
        'is_solved',
        'is_featured',
        'is_anonymous',
        'is_edited',
        'status',
        'edited_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'images' => 'array',
        'likes' => 'array',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_solved' => 'boolean',
        'is_featured' => 'boolean',
        'is_anonymous' => 'boolean',
        'is_edited' => 'boolean',
        'likes_count' => 'integer',
        'dislikes_count' => 'integer',
        'views_count' => 'integer',
        'replies_count' => 'integer',
        'shares_count' => 'integer',
        'edited_at' => 'datetime',
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

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Forum::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'forum_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeSolved($query)
    {
        return $query->where('is_solved', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}