<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'thread_id',
        'user_id',
        'sender_id',
        'receiver_id',
        'reply_to_id',
        'message',
        'attachments',
        'images',
        'type',
        'is_forwarded',
        'is_read',
        'is_deleted',
        'is_edited',
        'is_starred',
        'read_at',
        'read_by',
        'metadata',
    ];

    protected $casts = [
        'attachments' => 'array',
        'images' => 'array',
        'is_forwarded' => 'boolean',
        'is_read' => 'boolean',
        'is_deleted' => 'boolean',
        'is_edited' => 'boolean',
        'is_starred' => 'boolean',
        'read_at' => 'datetime',
        'read_by' => 'array',
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

    public function thread(): BelongsTo
    {
        return $this->belongsTo(MessageThread::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeStarred($query)
    {
        return $query->where('is_starred', true);
    }
}