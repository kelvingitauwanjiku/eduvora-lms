<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chat_messages';

    protected $fillable = [
        'uuid',
        'chat_channel_id',
        'user_id',
        'parent_id',
        'thread_id',
        'reply_to_id',
        'content',
        'type',
        'attachments',
        'embeds',
        'mentions',
        'reactions',
        'reactions_count',
        'threads_count',
        'replies_count',
        'is_pinned',
        'is_slowmode',
        'is_edited',
        'is_deleted',
        'is_flagged',
        'edited_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'embeds' => 'array',
        'mentions' => 'array',
        'reactions' => 'array',
        'reactions_count' => 'integer',
        'threads_count' => 'integer',
        'replies_count' => 'integer',
        'is_pinned' => 'boolean',
        'is_slowmode' => 'boolean',
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'is_flagged' => 'boolean',
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

    public function chatChannel(): BelongsTo
    {
        return $this->belongsTo(ChatChannel::class, 'chat_channel_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'parent_id');
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'thread_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id');
    }

    public function scopeThreads($query)
    {
        return $query->whereNotNull('thread_id');
    }

    public function scopeParentMessages($query)
    {
        return $query->whereNull('thread_id')->whereNull('parent_id');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeflagged($query)
    {
        return $query->where('is_flagged', true);
    }
}