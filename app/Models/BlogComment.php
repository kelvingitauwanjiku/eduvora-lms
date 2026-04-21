<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'blog_id',
        'user_id',
        'parent_id',
        'replied_to_id',
        'comment',
        'attachments',
        'likes',
        'likes_count',
        'dislikes_count',
        'is_approved',
        'is_pinned',
        'is_reported',
        'status',
        'published_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_pinned' => 'boolean',
        'is_reported' => 'boolean',
        'attachments' => 'array',
        'likes' => 'array',
        'published_at' => 'datetime',
    ];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'parent_id');
    }
}
