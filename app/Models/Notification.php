<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'type',
        'title',
        'body',
        'icon',
        'image',
        'action_url',
        'action_text',
        'data',
        'channel',
        'is_read',
        'read_at',
        'is_clicked',
        'clicked_at',
        'is_broadcast',
        'metadata',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_clicked' => 'boolean',
        'is_broadcast' => 'boolean',
        'data' => 'array',
        'metadata' => 'array',
        'read_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsClicked(): void
    {
        if (! $this->is_clicked) {
            $this->update([
                'is_clicked' => true,
                'clicked_at' => now(),
            ]);
        }
    }
}
