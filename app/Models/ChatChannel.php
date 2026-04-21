<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ChatChannel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'type',
        'icon',
        'workspace_id',
        'user_id',
        'topic',
        'position',
        'is_private',
        'is_read_only',
        'allow_massages',
        'allow_threads',
        'allow_files',
        'allow_links',
        'allow_images',
        'allow_emoji',
        'allow_mentions',
        'is_archived',
        'members_count',
        'messages_count',
        'threads_count',
        'files_count',
        'last_message_at',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'is_read_only' => 'boolean',
        'allow_massages' => 'boolean',
        'allow_threads' => 'boolean',
        'allow_files' => 'boolean',
        'allow_links' => 'boolean',
        'allow_images' => 'boolean',
        'allow_emoji' => 'boolean',
        'allow_mentions' => 'boolean',
        'is_archived' => 'boolean',
        'position' => 'integer',
        'members_count' => 'integer',
        'messages_count' => 'integer',
        'threads_count' => 'integer',
        'files_count' => 'integer',
        'last_message_at' => 'datetime',
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

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ChatChannelMember::class, 'chat_channel_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'chat_channel_id');
    }

    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeText($query)
    {
        return $query->where('type', 'text');
    }

    public function scopeVoice($query)
    {
        return $query->where('type', 'voice');
    }
}