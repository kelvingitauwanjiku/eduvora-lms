<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ChatChannelMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chat_channel_members';

    protected $fillable = [
        'uuid',
        'chat_channel_id',
        'user_id',
        'role_id',
        'role',
        'can_post',
        'can_reply',
        'can_share',
        'can_manage',
        'is_muted',
        'muted_until',
        'last_read_at',
        'unread_count',
    ];

    protected $casts = [
        'can_post' => 'boolean',
        'can_reply' => 'boolean',
        'can_share' => 'boolean',
        'can_manage' => 'boolean',
        'is_muted' => 'boolean',
        'muted_until' => 'datetime',
        'last_read_at' => 'datetime',
        'unread_count' => 'integer',
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

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function scopeMuted($query)
    {
        return $query->where('is_muted', true);
    }

    public function scopeOwners($query)
    {
        return $query->where('role', 'owner');
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['owner', 'admin']);
    }

    public function scopeModerators($query)
    {
        return $query->whereIn('role', ['owner', 'admin', 'moderator']);
    }

    public function getIsMutedAttribute(): bool
    {
        return $this->is_muted && ($this->muted_until === null || $this->muted_until > now());
    }
}