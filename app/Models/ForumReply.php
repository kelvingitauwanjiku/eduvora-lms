<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ForumReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'forum_replies';

    protected $fillable = [
        'uuid',
        'forum_id',
        'user_id',
        'parent_id',
        'content',
        'attachments',
        'likes',
        'likes_count',
        'dislikes_count',
        'is_accepted',
        'is_pinned',
        'is_anonymous',
        'is_edited',
        'status',
        'edited_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'likes' => 'array',
        'is_accepted' => 'boolean',
        'is_pinned' => 'boolean',
        'is_anonymous' => 'boolean',
        'is_edited' => 'boolean',
        'likes_count' => 'integer',
        'dislikes_count' => 'integer',
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

    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAccepted($query)
    {
        return $query->where('is_accepted', true);
    }
}